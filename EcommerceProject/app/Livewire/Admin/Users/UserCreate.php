<?php

namespace App\Livewire\Admin\Users;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserCreate extends Component
{
    use WithFileUploads, AutoValidatesRequest;

    public $username = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $first_name = '';
    public $last_name = '';
    public $birthday = null;
    public $avatar = null;
    public $role = 'user';

    protected UserRepositoryInterface $repository;
    protected $request = UserRequest::class;

    public function boot(UserRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function store(){
        $this->validate();

        $this->repository->create(
            $this->only([
                'username',
                'email',
                'password',
                'first_name',
                'last_name',
                'birthday',
                'role',
            ]) + [
                'avatar' => storeImage($this->avatar, 'avatars'),
                'email_verified_at' => now()
            ]
        );

        return redirect()->route('admin.users.index')->with('data-changed', ['New user account has been created successfully.', now()]);
    }

    public function resetForm(){
        $this->reset('username', 'email', 'password', 'password_confirmation', 'first_name', 'last_name', 'birthday', 'avatar', 'role');
    }

    #[Title('Add New User - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.users.user-create');
    }
}
