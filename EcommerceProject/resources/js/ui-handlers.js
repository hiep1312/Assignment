let $wire = null;

document.addEventListener('livewire:initialized', (e) => {
    $wire = Livewire.find(document.getElementById('main-component')?.getAttribute('wire:id') ?? null) ?? Livewire.first();
});

window.toggleSelectAll = function(checkboxElement){
    const reverseState = !Boolean(+checkboxElement.dataset.state);
    const dataIds = Array.from(document.querySelectorAll('.record-checkbox'), (checkbox) => {
        checkbox.checked = reverseState;
        return reverseState && checkbox.value;
    });

    checkboxElement.dataset.state = checkboxElement.checked = +reverseState;
    $wire && ($wire.selectedRecordIds = reverseState ? dataIds : []);
}

window.updateSelectAllState = function(){
    const toggleAllElement = document.getElementById('toggleAll');
    const stateNew = Array.from(document.querySelectorAll('.record-checkbox')).every(checkbox => checkbox.checked);

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

window.humanizeTimeDifference = function(baseTime, targetTime = new Date()){
    const units = {
        year: 31536000000,
        month: 2592000000,
        week: 604800000,
        day: 86400000,
        hour: 3600000,
        minute: 60000,
        second: 1000
    };

    const diffMilliseconds = baseTime - targetTime;
    const relativeTimeFormatter = new Intl.RelativeTimeFormat('en', {style: 'long', numeric: 'auto'});

    for(const [unit, value] of Object.entries(units)) {
        if(Math.abs(diffMilliseconds) >= value || unit === "second"){
            return relativeTimeFormatter.format(Math.round(diffMilliseconds / value), unit);
        }
    }
}
