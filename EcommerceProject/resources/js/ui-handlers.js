/** @type {Wire} */
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

window.setQueryParams = function(keyOrObject, value = null) {
    const params = new URLSearchParams(window.location.search);
    const applyParam = function(key, value){
        return value === null ?
            params.delete(key) :
            params.set(key, value);
    };

    if(typeof keyOrObject === "object") {
        for(const [paramKey, paramValue] of Object.entries(keyOrObject)) {
            applyParam(paramKey, paramValue);
        }
    }else {
        applyParam(keyOrObject, value);
    }

    const queryString = params.size ? ('?' + params.toString()) : '';
    const newUrl = window.location.pathname + queryString;
    history.replaceState({}, '', newUrl);

    return newUrl;
}

window.getQueryParams = function(fields) {
    const params = new URLSearchParams(window.location.search);

    if(Array.isArray(fields)) {
        return fields.map(key => params.get(key));
    }else if(arguments.length === 0) {
        return Object.fromEntries(params);
    }

    return params.get(fields);
}

window.BasePageController = {
    init() {
        /* Fetch initial data */
        this.fetchData();
        this.registerEvents();
    },

    fetchData: async () => {},

    refreshData() {
        $wire.$set('isDataLoading', true);
        this.fetchData();
    },

    _buildApiParams: {},

    _internal: {},

    events: {},

    registerEvents() {
        for(const [eventName, handler] of Object.entries(this.events)) {
            document.addEventListener(eventName, handler);
        }

        /* Register default events and ensure cleanup on page unload */
        window.addEventListener('beforeunload', this.unregisterEvents);
    },

    unregisterEvents() {
        for(const [eventName, handler] of Object.entries(this.events)) {
            document.removeEventListener(eventName, handler);
        }
    }
};

window.getCookie = function(key = null, defaultValue = null) {
    const cookies = document.cookie.split(';').reduce((accumulator, cookie) => {
        const [name, ...rest] = cookie.trim().split('=');
        accumulator[decodeURIComponent(name)] = decodeURIComponent(rest.join('='));
        return accumulator;
    }, {});

    if(key === null) {
        return cookies;
    }else if(Array.isArray(key)) {
        return key.map(cookieName => cookies[cookieName]);
    }

    return cookies[key] ?? defaultValue;
}

window.setCookie = function(key, value = '', options = {}) {
    const buildCookieString = (name, cookieValue) => {
        let cookieStr = `${encodeURIComponent(name)}=${encodeURIComponent(cookieValue)}`;

        if(typeof options.expires !== "undefined") {
            if(options.expires instanceof Date) {
                cookieStr += `; expires=${options.expires.toUTCString()}`;
            }else {
                cookieStr += `; max-age=${options.expires}`;
            }
        }

        if(options.domain) cookieStr += `; domain=${options.domain}`;
        if(options.path) cookieStr += `; path=${options.path}`;
        if(options.secure) cookieStr += `; secure`;
        if(options.sameSite) cookieStr += `; samesite=${options.sameSite}`;

        return cookieStr;
    };

    if(typeof key === 'object' && key !== null) {
        if(typeof value === "object" && value !== null && !Object.keys(options).length) {
            [options, value] = [value, ''];
        }

        for(const cookieName in key) {
            document.cookie = buildCookieString(cookieName, key[cookieName]);
        }
    }else {
        document.cookie = buildCookieString(key, value);
    }
}
