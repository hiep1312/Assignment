<div class="container-xxl flex-grow-1 container-p-y" id="main-component">
    <livewire:admin.components.confirm-modal>

    @if(session()->has('data-changed'))
        <x-livewire::toast-message title="Update Mail List" type="primary" time="{{ session('data-changed')[1] }}" :show="true" :duration="8">
            {{ session('data-changed')[0] }}
        </x-livewire::toast-message>
    @endif

    <x-livewire::management-header title="Mail Template List" btn-link="{{ route('admin.mails.create') }}" btn-label="Add New Mail" btn-icon="fas fa-envelope" />

    <x-livewire::filter-bar placeholderSearch="Search mails..." modelSearch="search" resetAction="resetFilters">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="type">
                <option value="">All Types</option>
                <option value="0">Custom</option>
                <option value="1">Order Success</option>
                <option value="2">Order Failed</option>
                <option value="3">Shipping Update</option>
                <option value="4">Forgot Password</option>
                <option value="5">Register Success</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.change="hasSubject">
                <option value="">All Subject Status</option>
                <option value="1">Has Subject</option>
                <option value="0">No Subject</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Mail Records">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Delete Mails"
                x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                data-title="Delete Mails" data-type="warning" x-bind:data-message="`Are you sure you want to delete these ${$wire.selectedRecordIds.length} mails? This action cannot be undone.`"
                data-confirm-label="Confirm Delete" data-event-name="mail.deleted" wire:key="delete">
                <i class="fas fa-times-circle me-1"></i>
                Delete Mails
            </button>
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this)" data-state="0">
                        </th>
                        <th>Subject</th>
                        <th>Body</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mails as $mail)
                        <tr class="text-center" wire:key="mail-{{ $mail->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $mail->id }}" onclick="updateSelectAllState()">
                            </td>
                            <td>
                                <div class="text-start">
                                    <div class="@if($mail->subject) fw-bold @else text-muted fst-italic @endif">
                                        {{ Str::limit($mail->subject ?? 'No subject', 30, '...') }}
                                    </div>
                                    <small class="text-muted">ID: #{{ $mail->id }}</small>
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 330px;">
                                    <small class="text-muted d-block text-wrap lh-base">
                                        {{ Str::limit(strip_tags($mail->body), 100, '...') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill
                                    @switch($mail->type)
                                        @case(0) bg-secondary @break
                                        @case(1) bg-success bootstrap-color @break
                                        @case(2) bg-danger @break
                                        @case(3) bg-info @break
                                        @case(4) bg-warning @break
                                        @case(5) bg-primary @break
                                    @endswitch
                                ">
                                    @switch($mail->type)
                                        @case(0) Custom @break
                                        @case(1) Order Success @break
                                        @case(2) Order Failed @break
                                        @case(3) Shipping Update @break
                                        @case(4) Forgot Password @break
                                        @case(5) Register Success @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span>{{ $mail->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $mail->created_at->format('H:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.mails.edit', $mail->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                        data-title="Delete Mail" data-type="warning" data-message="Are you sure you want to delete this mail #{{ $mail->id }}? This action cannot be undone."
                                        data-confirm-label="Confirm Delete" data-event-name="mail.deleted" data-event-data="{{ $mail->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No mail templates found</h5>
                                    <p class="text-muted">There are no mail templates or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($mails->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $mails->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
