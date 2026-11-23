import { render, screen, waitFor } from '@testing-library/react';
import UsersIndex from '@/Pages/Users/Index';
import { vi } from 'vitest';

vi.mock('@/Layouts/AuthenticatedLayout', () => ({
    default: ({ children }: any) => <div>{children}</div>,
}));

vi.mock('@inertiajs/react', () => ({
    Head: ({ children }: any) => <div>{children}</div>,
    usePage: () => ({
        props: {
            users: {
                data: [{ id: 1, name: 'Alice', email: 'a@example.com', role: { name: 'editor' } }],
            },
            roles: [{ id: 2, name: 'editor' }],
        },
    }),
    router: {
        get: vi.fn(),
    },
}));

test('user list renders with users from Inertia props', async () => {
    render(<UsersIndex />);

    await waitFor(() => expect(screen.getByText('Alice')).toBeInTheDocument());
    expect(screen.getByText('a@example.com')).toBeInTheDocument();
    const editors = screen.getAllByText(/editor/i);
    expect(editors.length).toBeGreaterThan(0);
});
