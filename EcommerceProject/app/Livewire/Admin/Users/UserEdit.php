<?php

namespace App\Livewire\Admin\Users;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserEdit extends Component
{
    use WithFileUploads;
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
    public $username = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    public $birthday = null;
    public $role = 'user';
    public $avatar = null;
    #[Locked]
    public $oldAvatar = null;

    protected UserRepositoryInterface $repository;
    protected $request = UserRequest::class;

    public function rules(){
        return $this->baseRequestRules(isEdit: true, recordId: $this->id, isUploadedFile: $this->avatar instanceof UploadedFile);
    }

    public function mount(int $user){
        $user = $this->repository->find(idOrCriteria: $user, throwNotFound: true);
        $this->fill($user->only([
            'id',
            'username',
            'email',
            'first_name',
            'last_name',
            'avatar',
        ]) + [
            'birthday' => $user->birthday?->format('Y-m-d'),
            'oldAvatar' => $user->avatar,
            'role' => $user->role->value
        ]);
    }

    public function boot(UserRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'username',
                'email',
                'first_name',
                'last_name',
                'birthday',
                'role'
            ]) + [
                'avatar' => $this->avatar instanceof UploadedFile
                    ? updateImage($this->avatar, $this->oldAvatar, 'avatars')
                    : (is_null($this->avatar) ? null : $this->oldAvatar)
            ]
        );

        return redirect()->route('admin.users.index')->with('data-changed', ['User account has been updated successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('username', 'email', 'first_name', 'last_name', 'birthday', 'avatar', 'oldAvatar', 'role');
        $this->mount($this->id);
    }

    #[Title('Edit User - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.users.user-edit');
    }
}
