@use('App\Enums\UserRole')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-admin.management-header title="User List" add-new-url="{{ route('admin.users.create') }}" add-label="Add New User" />

    <div class="row mb-2">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary bootstrap text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap">Total Users</h6>
                            <h3 class="card-title mb-0 bootstrap">1,234</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success bootstrap text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap">Active Users</h6>
                            <h3 class="card-title mb-0 bootstrap">987</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning bootstrap text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap">Pending</h6>
                            <h3 class="card-title mb-0 bootstrap">45</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-danger bootstrap text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 bootstrap">Inactive</h6>
                            <h3 class="card-title mb-0 bootstrap">202</h3>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-admin.filter-bar placeholderSearch="Search users..." modelSearch="search" resetAction="resetFilters">
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
    </x-admin.filter-bar>

    <x-admin.data-table caption="User Records">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-danger bootstrap" style="padding: 0.4rem 1.25rem;"
                wire:click="softDeleteUser" title="Remove User" x-show="$wire.selectedUserIds.length" x-transition>
                <i class="fas fa-list-alt me-1"></i>
                Remove User
            </button>
            <button type="button" class="btn btn-outline-primary bootstrap" style="padding: 0.4rem 1.25rem;" title="View Deleted Users">
                <i class="fas fas fa-trash-restore me-1"></i>
                Deleted Users
            </button>
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
                                    <img src="{{ asset('storage/' . ($user->avatar ?? '404.webp')) }}"
                                        class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                    <div class="text-start">
                                        <div class="fw-bold">{{ Str::limit(trim("{$user->first_name} {$user->last_name}"), 20, '...') }}</div>
                                        <small class="text-muted">ID: #{{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ Str::limit($user->email, 20, '...') }}
                                @if($user->email_verified_at)
                                    <small class="text-success bootstrap d-block text-center mt-1">
                                        <i class="fas fa-check-circle me-1"></i>Email verified
                                    </small>
                                @else
                                    <small class="text-danger bootstrap d-block text-center mt-1">
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
                                        @case(UserRole::ADMIN->value) bg-primary @break
                                        @case(UserRole::USER->value) bg-secondary @break
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
                                    <button class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete" wire:click="softDeleteUser({{ $user->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
    </x-admin.data-table>
</div>
@script
<script>
    window.toggleSelectAll = function(checkboxElement){
        const reverseState = !Boolean(+checkboxElement.dataset.state);
        const dataIds = Array.from(document.querySelectorAll('.user-checkbox'), (checkbox) => {
            checkbox.checked = reverseState;
            return reverseState && checkbox.value;
        });

        checkboxElement.dataset.state = checkboxElement.checked = +reverseState;
        $wire.selectedUserIds = reverseState ? dataIds : [];
    }

    window.updateSelectAllState = function(){
        const toggleAllElement = document.getElementById('toggleAll');
        const stateNew = Array.from(document.querySelectorAll('.user-checkbox')).every(checkbox => checkbox.checked);

        toggleAllElement.dataset.state = toggleAllElement.checked = +stateNew;
    }
</script>
@endscript
