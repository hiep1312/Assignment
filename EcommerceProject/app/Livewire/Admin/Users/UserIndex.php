<?php

namespace App\Livewire\Admin\Users;

use App\Repositories\UserRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    protected UserRepository $repository;

    public string $search = '';
    public string $role = '';
    public ?int $emailVerified = null;
    public array $selectedUserIds = [];

    public function boot(UserRepository $repository){
        $this->repository = $repository;
    }

    public function resetFilters(){
        $this->reset('search', 'role', 'emailVerified');
        $this->resetPage();
    }

    public function softDeleteUser(?int $id = null){

    }

    public function forceDeleteUser(){

    }

    #[Title('User List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $users = $this->repository->getAll(filters: function(&$query) {
            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('email', '%'. trim($this->search) .'%')
                        ->orWhereLike('name', "%{$this->search}%");
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
        }, perPage: 10, columns: ['*'], pageName: 'page');

        return view('admin.pages.users.user-index', compact('users'));
    }
}
