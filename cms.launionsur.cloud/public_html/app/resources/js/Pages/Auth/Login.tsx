import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import InputWithIcon from '@/Components/InputWithIcon';
import GuestLayout from '@/Layouts/GuestLayout';
import LoginShell from '@/Components/LoginShell';
import Card from '@/Components/Card';
import Alert from '@/Components/Alert';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const [showPassword, setShowPassword] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);

    const submit: FormEventHandler = e => {
        e.preventDefault();
        setSubmitting(true);

        post(route('login'), {
            onFinish: () => {
                reset('password');
                setSubmitting(false);
                setShowSuccess(true);
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Iniciar sesión" />

            <LoginShell>
                <Card>
                    <h2 className="lf-title">Iniciar sesión</h2>
                    <p className="lf-sub">Ingresa tus credenciales institucionales</p>
                    <br />

                    {(status || showSuccess) && (
                        <div className={`success-banner show`}>
                            {status ?? 'Credenciales verificadas. Redirigiendo al panel...'}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-4 media-form">
                        <div className="lf-field">
                            <InputLabel htmlFor="email" value="Correo institucional" />

                            <InputWithIcon
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                autoComplete="username"
                                isFocused={true}
                                onChange={e => setData('email', e.target.value)}
                                placeholder="usuario@launionsur.gob.sv"
                                icon={
                                    <svg
                                        width="18"
                                        height="14"
                                        viewBox="0 0 20 16"
                                        fill="none"
                                        className="text-[rgb(var(--color-text-muted))]"
                                        aria-hidden
                                    >
                                        <path
                                            d="M1 3.5v9a1 1 0 001 1h16a1 1 0 001-1v-9a1 1 0 00-1-1H2a1 1 0 00-1 1z"
                                            stroke="currentColor"
                                            strokeWidth="1"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M2 4.5l8 5 8-5"
                                            stroke="currentColor"
                                            strokeWidth="1"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                    </svg>
                                }
                                className="lf-input has-icon"
                            />

                            <InputError message={errors.email} className="mt-2 lf-hint" />
                        </div>

                        <div className="lf-field">
                            <InputLabel htmlFor="password" value="Contraseña" />

                            <div className="relative">
                                <InputWithIcon
                                    id="password"
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    value={data.password}
                                    autoComplete="current-password"
                                    onChange={e => setData('password', e.target.value)}
                                    placeholder="••••••••"
                                    className="lf-input pass-input"
                                />

                                <button
                                    type="button"
                                    onClick={() => setShowPassword(s => !s)}
                                    className="absolute right-2 top-1/2 -translate-y-1/2 lf-eye"
                                    aria-label="toggle-password"
                                >
                                    {showPassword ? (
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path
                                                d="M2 2l12 12"
                                                stroke="#9aacbc"
                                                strokeWidth="1"
                                                strokeLinecap="round"
                                            />
                                        </svg>
                                    ) : (
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <ellipse
                                                cx="8"
                                                cy="8"
                                                rx="7"
                                                ry="4.5"
                                                stroke="#9aacbc"
                                                strokeWidth="1"
                                            />
                                            <circle cx="8" cy="8" r="2" fill="#9aacbc" />
                                        </svg>
                                    )}
                                </button>
                            </div>

                            <InputError message={errors.password} className="mt-2 lf-hint" />
                        </div>

                        <div className="lf-row">
                            <label className="lf-check-row">
                                <Checkbox
                                    name="remember"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked as boolean)}
                                />
                                <span className="ms-2 lf-check-label">Recordar sesión</span>
                            </label>

                            {canResetPassword && (
                                <Link
                                    href={route('password.request')}
                                    className="lf-forgot"
                                    style={{ marginLeft: 12 }}
                                >
                                    ¿Olvidaste tu contraseña?
                                </Link>
                            )}
                        </div>

                        <div>
                            <button
                                type="submit"
                                className="lf-btn w-full"
                                style={{
                                    background: 'rgb(var(--color-primary))',
                                    border: '1px solid rgb(var(--color-border-strong))',
                                    color: 'rgb(var(--color-gold))',
                                    borderRadius: '10px',
                                    position: 'relative',
                                    paddingLeft: '48px',
                                    paddingRight: '20px',
                                    paddingTop: '10px',
                                    paddingBottom: '10px',
                                    fontWeight: 600,
                                }}
                                disabled={processing || submitting}
                            >
                                <svg
                                    id="btn-arrow"
                                    width="14"
                                    height="14"
                                    viewBox="0 0 14 14"
                                    fill="none"
                                    aria-hidden
                                    style={{
                                        position: 'absolute',
                                        left: 12,
                                        top: '50%',
                                        transform: 'translateY(-50%)',
                                    }}
                                ></svg>

                                <span
                                    id="btn-text"
                                    style={{ display: 'block', width: '100%', textAlign: 'center' }}
                                >
                                    {submitting ? 'Verificando...' : 'Ingresar al sistema'}
                                </span>

                                <svg
                                    id="btn-spinner"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 16 16"
                                    fill="none"
                                    className={`${submitting ? '' : 'hidden'} animate-spin`}
                                    aria-hidden
                                    style={{
                                        position: 'absolute',
                                        right: 12,
                                        top: '50%',
                                        transform: 'translateY(-50%)',
                                    }}
                                >
                                    <circle
                                        cx="8"
                                        cy="8"
                                        r="6"
                                        stroke="rgba(217,183,119,.35)"
                                        strokeWidth="2"
                                    />
                                    <path
                                        d="M8 2a6 6 0 016 6"
                                        stroke="rgb(var(--color-gold))"
                                        strokeWidth="2"
                                        strokeLinecap="round"
                                    />
                                </svg>
                            </button>

                            {/* SSO block removed */}
                        </div>
                    </form>
                </Card>
            </LoginShell>
        </GuestLayout>
    );
}
