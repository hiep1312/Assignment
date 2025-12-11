type HookFunction = () => void;
type HookMap = Record<string, HookFunction[]>;

interface Trait {
    [key: string]: any;
}

interface PaginationResponse {
    current_page: number;
    last_page: number;
    per_page: number;
    first_page_url: string;
    last_page_url: string;
    links: object[];
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
    from: number;
    to: number;
    total: number;
}

interface BasePageController {
    _internal: Record<string, any>;
    _traits: Trait[];
    _hooks: HookMap;

    init(): void;
    registerEvents(): void;
    unregisterEvents(): void;
    showError(status: number): void;
    _hidePageForError(): void;

    _applyHook(hookName: string): void;
    _applyTraits(): void;
    _applyTraitObject(traitObj: Trait, override?: boolean): void;

    events: Record<string, EventListenerOrEventListenerObject>;
}

interface FetchableTrait extends Trait {
    init?(): void;
    fetchData(): Promise<any>;
    refreshData(): void;
    _buildApiParams: Record<string, Function>;
}

interface PaginationTrait extends Trait {
    getPagination(response: any): PaginationResponse;
}

interface Window {
    BasePageController: BasePageController;
    Fetchable: FetchableTrait;
    ApiPagination: PaginationTrait;
}
