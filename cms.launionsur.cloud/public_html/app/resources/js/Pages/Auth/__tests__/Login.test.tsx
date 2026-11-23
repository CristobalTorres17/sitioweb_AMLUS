import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import Login from '@/Pages/Auth/Login';

vi.mock('@/Layouts/GuestLayout', () => ({
    default: ({ children }: any) => <div data-testid="guest">{children}</div>,
}));
vi.mock('@/Components/Checkbox', () => ({
    default: (props: any) => <input data-testid="checkbox" {...props} />,
}));
vi.mock('@/Components/InputError', () => ({
    default: ({ message }: any) => (message ? <div>{message}</div> : null),
}));
vi.mock('@/Components/InputLabel', () => ({ default: ({ value }: any) => <label>{value}</label> }));
vi.mock('@/Components/PrimaryButton', () => ({
    default: ({ children }: any) => <button>{children}</button>,
}));
vi.mock('@/Components/TextInput', () => ({ default: (props: any) => <input {...props} /> }));
vi.mock('@/Components/Card', () => ({ default: ({ children }: any) => <div>{children}</div> }));
vi.mock('@/Components/Alert', () => ({ default: ({ children }: any) => <div>{children}</div> }));
vi.mock('@/Components/InputWithIcon', () => ({ default: (props: any) => <input {...props} /> }));

describe('Login page', () => {
    it('renders and shows status and title', () => {
        render(<Login canResetPassword={true} status={'Prueba de estado'} />);

        expect(screen.getByText('Iniciar sesión')).toBeTruthy();
        expect(screen.getByText('Prueba de estado')).toBeTruthy();
    });
});
