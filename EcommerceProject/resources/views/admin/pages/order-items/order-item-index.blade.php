<div>
    <x-livewire::filter-bar placeholderSearch="Search order items..." columnSearch="col-md-6 col-lg-4" modelSearch="search"
        columnReset="col-md-3 col-lg-2" resetAction="resetFilters" :isDetailFilter="true">
        <div class="col-md-3">
            <select class="form-select" wire:model.change="quantityRange">
                <option value="">All Quantities</option>
                <option value="1-5">1-5 items</option>
                <option value="6-10">6-10 items</option>
                <option value="11+">11+ items</option>
            </select>
        </div>
    </x-livewire::filter-bar>

    <x-livewire::data-table caption="Order Items Records" class="mt-3" :isDetailFilter="true">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $item)
                        @php
                            $variant = $item->productVariant;
                            $product = $variant->product;
                        @endphp
                        <tr class="text-center" wire:key="order-item-{{ $item->id }}">
                            <td style="min-width: 300px">
                                <div class="d-flex align-items-center">
                                    <div class="product-image me-2">
                                        <img src="{{ asset('storage/' . ($product->mainImage?->image_url ?? DefaultImage::PRODUCT->value)) }}"
                                            alt="Product Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0;">
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold">
                                            {{ Str::limit($product->title . " - " . $variant->name, 35, '...') }}
                                        </div>
                                        <small class="text-muted">Item ID: #{{ $item->id }}</small>
                                        <small class="text-muted d-block">Product ID: #{{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    {{ number_format($item->price, 0, '.', '.') }}đ
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary" style="font-size: 0.9rem;">
                                    <i class="fas fa-boxes me-1"></i>
                                    {{ number_format($item->quantity, 0, '.', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success bootstrap-color">
                                    {{ number_format($item->price * $item->quantity, 0, '.', '.') }}đ
                                </span>
                            </td>
                            <td>
                                <span>{{ $item->created_at->format('m/d/Y') }}</span>
                                <small class="text-muted d-block">{{ $item->created_at->format('H:i A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-state-row">
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state" style="padding: 0">
                                    <i class="fas fa-shopping-basket fa-2x text-muted mb-3"></i>
                                    <h5 class="text-muted">No order items found</h5>
                                    <p class="text-muted">There are no order items or matching search results at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:pagination>
            @if($orderItems->hasPages())
                <div class="card-footer bg-white custom-pagination" style="padding: 1.2rem 1.5rem;">
                    {{ $orderItems->links() }}
                </div>
            @endif
        </x-slot:pagination>
    </x-livewire::data-table>
</div>
