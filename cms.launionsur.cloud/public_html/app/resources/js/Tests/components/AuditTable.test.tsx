import React from 'react';
import { render, screen } from '@testing-library/react';
import AuditTable from '../../Components/AuditTable';

describe('AuditTable', () => {
    it('shows loading', () => {
        render(<AuditTable logs={[]} loading={true} onRowClick={() => {}} />);
        expect(screen.getByText(/Cargando/i)).toBeInTheDocument();
    });

    it('shows empty message', () => {
        render(<AuditTable logs={[]} loading={false} onRowClick={() => {}} />);
        expect(screen.getByText(/No hay registros/i)).toBeInTheDocument();
    });

    it('renders rows', () => {
        const logs = [
            {
                id: 1,
                created_at: new Date().toISOString(),
                user: { name: 'Alice' },
                action: 'create',
                model_type: 'App\\Models\\User',
                ip_address: '1.2.3.4',
                is_sensitive: false,
            },
        ];
        render(<AuditTable logs={logs} loading={false} onRowClick={() => {}} />);
        expect(screen.getByText(/Alice/)).toBeInTheDocument();
        expect(screen.getByText(/create/)).toBeInTheDocument();
    });
});
