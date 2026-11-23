import { PropsWithChildren } from 'react';

export default function LoginShell({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen flex items-stretch justify-center p-6">
            <div
                className="w-full max-w-5xl login-shell flex rounded-lg overflow-hidden border"
                data-design-option="a"
            >
                <aside className="login-panel hidden md:flex flex-col w-96 p-6 bg-[rgb(var(--color-primary))] text-[rgb(var(--color-text-inverse))]">
                    <div>
                        <div className="lp-logo flex items-center gap-3 mb-6">
                            <div className="lp-logo-sq rounded-md bg-[rgb(var(--color-primary))] flex items-center justify-center text-[rgb(var(--color-primary))] font-medium">
                                <img
                                    src="/images/anchor.png"
                                    alt="CMS"
                                    className="h-16 rounded p-0"
                                />
                            </div>
                            <div className="lp-logo-text text-[16px] font-medium">
                                ALCADÍA DE LA UNIÓN SUR
                                <small className="block text-[12px] text-[rgba(217,183,119,.6)] font-normal">
                                    Portal Institucional
                                </small>
                            </div>
                        </div>
                        <h2 className="lp-headline text-[20px] font-medium mb-3">
                            Sistema de gestión de
                            <br />
                            contenidos y
                            <span className="text-[rgb(var(--color-gold))]"> publicaciones</span>
                        </h2>
                        <p className="lp-desc text-[13px] text-[rgba(217,195,154,.6)]">
                            Administración centralizada de noticias, páginas y medios para el sitio
                            web oficial de la Alcaldía de La Unión Sur.
                        </p>

                        <div className="lp-features mt-6 flex flex-col gap-3">
                            <div className="lp-feat flex items-center gap-3">
                                <span className="lp-feat-dot w-1.5 h-1.5 rounded-sm bg-[rgb(var(--color-gold))]"></span>
                                <span className="lp-feat-text text-[12px] text-[rgba(217,195,154,.7)]">
                                    Gestión de noticias y páginas
                                </span>
                            </div>
                            <div className="lp-feat flex items-center gap-3">
                                <span className="lp-feat-dot w-1.5 h-1.5 rounded-sm bg-[rgb(var(--color-gold))]"></span>
                                <span className="lp-feat-text text-[12px] text-[rgba(217,195,154,.7)]">
                                    Flujos de revisión y aprobación
                                </span>
                            </div>
                            <div className="lp-feat flex items-center gap-3">
                                <span className="lp-feat-dot w-1.5 h-1.5 rounded-sm bg-[rgb(var(--color-gold))]"></span>
                                <span className="lp-feat-text text-[12px] text-[rgba(217,195,154,.7)]">
                                    Control de usuarios y roles
                                </span>
                            </div>
                            <div className="lp-feat flex items-center gap-3">
                                <span className="lp-feat-dot w-1.5 h-1.5 rounded-sm bg-[rgb(var(--color-gold))]"></span>
                                <span className="lp-feat-text text-[12px] text-[rgba(217,195,154,.7)]">
                                    Auditoría de publicaciones
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="lp-bottom text-[11px] text-[rgba(217,183,119,.35)] mt-auto">
                        version 1.0.0 Alcaldía de La Unión Sur
                        <br />
                        Acceso exclusivo para personal autorizado.
                    </div>
                </aside>

                <main className="login-form-side flex-1 bg-[rgb(var(--color-surface))] flex items-center justify-center p-8">
                    <div className="login-form-wrap w-full max-w-sm">{children}</div>
                </main>
            </div>
        </div>
    );
}
