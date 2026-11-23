import '@testing-library/jest-dom';

// Provide a minimal global `route` helper used by some pages
// and a default axios stub that tests can override per-suite.

(globalThis as any).route = (name: string) => {
    const map: Record<string, string> = {
        login: '/login',
        'password.request': '/password/request',
    };
    return map[name] || `/${name}`;
};

// Default minimal axios stub

if (!(globalThis as any).axios) {
    (globalThis as any).axios = {
        get: async () => ({ data: [] }),
        post: async () => ({ data: {} }),
        delete: async () => ({ data: {} }),
    } as any;
}

// Mock inertia bindings used in pages to avoid requiring a full Inertia app context
import { vi } from 'vitest';

vi.mock('@inertiajs/react', () => ({
    useForm: (initial = {}) => {
        const data: Record<string, any> = Object.assign({}, initial);
        return {
            data,
            setData: (key: any, value?: any) => {
                if (typeof key === 'string') {
                    data[key] = value;
                } else if (typeof key === 'object') {
                    Object.assign(data, key);
                }
            },
            post: async (url: string) => {
                // forward to the global axios stub so tests can spy on it

                return (globalThis as any).axios.post(url, data);
            },
            processing: false,
            errors: {},
            reset: () => {
                Object.keys(data).forEach(k => (data[k] = ''));
            },
        };
    },
    Head: (props: any) => props.children,
    Link: (props: any) => props.children,
}));
