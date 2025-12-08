interface Window {
    toggleSelectAll: (
        checkboxElement: HTMLElement & { dataset: DOMStringMap },
        isComponentScoped?: boolean
    ) => void;

    updateSelectAllState: () => void;

    confirmModalAction: (
        callingElement: HTMLElement & { dataset: DOMStringMap },
        eventTarget?: boolean | string
    ) => void;

    humanizeTimeDifference: (
        baseTime: Date | number,
        targetTime?: Date | number
    ) => string;

    copyToClipboard: (
        text: string,
        button: HTMLElement
    ) => void;

    getPaginationFromApi: (
        response: {
            current_page: number;
            last_page: number;
            per_page: number;
            first_page_url: string;
            last_page_url: string;
            links: Array<object>;
            next_page_url: string | null;
            prev_page_url: string | null;
            path: string;
            from: number;
            to: number;
            total: number;
        } | any
    ) => {
        current_page: number;
        last_page: number;
        per_page: number;
        first_page_url: string;
        last_page_url: string;
        links: Array<object>;
        next_page_url: string | null;
        prev_page_url: string | null;
        path: string;
        from: number;
        to: number;
        total: number;
    } | {};

    setQueryParams: (
        keyOrObject: Record<string, any> | string,
        value?: string | null
    ) => string;

    getQueryParams(
        fields?: string | string[]
    ): string | null | Record<string, string>;

    BasePageController: {
        init(): void;
        fetchData(): Promise<any>;
        refreshData(): void;

        _buildApiParams: Record<string, Function>;
        _internal: Record<string, any>;
        events: Record<string, (event: any) => void>;

        registerEvents(): void;
        unregisterEvents(): void;
    };

    getCookie(
        key?: string | string[] | null,
        defaultValue?: any
    ): any | any[] | Record<string, string>;

    setCookie(
        key: string | Record<string, any>,
        value?: any,
        options?: {
            expires?: number | Date;
            path?: string;
            domain?: string;
            secure?: boolean;
            sameSite?: 'Strict' | 'Lax' | 'None';
        }
    ): void;
}

interface Wire {
    [key: string]: any;

    $parent: Wire | null;

    $el: HTMLElement;

    $id: string;

    $get(name: string): any;

    $set(name: string, value: any, live?: boolean): void;

    $toggle(name: string, live?: boolean): void;

    $js(name: string, callback: Function): void;

    $entangle(name: string, live?: boolean): any;

    $refresh(): void;

    $commit(): void;

    $on(event: string, callback: Function): void;

    $dispatch(event: string, params?: Record<string, any>): void;

    $dispatchTo(
        otherComponentName: string,
        event: string,
        params?: Record<string, any>
    ): void;

    $dispatchSelf(event: string, params?: Record<string, any>): void;

    $upload(
        name: string,
        file: File,
        finish?: () => void,
        error?: () => void,
        progress?: (event: { detail: { progress: number } }) => void
    ): void;

    $uploadMultiple(
        name: string,
        files: File[],
        finish?: () => void,
        error?: () => void,
        progress?: (event: { detail: { progress: number } }) => void
    ): void;
}
