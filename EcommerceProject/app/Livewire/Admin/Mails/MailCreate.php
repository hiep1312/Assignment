<?php

namespace App\Livewire\Admin\Mails;

use App\Helpers\AutoValidatesRequest;
use App\Helpers\MailTemplateHelper;
use App\Http\Requests\MailRequest;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\MailRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class MailCreate extends Component
{
    use AutoValidatesRequest;

    public $subject = null;
    public $body = '';
    public $type = 0;

    protected MailRepositoryInterface $repository;
    protected ImageRepositoryInterface $imageRepository;
    protected $request = MailRequest::class;

    public function boot(MailRepositoryInterface $repository, ImageRepositoryInterface $imageRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
    }

    public function updatedBody(){
        $this->body = preg_replace('/\{\{(?:\s|&nbsp;)*([^\}\s]*)(?:\s|&nbsp;)*\}\}/', '{{${1}}}', $this->body);
        $this->dispatch('editor.update', $this->body);
    }

    public function store(){
        $this->validate();

        $this->repository->create(
            $this->only([
                'subject',
                'type',
            ]) + [
                'body' => MailTemplateHelper::applyInlineCss($this->body),
                'variable' => MailTemplateHelper::getUsedPlaceholders($this->body, $this->type),
            ]
        );

        return redirect()->route('admin.mails.index')->with('data-changed', ['New mail template has been created successfully.', now()->toISOString()]);
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $imageUrl = $this->imageRepository->find(idOrCriteria: function($query) use ($imageIds){
            $query->whereIn('id', $imageIds);
        }, columns: ['image_url'])->map(fn($image) => asset("storage/{$image->image_url}"))->toArray();

        if($imageUrl) $this->js("window.editorAPI.insertImage", $imageUrl);
    }

    public function resetForm(){
        $this->reset('subject', 'body', 'type');
    }

    #[Title('Add New Mail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.mails.mail-create');
    }
}
