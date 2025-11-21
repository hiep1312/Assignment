<?php

namespace App\Livewire\Admin\Mails;

use App\Helpers\AutoValidatesRequest;
use App\Helpers\MailTemplateHelper;
use App\Http\Requests\Admin\MailRequest;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\MailRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class MailEdit extends Component
{
    use AutoValidatesRequest;

    public $id;
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

    public function mount(int $mail){
        $mail = $this->repository->find(idOrCriteria: $mail, throwNotFound: true);

        $this->fill(
            $mail->only([
                'id',
                'subject',
                'type',
            ]) + [
                'body' => preg_replace("/(<img[^>]+style=[\"\'])[^\"\']*(aspect-ratio\s*:\s*[0-9\/]+)[^\"\']*([\"\'][^>]*\/?>)/i", "$1$2$3", $mail->body),
            ]
        );
    }

    public function updatedBody(){
        $this->body = preg_replace('/\{\{(?:\s|&nbsp;)*([^\}\s]*)(?:\s|&nbsp;)*\}\}/', '{{${1}}}', $this->body);
        $this->dispatch('editor.update', $this->body);
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'subject',
                'type',
            ]) + [
                'body' => MailTemplateHelper::applyInlineCss($this->body),
                'variable' => MailTemplateHelper::getUsedPlaceholders($this->body, $this->type),
            ]
        );

        return redirect()->route('admin.mails.index')->with('data-changed', ['Mail has been updated successfully.', now()->toISOString()]);
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
        $this->mount($this->id);
        $this->dispatch('editor.update', $this->body);
    }

    #[Title('Edit Mail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.mails.mail-edit');
    }
}
