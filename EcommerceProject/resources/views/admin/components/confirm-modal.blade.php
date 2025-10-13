@php $typeIcon = [
    'success' => 'fas fa-check',
    'warning' => 'fas fa-exclamation-triangle',
    'error' => 'fas fa-times',
    'info' => 'fas fa-info',
    'question' => 'fas fa-question',
    'delete' => 'fas fa-trash-alt'
]; @endphp
<div class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h4 class="modal-title mb-3">Xác nhận xóa</h4>
                <p class="modal-text">
                    Bạn có chắc chắn muốn xóa mục này không?
                </p>
                <p class="modal-text text-danger small fw-bold">
                    ⚠️ Hành động này không thể hoàn tác!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
