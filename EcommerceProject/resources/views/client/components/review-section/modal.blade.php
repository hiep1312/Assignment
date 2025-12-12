@assets
    @vite('resources/css/review-modal.css')
@endassets

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content pdp-modal-content">
            <div class="modal-header pdp-modal-header">
                <h5 class="modal-title"><i class="fas fa-pen-fancy"></i> Viết đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pdp-modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-star pdp-icon-label"></i> Đánh giá sao</label>
                        <div class="pdp-rating-input">
                            <button type="button" class="pdp-star-input" data-rating="1"><i class="fas fa-star"></i></button>
                            <button type="button" class="pdp-star-input" data-rating="2"><i class="fas fa-star"></i></button>
                            <button type="button" class="pdp-star-input" data-rating="3"><i class="fas fa-star"></i></button>
                            <button type="button" class="pdp-star-input" data-rating="4"><i class="fas fa-star"></i></button>
                            <button type="button" class="pdp-star-input" data-rating="5"><i class="fas fa-star"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewContent" class="form-label"><i class="fas fa-pen-fancy pdp-icon-label"></i> Nội dung đánh giá</label>
                        <textarea class="form-control pdp-form-textarea" id="reviewContent" rows="5" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="verifiedPurchase">
                        <label class="form-check-label" for="verifiedPurchase">
                            <i class="fas fa-check"></i> Tôi đã mua sản phẩm này
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer pdp-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn pdp-btn-submit-review">Gửi đánh giá</button>
            </div>
        </div>
    </div>
</div>
