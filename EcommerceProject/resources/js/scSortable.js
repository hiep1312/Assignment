import Sortable from 'sortablejs';

/**
 * @typedef {Object} scSortable
 * @property {Object} options - The configuration object of a Sortable instance.
 * @property {function(): string[]} toArray - Returns an array of IDs (as strings) of the dragged elements within the sortable.
 * @property {function(string[], boolean=): void} sort - Reorders the dragged elements according to the given array of IDs, optionally using animation.
*/

const $sc_configSortable = {
    /**
     * An object that stores the list of managed sortables, where the key is the ID and the value is the scSortable instance.
     *
     * @type {Object.<string, scSortable>}
     */
    _manage: {},
    _config: {
        delay: 0,
        delayOnTouchOnly: false,
        store: {
            /* get: function (sortable) {
                const order = localStorage.getItem(sortable.options.group.name);
                return order ? order.split('<$sc|>') : [];
            },
            set: function (sortable) {
                const order = sortable.toArray();
                localStorage.setItem(sortable.options.group.name, order.join('<$sc|>'));
            } */
        },
        animation: 150,
        easing: null,
        preventOnFilter: true,
        dataIdAttr: 'sc-id',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        ignore: 'a, img',
        swapThreshold: 1,
        invertSwap: false,
        invertedSwapThreshold: null,
        dropBubble: false,
        dragoverBubble: false,
        emptyInsertThreshold: 10,
    },
    _validators: {
        group_expression: function (value){
            const result = /\{(?:[^}]*)\}/u.test(value) ? JSON.parse(value.replace(/\s+,\s+/g, ', ').replace(/'/g, '"')) : value;
            return result;
        },
        sort_directive: function (directive){
            const {modifiers} = directive;
            return !modifiers.includes('sort');
        },
        disabled_directive: function (directive){
            const {modifiers} = directive;
            return modifiers.includes('disabled');
        },
        onSort_directive: function (directive){
            const {raw} = directive;
            const onSort = raw.match(/\.onsort\.([^\.]+)/);
            return onSort && onSort[1];
        },
        onMove_directive: function (directive){
            const {raw} = directive;
            const onMove = raw.match(/\.onmove\.([^\.]+(?:\.[^\.]+)?)/);
            return onMove && onMove[1];
        },
        checkFunction: function (model, $wire){
            return (model || false) && ($wire || typeof (Livewire.find(document.querySelector("[wire\\:id][sc-root]")?.getAttribute('wire:id') ?? document.querySelector("[wire\\:id]")?.getAttribute('wire:id')) ?? Livewire.first())[model] === 'function') && typeof $wire[model]==='function';
        }
    },
    _packageObjectEvent: function (event){
        if(!(event instanceof CustomEvent)) return typeof event === 'object' ? Object.fromEntries(event) : event;

        return {
            to: event?.to.dataset.row,
            from: event?.from.dataset.row,
            item: event?.item?.dataset.id,
            clone: event?.clone?.dataset.id,
            oldIndex: event?.oldIndex,
            newIndex: event?.newIndex,
            oldDraggableIndex: event?.oldDraggableIndex,
            newDraggableIndex: event?.newDraggableIndex,
            pullMode: event?.pullMode,
            dragged: event?.dragged?.dataset.id,
            draggedRect: event?.draggedRect && {
                top: event.draggedRect.top,
                left: event.draggedRect.left,
                right: event.draggedRect.right,
                bottom: event.draggedRect.bottom,
                width: event.draggedRect.width,
                height: event.draggedRect.height
            },
            related: event?.related?.dataset.id,
            relatedRect: event?.relatedRect && {
                top: event.relatedRect.top,
                left: event.relatedRect.left,
                right: event.relatedRect.right,
                bottom: event.relatedRect.bottom,
                width: event.relatedRect.width,
                height: event.relatedRect.height
            },
            willInsertAfter: event?.willInsertAfter
        };
    }
};

window.scSortable = {
    /**
     * Retrieves a Sortable instance based on an element or the element's ID.
     *
     * @param {string|HTMLElement} element - A string representing an ID, selector, or an HTML element.
     * @returns {Sortable|null} - Returns the Sortable instance if found, otherwise returns null.
     *
     * @description
     * - If `element` is a string, it will be used as an ID to look up in the management system `$sc_configSortable._manage`.
     * - If `element` is an `HTMLElement`, it will call `Sortable.get(element)` to retrieve the instance.
     * - If it’s neither a string nor an HTMLElement, it returns `null`.
     *
     * @example
     * let sortable1 = $sc_configSortable.get('my-list-id');
     * let sortable2 = $sc_configSortable.get(document.getElementById('my-list-id'));
     */

    get: function(element){
        if(!(element instanceof HTMLElement) && typeof element !== 'string') return null;
        let checkExistsInManage;
        typeof element === "string" && (checkExistsInManage = $sc_configSortable._manage[element]);

        return checkExistsInManage ?? Sortable.get(element instanceof HTMLElement ? element : document.querySelector(element));
    },

    /**
     * The currently active Sortable instance (being dragged or processed).
     * @type {Sortable|null}
     */
    active: Sortable.active,

    /**
     * The HTML element that is currently being dragged.
     * @type {HTMLElement|null}
     */
    dragged: Sortable.dragged,

    /**
     * The ghost element displayed while dragging.
     * @type {HTMLElement|null}
     */
    ghost: Sortable.ghost,

    /**
     * The cloned element of the item being dragged, used to preserve layout during the drag operation.
     * @type {HTMLElement|null}
     */
    clone: Sortable.clone
};

Object.seal($sc_configSortable);
Object.freeze(window.scSortable);

document.addEventListener('livewire:init', () => {
    Livewire.directive('sc-sortable', function ({ $wire, el, directive }){
        /* Set config */
        (el.hasAttribute('wire:ignore') || el.hasAttribute('wire:ignore.self')) || el.setAttribute('wire:ignore', '');

        const modelAtrribute = Array.from(el.attributes).find(attr => attr.name.startsWith('wire:sc-model'));
        /* Get directive & model */
        const model = modelAtrribute && modelAtrribute.value;
        const rawDirectiveModel = (modelAtrribute || '') && modelAtrribute.name.slice('wire:sc-model'.length);
        const modifiersModel = rawDirectiveModel.slice(1).split('.');
        /* Directive */
        const live = modifiersModel.includes('live');
        const debounce = rawDirectiveModel.match(/\.debounce\.([^\.]+)ms/)?.[1];

        const {expression} = directive;
        const group = expression ? $sc_configSortable._validators.group_expression(expression) : null;
        const handle = el.getAttribute('wire:sc-handle');
        const filter = el.getAttribute('wire:sc-filter');

        /* Modifier Options */
        const sort = $sc_configSortable._validators.sort_directive(directive);
        const disabled = $sc_configSortable._validators.disabled_directive(directive);

        let timeout;
        const onSort = function(evt){
            const callback = $sc_configSortable._validators.onSort_directive(directive);
            let result = null;
            typeof window[callback] === 'function' && (result = window[callback](evt));
            result !== null || ($sc_configSortable._validators.checkFunction(callback, $wire) && $wire[callback]($sc_configSortable._packageObjectEvent(evt)));
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                model &&
                typeof $wire[model] !== 'function' &&
                ((el.getAttribute('sc-group') && Array.isArray($sc_configSortable._manage[el.getAttribute('sc-group')])) ? $wire.$set(model, $sc_configSortable._manage[el.getAttribute('sc-group')].flatMap((sortable) => {
                    return sortable.toArray();
                }), live) : $wire.$set(model, sortable.toArray(), live));
            }, debounce ?? 0);
        };
        const onMove = function(evt){
            let callback = $sc_configSortable._validators.onMove_directive(directive);
            callback && (callback = callback.split('.'));
            let result = null;
            if(callback){
                for(let i = 0; i < callback.length; i++){
                    typeof window[callback[i]] === 'function' && (result = window[callback[i]](evt));
                    result !== null || ($sc_configSortable._validators.checkFunction(callback[i], $wire) && $wire[callback[i]]($sc_configSortable._packageObjectEvent(evt)));
                }
            }
            return result ?? function(evt){
                if(evt.to.classList.contains('disabled') && evt.to !== evt.from) return false;
                else if(evt.related.classList.contains('disabled') && (evt.related.classList.contains('before') || evt.related.classList.contains('after'))) return evt.related.classList.contains('before') ? -1 : 1;
                return void 0;
            }(evt);
        }
        const optionsCustom = Object.fromEntries(Object.entries({group, handle, filter, disabled, onSort, onMove}).filter(([, value]) => Boolean(value)));
        optionsCustom['sort'] = sort;

        const options = Object.assign({}, $sc_configSortable._config, optionsCustom);
        const sortable = Sortable.create(el, options);

        el.getAttribute('sc-group') &&
        ($sc_configSortable._manage[el.getAttribute('sc-group')] || ($sc_configSortable._manage[el.getAttribute('sc-group')] = sortable) && void 0) &&
        ($sc_configSortable._manage[el.getAttribute('sc-group')] = Array.isArray($sc_configSortable._manage[el.getAttribute('sc-group')]) ? [...$sc_configSortable._manage[el.getAttribute('sc-group')], sortable] : [$sc_configSortable._manage[el.getAttribute('sc-group')], sortable]);
    });
});
