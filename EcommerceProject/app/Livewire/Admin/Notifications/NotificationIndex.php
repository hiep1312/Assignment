<?php

namespace App\Livewire\Admin\Notifications;

use App\Repositories\Contracts\NotificationRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationIndex extends Component
{
    use WithPagination;

    protected NotificationRepositoryInterface $repository;

    public string $search = '';
    public ?int $type = null;
    public array $selectedRecordIds = [];

    public function boot(NotificationRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function resetFilters(){
        $this->reset('search', 'type');
        $this->resetPage();
    }

    #[On('notification.deleted')]
    public function delete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[Title('Notification Template List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $notifications = $this->repository->getAll(criteria: function(&$query) {
            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('title', '%'. trim($this->search) .'%')
                        ->orWhereLike('message', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->type !== null,
                fn($innerQuery) => $innerQuery->where('type', $this->type)
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.notifications.notification-index', compact('notifications'));
    }
}
