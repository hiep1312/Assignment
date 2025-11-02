<?php

namespace App\Livewire\Admin\Notifications;

use App\Helpers\AutoValidatesRequest;
use App\Helpers\NotificationTemplateHelper;
use App\Http\Requests\NotificationRequest;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class NotificationEdit extends Component
{
    use AutoValidatesRequest;

    public $id;
    public string $title = '';
    public string $message = '';
    public int $type = 0;

    protected NotificationRepositoryInterface $repository;
    protected ImageRepositoryInterface $imageRepository;
    protected $request = NotificationRequest::class;

    public function boot(NotificationRepositoryInterface $repository, ImageRepositoryInterface $imageRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
    }

    public function mount(int $notification){
        $notification = $this->repository->find(idOrCriteria: $notification, throwNotFound: true);

        $this->fill($notification->only([
            'id',
            'title',
            'message',
            'type',
        ]));
    }

    public function updatedMessage(){
        $this->message = preg_replace('/\{\{(?:\s|&nbsp;)*([^\}\s]*)(?:\s|&nbsp;)*\}\}/', '{{${1}}}', $this->message);
        $this->dispatch('editor.update', $this->message);
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            $this->id,
            $this->only([
                'title',
                'message',
                'type',
            ]) + [
                'variable' => NotificationTemplateHelper::getUsedPlaceholders($this->message, $this->type),
            ]
        );

        return redirect()->route('admin.notifications.index')->with('data-changed', ['Notification has been updated successfully.', now()->toISOString()]);
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $imageUrl = $this->imageRepository->find(idOrCriteria: function($query) use ($imageIds){
            $query->whereIn('id', $imageIds);
        }, columns: ['image_url'])->map(fn($image) => asset("storage/{$image->image_url}"))->toArray();

        if($imageUrl) $this->js("window.editorAPI.insertImage", $imageUrl);
    }

    public function resetForm(){
        $this->reset('title', 'message', 'type');
        $this->mount($this->id);
        $this->dispatch('editor.update', $this->message);
    }

    #[Title('Edit Mail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.notifications.notification-edit');
    }
}
