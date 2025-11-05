import {
    //Editor
    ClassicEditor,

    //Text Formatting
    Alignment, Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline,

    //Block Elements
    BlockQuote, CodeBlock,

    //Special Content
    Emoji, Mention, HtmlEmbed, MediaEmbed,

    //Core
    Essentials,

    //Typography
    Font, Heading, HorizontalLine, Fullscreen, Title, Highlight,

    //Images
    Image, ImageInsert, ImageCaption, ImageResize, ImageStyle, ImageToolbar,

    //Indentation
    Indent, IndentBlock,

    //Links
    AutoLink, Link, LinkImage,

    //Lists
    List, TodoList,

    //Basic Content
    Paragraph, RemoveFormat,

    //Tables
    Table, TableCaption, TableToolbar,

    //History
    Undo,
} from 'ckeditor5';

import ImageLibraryPlugin from './ImageLibraryPlugin';

let $wire = null;
let imageTasksQueue = [];

class EditorController {
    /**
     * Private static reference to the global CKEditor instance.
     *
     * @type {ClassicEditor|null}
     */
    static #editor = null;
    static #model = null;
    static #live = null;
    static wrapperElement = null;
    static #keyReload = null;
    static #idRoot = null;
    static #debounceTimer = null;
    static isFocused = false;

    static get editor(){
        return this.#editor;
    }

    static get sourceElement(){
        return this.#editor?.sourceElement;
    }

    static get state(){
        return this.#editor?.state;
    }

    static get keyReload(){
        return this.#keyReload;
    }

    static get idRoot(){
        return this.#idRoot;
    }

    static setup(editorRoot = '#ckeditor'){
        if(this.#editor) return;

        const editorElement = editorRoot instanceof HTMLElement ? editorRoot : document.querySelector(editorRoot);
        const {
            initialData,
            label = 'Content Editor',
            placeholder = 'Start typing your content here...',
            updateOnDestroy: updateSourceElementOnDestroy = true
        } = editorElement.dataset;

        this.wrapperElement = editorElement.closest('[wire\\:ignore]') ?? editorElement.parentElement;
        if(!this.wrapperElement) throw new Error('[Livewire Integration Error] Cannot add [wire:ignore] — the editor element has no parent node.');
        this.wrapperElement.toggleAttribute('wire:ignore', true);
        this.#keyReload = this.wrapperElement.getAttribute('wire:key');
        this.#idRoot = editorRoot;

        ClassicEditor.create(editorElement, {
            licenseKey: 'GPL',
            initialData, label, placeholder, updateSourceElementOnDestroy,

            image: {
                insert: {
                    integrations: ['localLibrary', 'url'],
                    type: 'auto'
                },

                upload: {
                    types: ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp']
                },

                toolbar: [
                    'imageTextAlternative', '|',
                    'imageStyle:inline', 'imageStyle:block', '|',
                    'imageStyle:wrapText', 'imageStyle:breakText', '|',
                    'toggleImageCaption', '|',
                    'linkImage'
                ]
            },

            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', '|', 'toggleTableCaption'],
                defaultHeadings: {
                    rows: 0,
                    columns: 0
                },
                tableToolbar: [
                    'bold', 'italic', 'underline', '|',
                    'link', '|',
                    'alignment', '|',
                    'fontColor', 'fontBackgroundColor'
                ]
            },

            plugins: [
                Essentials, Paragraph, Undo,
                Bold, Italic, Underline, Strikethrough, Code, Subscript, Superscript, RemoveFormat,
                Heading, Alignment, Font,
                BlockQuote, CodeBlock, HorizontalLine,
                List, TodoList,
                AutoLink, Link, LinkImage,
                Image, ImageInsert, ImageLibraryPlugin, ImageCaption, ImageResize, ImageStyle, ImageToolbar,
                Table, TableCaption, TableToolbar,
                Emoji, Mention, MediaEmbed
            ],

            removePlugins: [
                Fullscreen, Title, Highlight, Indent, IndentBlock, HtmlEmbed
            ],

            toolbar: {
                items: [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'alignment', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'link', 'imageInsert', 'mediaEmbed', 'insertTable', 'blockQuote', 'codeBlock', 'horizontalLine', '|',
                    'emoji', '|',
                    'code', 'subscript', 'superscript', '|',
                    'removeFormat'
                ],
                shouldNotGroupWhenFull: false
            }
        }).then(createdEditor => {
            this.#editor = createdEditor;
            this.#model = createdEditor.sourceElement.dataset.model;
            this.#live = createdEditor.sourceElement.dataset.live;

            if(this.isFocused){
                createdEditor.focus();
                this.isFocused = false;
            }

            Livewire.on('editor.insertImage', ([sources]) => EditorController.insertImagePicker(sources));
            for(const sourceImages of imageTasksQueue){
                this.insertImagePicker(sourceImages);
            }
            imageTasksQueue = [];

            if(typeof createdEditor.sourceElement.dataset.sync === 'string') this.#registerSync();
        }).catch(error => {
            console.error('An error occurred while initializing the editor:', error);
        });
    }

    static getData(source = 'editor', livewireSync = true, immediate = true){
        if(!(['editor', 'root'].includes(source))){
            throw new Error(`[EditorController Error] Invalid data source "${source}". Expected "editor" or "root".`);
        }

        if(this.#editor){
            const data = (source === 'editor' ? this.#editor.getData({ trim: 'empty' }) : this.#editor.sourceElement.innerHTML);
            if(livewireSync) this.#syncWithLivewire(immediate);

            return data;
        }

        return '';
    }

    static setData(data){
        if(this.#editor){
            this.#editor.setData(data);
        }
    }

    static insertImagePicker(sources){
        if(!(
            typeof sources === 'string' ||
            (Array.isArray(sources) && sources.length > 0)
        )) throw new Error('[ImageLibraryPlugin] insertImagePicker(sources): Argument must be a non-empty string or a non-empty array of strings.');

        if(EditorController.editor) EditorController.editor.execute('insertImage', { source: sources });
        else imageTasksQueue.push(sources);
    }

    static updateSourceElement(data){
        if(this.#editor){
            this.#editor.updateSourceElement(data);
        }
    }

    static destroy(livewireSync = true, immediate = true){
        if(this.#editor){
            if(livewireSync) this.#syncWithLivewire(immediate);

            this.#unregisterSync();
            this.#editor.model.document.destroy();
            this.#editor.destroy();
            this.#editor = null;
            this.#model = null;
            this.wrapperElement = null;
        }
    }

    static #syncWithLivewire(isLive = true){
        if(this.#model && $wire){
            typeof $wire[this.#model] === 'function'
                ? $wire[this.#model](this.#editor.getData({ trim: 'empty' }))
                : $wire.$set(this.#model, this.#editor.getData({ trim: 'empty' }), isLive);
        }
    }

    static #registerSync(){
        this.#editor.model.document.on('change:data', () => {
            clearTimeout(this.#debounceTimer);
            this.#debounceTimer = setTimeout(() => {
                this.updateSourceElement();
                this.#syncWithLivewire(typeof this.#live === 'string');
            }, parseInt(this.#live || 0));
        });
    }

    static #unregisterSync(){
        clearTimeout(this.#debounceTimer);
        this.#editor.model.document.off('change:data', this.#debounceTimer);
        this.#debounceTimer = null;
    }
}

document.addEventListener('livewire:initialized', () => {
    $wire = Livewire.find(document.getElementById('main-component')?.getAttribute('wire:id')) ?? Livewire.first();

    window.editorAPI = {
        keyReload: EditorController.keyReload,
        editor: EditorController.editor,
        state: EditorController.state,
        sourceElement: EditorController.sourceElement,
        wrapperElement: EditorController.wrapperElement,
        setup: EditorController.setup,
        getData: EditorController.getData,
        setData: EditorController.setData,
        insertImage: EditorController.insertImagePicker,
        updateSourceElement: EditorController.updateSourceElement,
        destroy: EditorController.destroy
    };

    EditorController.setup();
});

function resyncEditorInstance($morphedWire){
    const editorInstance = EditorController.editor;
    const currentWrapper = $morphedWire.el.querySelector(EditorController.idRoot ?? '')?.parentElement;
    const wasFocused = EditorController.editor?.ui.view.editable.isFocused;

    if(!editorInstance || EditorController.wrapperElement === currentWrapper || $wire.el.getAttribute('wire:id') !== $morphedWire.el.getAttribute('wire:id')) {
        return;
    }

    EditorController.destroy();

    if(EditorController.keyReload !== currentWrapper?.getAttribute('wire:key')){
        EditorController.setup();
        EditorController.isFocused = wasFocused;
    }
}

Livewire.hook('morphed',  ({ component }) => resyncEditorInstance(component.$wire));
Livewire.hook('commit.prepare', () => {
    if(!EditorController.editor) return;

    EditorController.getData(
        EditorController.sourceElement.tagName === 'textarea' ? 'root' : 'editor',
        true,
        false
    );
});
Livewire.on('editor.update', ([data]) => EditorController.setData(data));
