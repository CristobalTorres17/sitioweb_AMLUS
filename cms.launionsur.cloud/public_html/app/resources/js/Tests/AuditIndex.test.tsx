import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import { vi } from 'vitest';

// mock axios module before importing the page
vi.mock('axios', () => {
    return {
        default: {
            get: async (url: string, options?: any) => {
                if (url === '/api/audit-logs') {
                    const params = options?.params || {};
                    const page = Number(params.page || 1);
                    const per_page = Number(params.per_page || 15);
                    const total = 20;
                    const start = (page - 1) * per_page + 1;
                    const end = Math.min(start + per_page - 1, total);
                    const items: any[] = [];
                    for (let i = start; i <= end; i++) {
                        items.push({
                            id: i,
                            model_type: 'App\\Models\\User',
                            model_id: i,
                            action: 'update',
                            is_sensitive: false,
                            is_restoreable: false,
                            created_at: '2024-01-01T00:00:00Z',
                            user: { id: 1, name: `User ${i}`, email: `u${i}@example.com` },
                        });
                    }
                    return { data: { data: items, total } };
                }
                return { data: { data: [], total: 0 } };
            },
        },
    };
});

import AuditIndex from '@/Pages/AuditLog/Index';

test('Audit Index renders and paginates', async () => {
    render(<AuditIndex />);

    // wait for first page items
    await waitFor(() => expect(screen.getByText('User 1')).toBeInTheDocument());

    expect(screen.getByText('Página 1 de 2')).toBeInTheDocument();

    const nextBtn = screen.getByText('Next');
    const prevBtn = screen.getByText('Prev');

    // Prev should be disabled on first page
    expect(prevBtn).toBeDisabled();
    expect(nextBtn).not.toBeDisabled();

    // go to next page
    fireEvent.click(nextBtn);

    await waitFor(() => expect(screen.getByText('Página 2 de 2')).toBeInTheDocument());

    // now Prev should be enabled and Next disabled
    expect(prevBtn).not.toBeDisabled();
    expect(nextBtn).toBeDisabled();

    // Confirm a page 2 item exists
    expect(screen.getByText('User 16')).toBeInTheDocument();
});
