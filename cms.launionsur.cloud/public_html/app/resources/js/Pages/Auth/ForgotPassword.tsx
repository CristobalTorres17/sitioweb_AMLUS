import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import InputWithIcon from '@/Components/InputWithIcon';
import GuestLayout from '@/Layouts/GuestLayout';
import LoginShell from '@/Components/LoginShell';
import Card from '@/Components/Card';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ForgotPassword({ status }: { status?: string }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit: FormEventHandler = e => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Recuperar contraseña" />

            <LoginShell>
                <Card>
                    <h2 className="lf-title">Recuperar contraseña</h2>
                    <p className="lf-sub">
                        Ingresa tu correo para recibir el enlace de restablecimiento
                    </p>

                    {status && <div className="success-banner show">{status}</div>}

                    <form onSubmit={submit} className="space-y-4">
                        <div className="lf-field">
                            <InputLabel htmlFor="email" value="Correo" />

                            <InputWithIcon
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="lf-input has-icon"
                                isFocused={true}
                                onChange={e => setData('email', e.target.value)}
                                placeholder="example@launionsur.gob.sv"
                            />

                            <InputError message={errors.email} className="mt-2 lf-hint" />
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
                                    padding: '12px',
                                    fontWeight: 600,
                                }}
                                disabled={processing}
                            >
                                Enviar enlace de restablecimiento
                            </button>
                        </div>
                    </form>
                </Card>
            </LoginShell>
        </GuestLayout>
    );
}
