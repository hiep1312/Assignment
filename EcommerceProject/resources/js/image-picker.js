let $wire = null;

window.cleanupDragAndDrop = new Function();

window.setupDragAndDrop = function() {
    const dropZoneElement = document.getElementById('dropZoneModal');
    const updateStateDropZone = function(event, isDragOver) {
        event.preventDefault();

        dropZoneElement.classList.toggle("drag-over", typeof isDragOver !== 'undefined' && isDragOver);
        dropZoneElement.classList.toggle("drag-leave", typeof isDragOver !== 'undefined' && !isDragOver);
    }

    const handleListener = {
        dragover: (e) => updateStateDropZone(e, true),
        dragleave: (e) => updateStateDropZone(e, false),
        drop: (e) => {
            updateStateDropZone(e);

            $wire && $wire.uploadMultiple(
                'photos',
                e.dataTransfer.files,
                new Function(),
                $wire.showUploadError,
                (eventProgress) => document.getElementById('loading-uploader').style.display = eventProgress.detail.progress < 100 ? 'flex' : 'none'
            );
        }
    };

    Object.entries(handleListener).forEach(([eventName, handler]) => {
        dropZoneElement.addEventListener(eventName, handler);
    });

    window.cleanupDragAndDrop = function() {
        Object.entries(handleListener).forEach(([eventName, handler]) => {
            dropZoneElement.removeEventListener(eventName, handler);
        });
    }
}

document.addEventListener('livewire:initialized', function() {
    $wire = Livewire.find(document.querySelector('.image-picker-modal')?.getAttribute('wire:id') ?? null) ?? Livewire.first();

    setupDragAndDrop();
});

function handleImagePickerCleanup(element, shouldReset = false) {
    if(element && element.classList.contains('image-picker-modal')) {
        cleanupDragAndDrop();

        shouldReset && setupDragAndDrop();
    }
}

Livewire.hook('morph.removing', ({ element }) => handleImagePickerCleanup(element));
Livewire.hook('morph.updated', ({ element }) => handleImagePickerCleanup(element, true));

window.toggleSelectAllImagePicker = function(state) {
    const imageCards = document.querySelectorAll('.image-card');

    imageCards.forEach(imageCard => {
        if((imageCard.classList.contains('selected') && state) || (!imageCard.classList.contains('selected') && !state)) {
            imageCard.click();
        }
    });
}
