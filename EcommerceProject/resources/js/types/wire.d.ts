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
    $on(event: string, callback: Function): void;

    $refresh(): void;
    $commit(): void;

    $dispatch(event: string, params?: Record<string, any>): void;
    $dispatchSelf(event: string, params?: Record<string, any>): void;
    $dispatchTo(
        otherComponentName: string,
        event: string,
        params?: Record<string, any>
    ): void;

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
