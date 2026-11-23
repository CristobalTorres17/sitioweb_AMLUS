import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import InputWithIcon from '@/Components/InputWithIcon';
import GuestLayout from '@/Layouts/GuestLayout';
import LoginShell from '@/Components/LoginShell';
import Card from '@/Components/Card';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = e => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Registro" />

            <LoginShell>
                <Card>
                    <h2 className="lf-title">Crear cuenta</h2>
                    <p className="lf-sub">Regístrate con tu cuenta institucional</p>

                    <form onSubmit={submit} className="space-y-4">
                        <div className="lf-field">
                            <InputLabel htmlFor="name" value="Nombre" />

                            <InputWithIcon
                                id="name"
                                name="name"
                                value={data.name}
                                className="lf-input"
                                autoComplete="name"
                                isFocused={true}
                                onChange={e => setData('name', e.target.value)}
                                required
                            />

                            <InputError message={errors.name} className="mt-2 lf-hint" />
                        </div>

                        <div className="lf-field">
                            <InputLabel htmlFor="email" value="Correo" />

                            <InputWithIcon
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="lf-input has-icon"
                                autoComplete="username"
                                onChange={e => setData('email', e.target.value)}
                                required
                            />

                            <InputError message={errors.email} className="mt-2 lf-hint" />
                        </div>

                        <div className="lf-field">
                            <InputLabel htmlFor="password" value="Contraseña" />

                            <InputWithIcon
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                className="lf-input"
                                autoComplete="new-password"
                                onChange={e => setData('password', e.target.value)}
                                required
                            />

                            <InputError message={errors.password} className="mt-2 lf-hint" />
                        </div>

                        <div className="lf-field">
                            <InputLabel
                                htmlFor="password_confirmation"
                                value="Confirmar contraseña"
                            />

                            <InputWithIcon
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                className="lf-input"
                                autoComplete="new-password"
                                onChange={e => setData('password_confirmation', e.target.value)}
                                required
                            />

                            <InputError
                                message={errors.password_confirmation}
                                className="mt-2 lf-hint"
                            />
                        </div>

                        <div>
                            <div className="mb-2 text-sm text-[rgb(var(--color-text-secondary))]">
                                ¿Ya estás registrado?{' '}
                                <Link
                                    href={route('login')}
                                    className="text-[rgb(var(--color-primary-hex))] underline"
                                >
                                    Ingresar
                                </Link>
                            </div>
                            <button
                                type="submit"
                                className="lf-btn w-full"
                                style={{
                                    background: 'rgb(var(--color-primary))',
                                    border: '1px solid rgb(var(--color-border-strong))',
                                    color: 'rgb(var(--color-gold))',
                                    borderRadius: '10px',
                                    padding: '12px',
                                    fontWeight: 600,
                                }}
                                disabled={processing}
                            >
                                Registrarse
                            </button>
                        </div>
                    </form>
                </Card>
            </LoginShell>
        </GuestLayout>
    );
}
