import React from 'react';
import { render, screen } from '@testing-library/react';
import DiffViewer from '../../Components/DiffViewer';

describe('DiffViewer', () => {
    it('renders message when no diffs', () => {
        render(<DiffViewer diff={[]} />);
        expect(screen.getByText(/No hay cambios/i)).toBeInTheDocument();
    });

    it('renders diff rows', () => {
        const diffs = [
            { field: 'title', old: 'Old', new: 'New' },
            { field: 'description', old: '', new: 'Added' },
        ];
        render(<DiffViewer diff={diffs as any} />);
        expect(screen.getByText('title')).toBeInTheDocument();
        expect(screen.getByText('Old')).toBeInTheDocument();
        expect(screen.getByText('New')).toBeInTheDocument();
        expect(screen.getByText('description')).toBeInTheDocument();
    });
});
