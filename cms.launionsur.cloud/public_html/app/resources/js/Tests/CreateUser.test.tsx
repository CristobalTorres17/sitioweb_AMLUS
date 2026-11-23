import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import CreateUser from '@/Pages/Users/Create';
import { vi } from 'vitest';

vi.mock('@/Layouts/AuthenticatedLayout', () => ({
    default: ({ children }: any) => <div>{children}</div>,
}));

test('create user form shows success message on submit', async () => {
    const postMock = vi.fn().mockResolvedValue({ data: {} });

    (globalThis as any).axios = {
        get: async (url: string) => ({ data: [{ id: 1, name: 'editor' }] }),
        post: postMock,
    } as any;

    // stub location change

    delete (globalThis as any).location;

    (globalThis as any).location = { href: '' } as any;

    render(<CreateUser />);

    // fill form
    fireEvent.change(screen.getByLabelText(/Name/i), { target: { value: 'Bob' } });
    fireEvent.change(screen.getByLabelText(/Email/i), { target: { value: 'bob@example.com' } });
    fireEvent.change(screen.getByLabelText('Password'), { target: { value: 'password123' } });
    fireEvent.change(screen.getByLabelText('Confirm Password'), {
        target: { value: 'password123' },
    });
    fireEvent.change(screen.getByRole('combobox'), { target: { value: '1' } });

    fireEvent.click(screen.getByText(/Create/i));

    await waitFor(() => expect(postMock).toHaveBeenCalled());
    expect((globalThis as any).location.href).toBe('/admin/users');
});
