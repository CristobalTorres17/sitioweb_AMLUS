import React from 'react';
import { render, screen } from '@testing-library/react';
import AuditFilter from '../../Components/AuditFilter';

describe('AuditFilter', () => {
    it('renders filter inputs', () => {
        render(
            <AuditFilter
                modelType=""
                setModelType={() => {}}
                action=""
                setAction={() => {}}
                isSensitive=""
                setIsSensitive={() => {}}
                userId=""
                setUserId={() => {}}
                startDate=""
                setStartDate={() => {}}
                endDate=""
                setEndDate={() => {}}
                onFilter={() => {}}
                onReset={() => {}}
            />,
        );

        expect(screen.getByLabelText(/Model Type/i)).toBeInTheDocument();
        expect(screen.getByLabelText(/Action/i)).toBeInTheDocument();
        expect(screen.getByLabelText(/User/i)).toBeInTheDocument();
    });
});
