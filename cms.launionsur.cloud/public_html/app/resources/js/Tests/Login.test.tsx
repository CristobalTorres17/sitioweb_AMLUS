import { render, screen } from '@testing-library/react';
import Login from '@/Pages/Auth/Login';
import { vi } from 'vitest';

// Mock GuestLayout to avoid rendering site chrome
vi.mock('@/Layouts/GuestLayout', () => ({
    default: ({ children }: any) => <div>{children}</div>,
}));

// Mock useForm from inertia to avoid real implementation
vi.mock('@inertiajs/react', () => ({
    useForm: () => ({
        data: { email: '', password: '', remember: false },
        setData: () => {},
        post: () => {},
        processing: false,
        errors: {},
        reset: () => {},
    }),
    Head: ({ children }: any) => <div>{children}</div>,
    Link: ({ children }: any) => <div>{children}</div>,
}));

test('login page renders', () => {
    render(<Login canResetPassword={true} />);

    expect(screen.getByLabelText(/Correo institucional|Email/i)).toBeInTheDocument();
    expect(
        screen.getByPlaceholderText(/\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022|password/i),
    ).toBeInTheDocument();
    expect(screen.getByRole('heading', { name: /Iniciar sesi(o|ó)n|Log in/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /Ingresar al sistema|Log in/i })).toBeInTheDocument();
});
