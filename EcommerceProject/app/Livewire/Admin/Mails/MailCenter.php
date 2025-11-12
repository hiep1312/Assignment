<?php

namespace App\Livewire\Admin\Mails;

use App\Events\MailSentEvent;
use App\Helpers\AutoValidatesRequest;
use App\Helpers\MailTemplateHelper;
use App\Http\Requests\Admin\MailCenterRequest;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\MailRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class MailCenter extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public string $sendType = 'template';
    public ?int $selectedTemplate = null;
    public $subject = null;
    public $body = '';

    public string $searchUsers = '';
    public array $selectedUsers = [];

    protected MailRepositoryInterface $repository;
    protected ImageRepositoryInterface $imageRepository;
    protected UserRepositoryInterface $userRepository;
    protected $request = MailCenterRequest::class;

    public function rules(){
        return $this->baseRequestRules($this->sendType);
    }

    public function boot(MailRepositoryInterface $repository, ImageRepositoryInterface $imageRepository, UserRepositoryInterface $userRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
    }

    public function updatedBody(){
        $this->body = preg_replace('/\{\{(?:\s|&nbsp;)*([^\}\s]*)(?:\s|&nbsp;)*\}\}/', '{{${1}}}', $this->body);
        $this->dispatch('editor.update', $this->body);
    }

    public function submitEmail(){
        $this->validate();

        $emailTemplate = ($this->sendType === 'template'
            ? $this->repository->find($this->selectedTemplate)
            : $this->createTemplate());
        $recipients = $this->userRepository->find(idOrCriteria: function($query){
            $query->whereIn('id', $this->selectedUsers);
        })->all();

        event(new MailSentEvent($emailTemplate, $recipients, $recipients));

        session()->flash('mail.queued', ["The email has been queued for sending. Delivery will occur shortly.", now()->toISOString()]);
        $this->resetForm();
    }

    protected function createTemplate(){
        $mailCreated = $this->repository->create([
            'subject' => $this->subject,
            'body' => MailTemplateHelper::applyInlineCss($this->body),
            'variable' => MailTemplateHelper::getUsedPlaceholders($this->body, 0),
            'type' => 0,
        ]);

        return $mailCreated;
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $imageUrl = $this->imageRepository->find(idOrCriteria: function($query) use ($imageIds){
            $query->whereIn('id', $imageIds);
        }, columns: ['image_url'])->map(fn($image) => asset("storage/{$image->image_url}"))->toArray();

        if($imageUrl) $this->js("window.editorAPI.insertImage", $imageUrl);
    }

    public function resetForm(){
        $this->reset('sendType', 'selectedTemplate', 'subject', 'body', 'searchUsers', 'selectedUsers');
    }

    #[Title('Mail Center - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $templates = $this->repository->getAll(
            criteria: fn(&$query) =>  $query->where('type', 0),
            perPage: false,
            columns: ['id', 'subject']
        );

        $users = $this->userRepository->getAll(criteria: function(&$query){
            $query->when($this->searchUsers, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('email', '%'. trim($this->searchUsers) .'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'. trim($this->searchUsers) .'%']);
                });
            });

            $query->latest('role');
        }, perPage: false, columns: ['id', 'first_name', 'last_name', 'email', 'avatar']);

        return view('admin.pages.mails.mail-center', compact('templates', 'users'));
    }
}
