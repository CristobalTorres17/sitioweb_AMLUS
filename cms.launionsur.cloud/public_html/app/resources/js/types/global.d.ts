// Global type shims for the CMS frontend
declare global {
    interface Window {
        Laravel?: {
            user?: {
                id?: number;
                [key: string]: any;
            };
        };
        axios: any;
    }
}

// Allow importing modules without types during transition
declare module 'sortablejs';
declare module '@/Components/ui/button';

export {};

// Minimal test globals to satisfy TypeScript during build (install @types/jest for full typing)
declare var test: any;
declare var expect: any;
declare var describe: any;
declare var it: any;
declare var beforeEach: any;
declare var afterEach: any;
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { PageProps as AppPageProps } from './';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    var route: typeof ziggyRoute;
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
