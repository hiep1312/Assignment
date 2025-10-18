@use('App\Enums\UserRole')
@use('App\Enums\DefaultImage')
<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update User List" type="primary" time="{{ session('data-changed')[1]?->diffForHumans() }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="User List" btn-link="{{ route('admin.users.create') }}" btn-label="Add New User" />

    <x-livewire::stats-overview :data-stats="$statistic" />

    <x-livewire::filter-bar placeholderSearch="Search users..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="role">
                <option value="">All Roles</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="emailVerified">
                <option value="">All Email Status</option>
                <option value="1">Verified</option>
                <option value="0">Not Verified</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="User Records">
        <x-slot:actions>
            @if($isTrashed)
                <button type="button" class="btn btn-outline-secondary bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedUserIds.length ? `Restore Users` : `Restore All Users`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedUserIds.length ? `Restore Users` : `Restore All Users`" data-type="question"
                    x-bind:data-message="$wire.selectedUserIds.length
                        ? `Are you sure you want to restore these ${$wire.selectedUserIds.length} users? They will be moved back to the active users list.`
                        : `Are you sure you want to restore all users? They will be moved back to the active users list.`
                    "
                    data-confirm-label="Confirm Restore" data-event-name="user.restored" wire:key="restore">
                    <i class="fas fa-history me-1"></i>
                    <span x-text="$wire.selectedUserIds.length ? `Restore Users` : `Restore All Users`"></span>
                </button>
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" :title="$wire.selectedUserIds.length ? `Permanently Delete Users` : `Permanently Delete All Users`"
                    onclick="confirmModalAction(this)" :data-title="$wire.selectedUserIds.length ? `Permanently Delete Users` : `Permanently Delete All Users`" data-type="warning"
                    x-bind:data-message="$wire.selectedUserIds.length
                        ? `Are you sure you want to permanently delete these ${$wire.selectedUserIds.length} users? This action cannot be undone.`
                        : `Are you sure you want to permanently delete all users? This action cannot be undone.`
                    "
                    data-confirm-label="Confirm Delete" data-event-name="user.forceDeleted" wire:key="force-delete">
                    <i class="fas fa-trash-alt me-1"></i>
                    <span x-text="$wire.selectedUserIds.length ? `Permanently Delete Users` : `Permanently Delete All Users`"></span>
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;"
                    title="View Active Users"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-user-check me-1"></i>
                    Active Users
                </button>
            @else
                <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Remove Users"
                    x-show="$wire.selectedUserIds.length" x-transition onclick="confirmModalAction(this)"
                    data-title="Remove Users" data-type="warning" x-bind:data-message="`Are you sure you want to remove these ${$wire.selectedUserIds.length} users? They can be restored later.`"
                    data-confirm-label="Confirm Delete" data-event-name="user.deleted" wire:key="delete">
                    <i class="fas fa-user-times me-1"></i>
                    Remove Users
                </button>
                <button type="button" class="btn btn-outline-primary bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="View Deleted Users"
                    wire:click="$toggle('isTrashed', true)">
                    <i class="fas fa-trash-restore-alt me-1"></i>
                    Deleted Users
                </button>
            @endif
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="text-center" wire:key="user-{{ $user->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input user-checkbox" wire:model="selectedUserIds"
                                    value="{{ $user->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($user->avatar ?? DefaultImage::AVATAR->value)) }}"
                                        class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                    <div class="text-start">
                                        <div class="fw-bold">
                                            {{ Str::limit(trim("{$user->first_name} {$user->last_name}"), 20, '...') }}
                                            @if($isTrashed)
                                                <span class="badge badge-center rounded-pill bg-label-danger ms-1" style="font-size: 0.7rem; vertical-align: middle;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">ID: #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ Str::limit($user->email, 20, '...') }}
                                @if($user->email_verified_at)
                                    <small class="text-success bootstrap-color d-block text-center mt-1">
                                        <i class="fas fa-check-circle me-1"></i>Email verified
                                    </small>
                                @else
                                    <small class="text-danger bootstrap-color d-block text-center mt-1">
                                        <i class="fas fa-times-circle me-1"></i>Not verified
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($user->birthday)
                                    {{ $user->birthday->format('M d, Y') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill
                                    @switch($user->role)
                                        @case(UserRole::ADMIN) bg-primary @break
                                        @case(UserRole::USER) bg-secondary @break
                                    @endswitch
                                ">
                                    {{ $user->role }}
                                </span>

                            </td>
                            <td>
                                <span>{{ $user->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $user->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($isTrashed)
                                        <button class="btn btn-outline-warning btn-action" title="Restore" onclick="confirmModalAction(this)"
                                            data-title="Restore User" data-type="question" data-message="Are you sure you want to restore this user #{{ $user->id }}? The user will be moved back to the active users list."
                                            data-confirm-label="Confirm Restore" data-event-name="user.restored" data-event-data="{{ $user->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" title="Permanently Delete" onclick="confirmModalAction(this)"
                                            data-title="Permanently Delete User" data-type="warning" data-message="Are you sure you want to permanently delete this user #{{ $user->id }}? This action cannot be undone."
                                            data-confirm-label="Confirm Delete" data-event-name="user.forceDeleted" data-event-data="{{ $user->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                            data-title="Remove User" data-type="warning" data-message="Are you sure you want to remove this user #{{ $user->id }}? The user can be restored later."
                                            data-confirm-label="Confirm Delete" data-event-name="user.deleted" data-event-data="{{ $user->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No users found</h5>
                                    <p class="text-muted">There are no users or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($users->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $users->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
