// Global helper types for Inertia pages
export type InertiaPageProps = Record<string, unknown>;

declare global {
    type PageProps = InertiaPageProps;
}

export {};
