interface Window {
    toggleSelectAll: (
        checkboxElement: HTMLElement & { dataset: DOMStringMap },
        isComponentScoped?: boolean
    ) => void;

    updateSelectAllState: () => void;

    showConfirmModal: (
        callingElement: HTMLElement & { dataset: DOMStringMap },
        eventTarget?: boolean | string
    ) => void;

    showToast: (
        source: ToastPayload | HTMLElement & { dataset: DOMStringMap },
        eventTarget?: string | boolean
    ) => void;

    humanizeTimeDifference: (
        baseTime: Date | number,
        targetTime?: Date | number
    ) => string;

    copyToClipboard: (
        text: string,
        button: HTMLElement
    ) => void;

    setQueryParams: (
        keyOrObject: Record<string, any> | string,
        value?: string | null
    ) => string;

    getQueryParams(
        fields?: string | string[]
    ): string | null | Record<string, string>;

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

interface ToastPayload {
    title?: string;
    message?: string;
    type?: string;
    duration?: number | string;
    time?: string;
    animation?: string;
    icon?: string;
    show?: boolean;
}
