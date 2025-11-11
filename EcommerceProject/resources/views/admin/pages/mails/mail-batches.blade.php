@use('App\Enums\DefaultImage')
@use('Illuminate\Pagination\LengthAwarePaginator')
@use('Carbon\Carbon')
<div wire:poll.5s>
    <livewire:admin.components.confirm-modal wire:key="confirm-modal">

    <x-livewire::filter-bar placeholderSearch="Search mail batches..." modelSearch="search" resetAction="resetFilters" :isDetailFilter="true">
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
            <select class="form-select" wire:model.change="status">
                <option value="">All Status</option>
                <option value="0">Pending</option>
                <option value="1">Sent</option>
                <option value="2">Failed</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Mail Batch Records" class="mt-3">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-danger bootstrap-focus" style="padding: 0.4rem 1.25rem;" title="Delete Batches"
                x-show="$wire.selectedRecordIds.length" x-transition onclick="confirmModalAction(this)"
                data-title="Delete Mail Batches" data-type="warning" x-bind:data-message="`Are you sure you want to delete these ${$wire.selectedRecordIds.length} mail batches? This action cannot be undone.`"
                data-confirm-label="Confirm Delete" data-event-name="mailBatch.deleted" wire:key="delete">
                <i class="fas fa-times-circle me-1"></i>
                Delete Batches
            </button>
        </x-slot:actions>
        <div class="table-responsive shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="toggleAll" onclick="toggleSelectAll(this, true)" data-state="0">
                        </th>
                        <th>Mail</th>
                        <th>Body</th>
                        <th>Type</th>
                        <th class="text-nowrap">Delivery Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mailBatches as $batch)
                        <tr class="text-center" wire:key="batch-{{ $batch->batch_key }}">
                            <td>
                                <input type="checkbox" class="form-check-input record-checkbox" wire:model="selectedRecordIds"
                                    value="{{ $batch->batch_key }}" onclick="updateSelectAllState()">
                            </td>
                            <td style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    <div class="text-start">
                                        <div class="fw-bold text-wrap lh-base">{{ Str::limit($batch->subject ?? 'Subject not available', 40, '...') }}</div>
                                        <small class="text-muted d-block">Mail ID: #{{ $batch->mail_id }}</small>
                                        <small class="text-muted">Batch Key: {{ Str::limit($batch->batch_key, 25, '...') }}</small>
                                        <button class="btn btn-sm btn-outline-secondary bootstrap-focus ms-1" style="padding: 0.15rem 0.25rem; font-size: 0.75rem;"
                                            title="Copy Batch Key" onclick="copyToClipboard('{{ $batch->batch_key }}', this)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 270px; max-width: 320px;">
                                @if($batch->body)
                                    <small class="text-muted d-block text-wrap lh-base">{{ Str::limit(strip_tags($batch->body), 110, '...') }}</small>
                                @else
                                    <small class="text-danger d-block">
                                        <i class="fas fa-times-circle me-1"></i>Content deleted or not available
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill
                                    @switch($batch->type)
                                        @case(0) bg-secondary @break
                                        @case(1) bg-success bootstrap-color @break
                                        @case(2) bg-danger @break
                                        @case(3) bg-info @break
                                        @case(4) bg-warning @break
                                        @case(5) bg-primary @break
                                    @endswitch
                                ">
                                    @switch($batch->type)
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
                                <div class="batches-status-box">
                                    <div class="batches-status-item">
                                        <div class="batches-status-icon batches-status-sent">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="batches-status-text">
                                            <span class="batches-status-label">Sent</span>
                                            <span class="batches-status-count">{{ number_format($batch->sent_count) }}</span>
                                        </div>
                                    </div>
                                    <div class="batches-status-item">
                                        <div class="batches-status-icon batches-status-pending">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="batches-status-text">
                                            <span class="batches-status-label">Sending</span>
                                            <span class="batches-status-count">{{ number_format($batch->pending_count) }}</span>
                                        </div>
                                    </div>
                                    <div class="batches-status-item">
                                        <div class="batches-status-icon batches-status-failed">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div class="batches-status-text">
                                            <span class="batches-status-label">Failed</span>
                                            <span class="batches-status-count">{{ number_format($batch->failed_count) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info btn-action bootstrap-focus" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBatch{{ $batch->batch_key }}"
                                        aria-expanded="false" aria-controls="collapseBatch{{ $batch->batch_key }}" wire:key="collapse-batch-{{ $batch->batch_key }}" wire:ignore.self>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-action" title="Delete" onclick="confirmModalAction(this)"
                                        data-title="Delete Mail Batch" data-type="warning" data-message="Are you sure you want to delete this mail batch #{{ $batch->batch_key }}? This action cannot be undone."
                                        data-confirm-label="Confirm Delete" data-event-name="mailBatch.deleted" data-event-data='"{{ $batch->batch_key }}"'>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <x-livewire::expandable-row id="collapseBatch{{ $batch->batch_key }}" title="Mail Recipients" icon="fas fa-user-friends" wire:key="collapse-batch-{{ $batch->batch_key }}">
                            <div class="card-body p-0 table-responsive shadow-sm" style="border-radius: 0.5rem 0.5rem 0 0;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light text-center">
                                        <tr class="text-nowrap">
                                            <th>Recipient</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Sent At</th>
                                            <th>Error Message</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-light">
                                        @php
                                            $recipientPageName = 'recipientsPage';
                                            $currentRecipientPage = LengthAwarePaginator::resolveCurrentPage($recipientPageName);
                                            $recipients = collect(json_decode($batch->recipients, false, 512));
                                            $paginatedRecipients = new LengthAwarePaginator(
                                                items: $recipients->forPage($currentRecipientPage, 5),
                                                total: $recipients->count(),
                                                perPage: 5,
                                                currentPage: $currentRecipientPage,
                                                options: [
                                                    'pageName' => $recipientPageName
                                                ]
                                            );
                                        @endphp
                                        @foreach($paginatedRecipients as $recipient)
                                            <tr class="text-center" wire:key="recipient-{{ $recipient->id }}">
                                                <td style="min-width: 250px;">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('storage/' . ($recipient->avatar ?? DefaultImage::AVATAR->value)) }}"
                                                            class="rounded-circle me-2" width="40" height="40" alt="User Avatar">
                                                        <div class="text-start">
                                                            <div class="fw-bold">
                                                                {{ Str::limit($recipient->name ?? 'Unknown User', 30, '...') }}
                                                            </div>
                                                            <small class="text-muted">ID: #{{ $recipient->id }}</small>
                                                            <small class="text-muted d-block">User ID: #{{ $recipient->user_id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($recipient->email)
                                                        <small class="text-dark d-block text-nowrap">
                                                            {{ Str::limit($recipient->email, 25, '...') }}
                                                        </small>
                                                    @else
                                                        <small class="text-danger d-block text-nowrap">
                                                            <i class="fas fa-times-circle me-1"></i>User deleted or not found
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge
                                                        @switch($recipient->status)
                                                            @case(0) bg-label-warning @break
                                                            @case(1) bg-label-success bootstrap-color @break
                                                            @case(2) bg-label-danger @break
                                                        @endswitch
                                                    ">
                                                    @switch($recipient->status)
                                                        @case(0) <i class="fas fa-clock"></i> Pending @break
                                                        @case(1) <i class="fas fa-check-circle"></i> Sent @break
                                                        @case(2) <i class="fas fa-times-circle"></i> Failed @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @if($recipient->sent_at)
                                                        @php $sentAt = Carbon::parse($recipient->sent_at); @endphp
                                                        <span>{{ $sentAt->format('m/d/Y') }}</span>
                                                        <small class="text-muted d-block">{{ $sentAt->format('H:i A') }}</small>
                                                    @else
                                                        <span class="text-muted">Not sent yet</span>
                                                    @endif
                                                </td>
                                                <td style="min-width: 270px;">
                                                    @if($recipient->error_message)
                                                        <small class="text-danger text-wrap lh-base">{{ Str::limit($recipient->error_message, 85, '...') }}</small>
                                                        <button class="btn btn-sm btn-outline-secondary bootstrap-focus ms-1" style="padding: 0.15rem 0.25rem; font-size: 0.75rem;"
                                                            title="Copy Error Message" onclick="copyToClipboard('{{ $recipient->error_message }}', this)">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No errors</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php $createdAt = Carbon::parse($batch->created_at); @endphp
                                                    <span>{{ $createdAt->format('m/d/Y') }}</span>
                                                    <small class="text-muted d-block">{{ $createdAt->format('H:i A') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($paginatedRecipients->hasPages())
                                <div class="card-footer bg-white custom-pagination shadow-sm" style="padding: 1.2rem 1.5rem;">
                                    {{ $paginatedRecipients->onEachSide(1)->links(data: ['scrollTo' => "collapseBatch{$batch->batch_key}"]) }}
                                </div>
                            @endif
                        </x-livewire::expandable-row>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No mail batches found</h5>
                                    <p class="text-muted">There are no mail batches or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($mailBatches->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $mailBatches->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
