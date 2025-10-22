<?php

namespace App\Livewire\Admin\Users;

use App\Repositories\Contracts\UserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected UserRepositoryInterface $repository;

    public string $search = '';
    public string $role = '';
    public ?int $emailVerified = null;
    public array $selectedRecordIds = [];

    public function boot(UserRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'role', 'emailVerified');
        $this->resetPage();
    }

    #[On('user.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('user.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('user.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('User List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $users = $this->repository->getAll(criteria: function(&$query) {
            if($this->isTrashed) $query->onlyTrashed();

            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('email', '%'. trim($this->search) .'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'. trim($this->search) .'%']);
                });
            })
            ->when(
                $this->role,
                fn($innerQuery) => $innerQuery->where('role', $this->role)
            )
            ->when(
                $this->emailVerified !== null,
                fn($innerQuery) => $innerQuery->where('email_verified_at', $this->emailVerified ? '!=' : '=', null)
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        $statistic = [
            [
                'title' => 'Total Users',
                'value' => $this->repository->count(['withTrashed']),
                'icon' => 'fas fa-users',
            ],
            [
                'title' => 'Active Users',
                'value' => $this->repository->count(),
                'icon' => 'fas fa-user-check',
            ],
            [
                'title' => 'Deleted Users',
                'value' => $this->repository->count(['onlyTrashed']),
                'icon' => 'fas fa-user-slash',
            ],
            [
                'title' => 'Unverified Users',
                'value' => $this->repository->count(fn($query) => $query->whereNull('email_verified_at')),
                'icon' => 'fas fa-user-clock',
            ]
        ];

        return view('admin.pages.users.user-index', compact('users', 'statistic'));
    }
}
