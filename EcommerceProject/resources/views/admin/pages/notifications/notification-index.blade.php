<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Notification List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Notification Template List" btn-link="{{ route('admin.notifications.create') }}" btn-label="Add New Notification" btn-icon="fas fa-bell" />

    <x-livewire::filter-bar placeholderSearch="Search notifications..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="type">
                <option value="">All Types</option>
                <option value="0">Custom</option>
                <option value="1">Order Update</option>
                <option value="2">Payment Update</option>
                <option value="3">Promotion</option>
                <option value="4">Account Update</option>
                <option value="5">Maintenance</option>
                <option value="6">Internal System</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Notification Records">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Delete Notifications"
                x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                data-title="Delete Notifications" data-type="warning" x-bind:data-message="`Are you sure you want to delete these ${$wire.selectedRecordIds.length} notifications? This action cannot be undone.`"
                data-confirm-label="Confirm Delete" data-event-name="notification.deleted" wire:key="delete">
                <i class="fas fa-times-circle me-1"></i>
                Delete Notifications
            </button>
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr class="text-center" wire:key="notification-{{ $notification->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $notification->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="text-start">
                                    <div class="@if($notification->title) fw-bold @else text-muted fst-italic @endif">
                                        {{ Str::limit($notification->title, 30, '...') }}
                                    </div>
                                    <small class="text-muted">ID: #{{ $notification->id }}</small>
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 330px;">
                                    <small class="text-muted text-wrap lh-base">
                                        {{ Str::limit(strip_tags($notification->message), 100, '...') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill
                                    @switch($notification->type)
                                        @case(0) bg-secondary @break
                                        @case(1) bg-success bootstrap-color @break
                                        @case(2) bg-warning @break
                                        @case(3) bg-info @break
                                        @case(4) bg-primary @break
                                        @case(5) bg-dark @break
                                        @case(6) bg-danger @break
                                    @endswitch
                                ">
                                    @switch($notification->type)
                                        @case(0) Custom @break
                                        @case(1) Order Update @break
                                        @case(2) Payment Update @break
                                        @case(3) Promotion @break
                                        @case(4) Account Update @break
                                        @case(5) Maintenance @break
                                        @case(6) Internal System @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span>{{ $notification->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $notification->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                        data-title="Delete Notification" data-type="warning" data-message="Are you sure you want to delete this notification #{{ $notification->id }}? This action cannot be undone."
                                        data-confirm-label="Confirm Delete" data-event-name="notification.deleted" data-event-data="{{ $notification->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No notification templates found</h5>
                                    <p class="text-muted">There are no notification templates or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($notifications->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $notifications->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
