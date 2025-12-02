let $wire = null;

document.addEventListener('livewire:initialized', (e) => {
    $wire = Livewire.find(document.getElementById('main-component')?.getAttribute('wire:id') ?? null) ?? Livewire.first();
});

window.toggleSelectAll = function(checkboxElement, isComponentScoped = false){
    const reverseState = !Boolean(+checkboxElement.dataset.state);
    const dataIds = Array.from(document.querySelectorAll('.record-checkbox'), (checkbox) => {
        checkbox.checked = reverseState;
        return reverseState && checkbox.value;
    });

    checkboxElement.dataset.state = checkboxElement.checked = +reverseState;
    if(isComponentScoped) {
        const livewireComponent = Livewire.find(checkboxElement.closest('[wire\\:id]')?.getAttribute('wire:id'));
        livewireComponent && (livewireComponent.selectedRecordIds = reverseState ? dataIds : []);
    }else {
        $wire && ($wire.selectedRecordIds = reverseState ? dataIds : []);
    }
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

window.copyToClipboard = function(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const originalContent = button.innerHTML;

        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 1500);
    }).catch(error => {
        console.error('Failed to copy: ', error);
        window.alert('Oops! Something went wrong while copying the link.');
    });
};

/**
 * Extracts pagination data from an API response.
 *
 * @param {Object} response - The API response containing pagination data.
 * @param {number} response.current_page - The current page number.
 * @param {number} response.last_page - The last page number available.
 * @param {number} response.per_page - The number of items per page.
 * @param {string} response.first_page_url - The URL of the first page.
 * @param {string} response.last_page_url - The URL of the last page.
 * @param {Array<Object>} response.links - An array of link objects for pagination navigation.
 * @param {string|null} response.next_page_url - The URL of the next page, or null if none.
 * @param {string|null} response.prev_page_url - The URL of the previous page, or null if none.
 * @param {string} response.path - The base path for the pagination URLs.
 * @param {number} response.from - The index of the first item on the current page.
 * @param {number} response.to - The index of the last item on the current page.
 * @param {number} response.total - The total number of items across all pages.
 *
 * @returns {Object} A structured pagination object containing all relevant pagination properties.
 */
window.getPaginationFromApi = function(response) {
    if(typeof response !== 'object') return {};

    return {
        current_page: response.current_page,
        last_page: response.last_page,
        per_page: response.per_page,
        first_page_url: response.first_page_url,
        last_page_url: response.last_page_url,
        links: response.links,
        next_page_url: response.next_page_url,
        prev_page_url: response.prev_page_url,
        path: response.path,
        from: response.from,
        to: response.to,
        total: response.total
    };
}
