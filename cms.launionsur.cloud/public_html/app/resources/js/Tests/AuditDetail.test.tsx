import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import { vi } from 'vitest';

// mock Modal similar to other tests
vi.mock('@/Components/Modal', () => ({
    default: ({ show, onClose, children }: any) => {
        if (!show) return null;
        return (
            <div data-testid="modal">
                <button onClick={onClose} data-testid="modal-close">
                    Close
                </button>
                {children}
            </div>
        );
    },
}));

// mock inertia usePage to provide auth (super admin)
vi.mock('@inertiajs/react', async () => {
    const actual = await vi.importActual<any>('@inertiajs/react');
    return {
        ...actual,
        usePage: () => ({
            props: {
                auth: { user: { id: 1, name: 'Admin', role: { name: 'Super Admin' } } },
            },
        }),
    };
});

// mock axios module at import level (define inside factory to avoid hoisting issues)
vi.mock('axios', () => ({
    default: {
        get: async (url: string) => {
            if (url === '/api/audit-logs/42') {
                return {
                    data: {
                        id: 42,
                        user: { id: 1, name: 'Admin', email: 'admin@example.com' },
                        ip_address: '127.0.0.1',
                        created_at: '2024-01-01T00:00:00Z',
                        reason: 'Testing restore',
                        is_restoreable: true,
                        old_values: { name: 'Old Name' },
                        new_values: { name: 'New Name' },
                    },
                };
            }
            return { data: null };
        },
        post: async (url: string) => ({ data: {} }),
    },
}));

// set location so component extracts id
beforeEach(() => {
    // ensure pathname ends with id
    (window as any).location = new URL('http://localhost/admin/audit-logs/42');
});

import Detail from '@/Pages/AuditLog/Detail';

test('Audit Detail shows diff and can restore', async () => {
    render(<Detail />);

    // wait for header and user
    await waitFor(() => expect(screen.getByText('Audit Log #42')).toBeInTheDocument());
    expect(screen.getByText('Admin')).toBeInTheDocument();
    expect(screen.getByText('127.0.0.1')).toBeInTheDocument();

    // diff rows
    expect(screen.getByText('name')).toBeInTheDocument();
    expect(screen.getByText('Old Name')).toBeInTheDocument();
    expect(screen.getByText('New Name')).toBeInTheDocument();

    // restore button should be present (in Spanish label)
    const restoreBtn = screen.getByText('Restaurar');
    expect(restoreBtn).toBeInTheDocument();

    // click to open modal
    fireEvent.click(restoreBtn);
    await waitFor(() => expect(screen.getByTestId('modal')).toBeInTheDocument());

    // within modal click the confirm button
    const confirmBtn = screen.getByTestId('restore-confirm');
    expect(confirmBtn).toBeInTheDocument();
    fireEvent.click(confirmBtn);

    // success toast should appear (indicates restore completed)
    await waitFor(() => expect(screen.getByText('Restauración exitosa')).toBeInTheDocument());
});
