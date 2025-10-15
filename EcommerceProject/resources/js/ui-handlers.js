let $wire = null;

document.addEventListener('livewire:initialized', (e) => {
    $wire = Livewire.find(document.getElementById('main-component')?.getAttribute('wire:id') ?? null) ?? Livewire.first();
});

window.toggleSelectAll = function(checkboxElement){
    const reverseState = !Boolean(+checkboxElement.dataset.state);
    const dataIds = Array.from(document.querySelectorAll('.user-checkbox'), (checkbox) => {
        checkbox.checked = reverseState;
        return reverseState && checkbox.value;
    });

    checkboxElement.dataset.state = checkboxElement.checked = +reverseState;
    $wire && ($wire.selectedUserIds = reverseState ? dataIds : []);
}

window.updateSelectAllState = function(){
    const toggleAllElement = document.getElementById('toggleAll');
    const stateNew = Array.from(document.querySelectorAll('.user-checkbox')).every(checkbox => checkbox.checked);

    toggleAllElement.dataset.state = toggleAllElement.checked = +stateNew;
}

window.confirmModalAction = function(callingElement, eventTarget = false){
    let { title, type, message, id, confirmLabel, eventName, eventData } = callingElement.dataset;
    if(
        eventData &&
        (eventData = JSON.parse(eventData)) &&
        !Array.isArray(eventData)
    ){
        eventData = [eventData];
    }

    if($wire){
        const data = {title, type, message, id, confirmLabel, eventName, eventData, realtimeOpen: true};
        switch(true){
            case typeof eventTarget === 'string':
                $wire.$dispatchTo(eventTarget, 'modal.show', data);
                break;
            case eventTarget === true:
                $wire.$dispatch('modal.show', data);
                break;
            default:
                $wire.$dispatchTo('admin.components.confirm-modal', 'modal.show', data)
                break;
        }
    }
}
