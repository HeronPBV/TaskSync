export {};

declare global {
    function route(
        name: string,
        params?: Record<string, any> | string,
        absolute?: boolean
    ): string;
}
