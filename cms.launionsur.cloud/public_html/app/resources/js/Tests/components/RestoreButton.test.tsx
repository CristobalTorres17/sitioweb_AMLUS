import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { vi } from 'vitest';
import RestoreButton from '../../Components/RestoreButton';
import axios from 'axios';

vi.mock('axios');

describe('RestoreButton', () => {
    afterEach(() => {
        vi.resetAllMocks();
    });

    it('does not render when not restoreable', () => {
        const { container } = render(<RestoreButton auditId={1} isRestoreable={false} />);
        expect(container).toBeEmptyDOMElement();
    });

    it('opens modal and calls API on confirm', async () => {
        const mockPost = vi.fn().mockResolvedValue({ data: {} });
        (axios.post as any) = mockPost;
        const onRestored = vi.fn();

        render(<RestoreButton auditId={42} isRestoreable={true} onRestored={onRestored} />);

        fireEvent.click(screen.getByTestId('restore-open'));
        expect(screen.getByText(/Confirmar restauración/i)).toBeInTheDocument();

        fireEvent.click(screen.getByTestId('restore-confirm'));

        await waitFor(() =>
            expect(mockPost).toHaveBeenCalledWith('/api/audit-logs/42/restore', { reason: '' }),
        );
        expect(onRestored).toHaveBeenCalled();
    });
});
