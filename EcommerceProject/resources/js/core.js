/** @type {Wire} */
let $wire = null;

document.addEventListener('livewire:initialized', (e) => {
    $wire = Livewire.find(document.getElementById('main-component')?.getAttribute('wire:id') ?? null) ?? Livewire.first();
});

window.BasePageController = {
    _internal: {},
    _traits: [],
    _hooks: {
        init: [],
    },

    init() {
        this._applyTraits();
        this._applyHook('init');
        this.registerEvents();
    },

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
    },

    showError(status) {
        if(typeof status !== 'number') {
            throw new TypeError('Status must be a number');
        }

        if($wire) {
            $wire.$dispatchTo('client.partials.error', 'error:show', { status });
        }else {
            Livewire.dispatchTo('client.partials.error', 'error:show', { status });
        }

        Livewire.hook('morphed', this._hidePageForError);
    },

    _hidePageForError() {
        const mainComponent = document.getElementById('main-component');
        const navbar = document.querySelector('nav.navbar');
        const heroHeader = document.querySelector('.hero-header');

        if(mainComponent && (mainComponent.getAttribute('wire:ignore') === null || mainComponent.style.display !== 'none')) {
            mainComponent.setAttribute('wire:ignore', '');
            mainComponent.style.display = 'none';
        }

        if(navbar && (navbar.getAttribute('wire:ignore') === null || !navbar.classList.contains('navbar-no-breadcrumb'))) {
            navbar.setAttribute('wire:ignore', '');
            navbar.classList.add('navbar-no-breadcrumb');
        }

        if(heroHeader && (heroHeader.getAttribute('wire:ignore') === null || heroHeader.style.display !== 'none')) {
            heroHeader.setAttribute('wire:ignore', '');
            heroHeader.style.display = 'none';
        }
    },

    _applyHook(hookName) {
        for(const hookFn of this._hooks[hookName]) {
            hookFn();
        }
    },

    _applyTraits() {
        for(const trait of this._traits) {
            try {
                this._applyTraitObject(trait);
            }catch(error) {
                const traitName = trait?.constructor?.name || trait?.toString() || 'Anonymous trait';
                throw new Error(`Failed to apply trait "${traitName}": ${error.message}`);
            }
        }
    },

    _applyTraitObject(traitObj, override = false) {
        if(typeof traitObj !== 'object'){
            throw new TypeError('Trait must be an object');
        }

        for(const [key, value] of Object.entries(traitObj)) {
            if(typeof value === 'function' && Object.keys(this._hooks).includes(key)) {
                this._hooks[key].push(value.bind(this));
            }else if(this[key] !== undefined && !override) {
                continue;
            }

            if(typeof value === 'function') {
                this[key] = value.bind(this);
            }else {
                this[key] = value;
            }
        }
    }
};

window.Fetchable = {
    init() {
        this.fetchData();
    },

    fetchData: async () => {},

    refreshData() {
        $wire.$set('isDataLoading', true);
        this.fetchData();
    },

    _buildApiParams: {},
};

window.ApiPagination = {
    getPagination(response) {
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
}
