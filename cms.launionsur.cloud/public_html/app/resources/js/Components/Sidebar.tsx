import NavLink from '@/Components/NavLink';
import { Link } from '@inertiajs/react';
import { useSidebar } from '@/Context/SidebarContext';
import { useEffect, useRef, useState, type ReactNode } from 'react';
import Modal from '@/Components/Modal';
import { router } from '@inertiajs/react';

function SidebarSection({
    title,
    collapsed = false,
    children,
}: {
    title: string;
    collapsed?: boolean;
    children: ReactNode;
}) {
    return (
        <div className="space-y-1">
            <div
                className={`px-2 pt-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-gold/60 ${collapsed ? 'hidden' : ''}`}
            >
                {title}
            </div>
            <div className="flex flex-col gap-1">{children}</div>
        </div>
    );
}

export default function Sidebar() {
    const { desktopState, burgerOpen, closeBurger } = useSidebar();
    const isCollapsed = desktopState === 'collapsed';
    const asideRef = useRef<HTMLElement | null>(null);

    // Desktop sidebar (expanded or collapsed)
    const desktopWidthClass = isCollapsed ? 'w-16' : 'w-64';

    // Safe route helper: avoid calling `route(name)` when the route isn't registered in Ziggy
    function safeRoute(name: string, fallback = '#') {
        try {
            return route().has(name) ? route(name) : fallback;
        } catch (e) {
            return fallback;
        }
    }

    function renderSections(collapsed = false) {
        return (
            <>
                <SidebarSection title="General" collapsed={collapsed}>
                    <NavLink href={route('dashboard')} active={route().current('dashboard')}>
                        Dashboard
                    </NavLink>
                    <NavLink href={route('media.index')} active={route().current('media.*')}>
                        Media
                    </NavLink>
                </SidebarSection>

                <SidebarSection title="Contenido" collapsed={collapsed}>
                    <NavLink href={route('articles.index')} active={route().current('articles.*')}>
                        Noticias
                    </NavLink>
                    <NavLink href={safeRoute('pages.index')} active={isCurrent('pages.*')}>
                        Páginas
                    </NavLink>
                    <NavLink href={route('events.index')} active={route().current('events.*')}>
                        Eventos
                    </NavLink>
                    <div className="ml-4">
                        <NavLink
                            href={safeRoute('categories.index')}
                            active={route().current('categories.*')}
                        >
                            Categorías
                        </NavLink>
                    </div>
                    <NavLink href={route('banners.index')} active={route().current('banners.*')}>
                        Banners
                    </NavLink>
                    <NavLink
                        href={safeRoute('cms-blocks.index')}
                        active={route().current('cms-blocks.*')}
                    >
                        CMS Blocks
                    </NavLink>
                </SidebarSection>

                <SidebarSection title="Servicios y trámites" collapsed={collapsed}>
                    <NavLink href={route('services.index')} active={route().current('services.*')}>
                        Servicios
                    </NavLink>
                    <div className="ml-4">
                        <NavLink
                            href={safeRoute('service-categories.index')}
                            active={route().current('service-categories.*')}
                        >
                            Categorías de servicios
                        </NavLink>
                    </div>
                    <NavLink
                        href={route('districts.index')}
                        active={route().current('districts.*')}
                    >
                        Distritos
                    </NavLink>
                </SidebarSection>

                <SidebarSection title="Administración" collapsed={collapsed}>
                    <NavLink
                        href={safeRoute('admin.menus.index')}
                        active={route().current('admin.menus.*')}
                    >
                        Menús
                    </NavLink>
                    <NavLink
                        href={safeRoute('admin.settings.index')}
                        active={route().current('admin.settings.*')}
                    >
                        Ajustes
                    </NavLink>
                    <NavLink
                        href={safeRoute('admin.roles.index')}
                        active={route().current('admin.roles.*')}
                    >
                        Roles
                    </NavLink>
                    <NavLink
                        href={safeRoute('admin.permissions.index')}
                        active={route().current('admin.permissions.*')}
                    >
                        Permisos
                    </NavLink>
                    <NavLink
                        href={safeRoute('admin.users.index')}
                        active={route().current('admin.users.*')}
                    >
                        Usuarios
                    </NavLink>
                </SidebarSection>

                <SidebarSection title="Cuenta" collapsed={collapsed}>
                    <NavLink href={route('profile.edit')} active={route().current('profile.*')}>
                        Perfil
                    </NavLink>
                </SidebarSection>
            </>
        );
    }

    // Robust active-route checker: prefer Ziggy's route().current, fallback to pathname
    function isCurrent(pattern: string) {
        try {
            return route().current(pattern);
        } catch (e) {
            if (typeof window === 'undefined') return false;
            const path = window.location.pathname.replace(/^\/+/, '');
            if (pattern.endsWith('.*')) {
                const base = pattern.replace(/\.\*$/, '');
                return path === base || path.startsWith(base + '/');
            }
            return path === pattern || path.startsWith(pattern + '/');
        }
    }

    useEffect(() => {
        if (!burgerOpen || !asideRef.current) return;

        const container = asideRef.current;

        // find focusable elements inside the off-canvas
        const focusableSelector =
            'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';
        const focusable = Array.from(
            container.querySelectorAll<HTMLElement>(focusableSelector),
        ).filter(el => el.offsetParent !== null);

        const first = (focusable[0] as HTMLElement) ?? container;
        const last = (focusable[focusable.length - 1] as HTMLElement) ?? container;

        // focus the first focusable element
        (first as HTMLElement).focus();

        function onKeyDown(e: KeyboardEvent) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeBurger();
                const burgerBtn = document.getElementById(
                    'mobile-burger-button',
                ) as HTMLElement | null;
                if (burgerBtn) burgerBtn.focus();
                return;
            }

            if (e.key === 'Tab') {
                if (focusable.length === 0) {
                    e.preventDefault();
                    return;
                }
                const active = document.activeElement as HTMLElement | null;
                if (!active) return;

                if (e.shiftKey) {
                    // shift + tab
                    if (active === first || active === container) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    // tab
                    if (active === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }
        }

        document.addEventListener('keydown', onKeyDown);

        // prevent background from scrolling while open
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', onKeyDown);
            document.body.style.overflow = prevOverflow;
        };
    }, [burgerOpen, closeBurger]);

    return (
        <>
            <aside
                className={`hidden lg:block ${desktopWidthClass} shrink-0 bg-primary dark:bg-primary fixed top-0 left-0 bottom-0 transition-all`}
            >
                <div className="h-full overflow-y-auto px-4 py-4">
                    <div className="mb-4 px-0 pb-2 border-b border-gold/20">
                        <Link
                            href={route('dashboard')}
                            className={`flex items-center gap-3 ${isCollapsed ? 'justify-center' : ''}`}
                        >
                            <img
                                src="/images/anchor.png"
                                alt="CMS"
                                className="h-10 w-10 rounded p-0"
                            />
                            {!isCollapsed && (
                                <span className="ml-3 text-sm font-extrabold hidden sm:inline leading-tight bg-gradient-to-tr from-[#d9b777] via-[#d9c39a] to-[#8c603a] bg-clip-text text-transparent">
                                    ALCALDIA DE
                                    <br />
                                    LA UNION SUR
                                </span>
                            )}
                        </Link>
                    </div>

                    <nav className="flex flex-col px-1">{renderSections(isCollapsed)}</nav>
                </div>
            </aside>

            {/* Mobile off-canvas when burgerOpen */}
            {burgerOpen && (
                <div className="lg:hidden fixed inset-0 z-50 flex">
                    <div className="fixed inset-0 bg-black/50" onClick={closeBurger} />
                    <aside
                        ref={el => (asideRef.current = el)}
                        role="dialog"
                        aria-modal="true"
                        className="relative w-72 bg-primary dark:bg-primary h-full flex flex-col px-4 py-4"
                    >
                        {/* focus trap + ESC handling attached via useEffect below */}
                        <div className="flex items-center justify-between mb-4">
                            <Link href={route('dashboard')} className="flex items-center gap-3">
                                <svg
                                    className="h-8 w-8 bg-gold text-primary rounded p-1"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        strokeWidth="2"
                                    />
                                </svg>
                                <span className="text-lg font-semibold text-gold">CMS</span>
                            </Link>

                            <button
                                aria-label="Cerrar"
                                className="p-2 text-gold hover:bg-primary/80 rounded"
                                onClick={closeBurger}
                            >
                                ✕
                            </button>
                        </div>

                        <nav className="flex-1 overflow-y-auto flex flex-col px-1">
                            {renderSections(false)}
                        </nav>

                        <div className="mt-4 border-t border-gold/20 pt-4 flex-none">
                            <div className="flex flex-col gap-2">
                                {/* Logout with confirmation modal */}
                                <LogoutButton closeBurger={closeBurger} />
                            </div>
                        </div>
                    </aside>
                </div>
            )}
        </>
    );
}

function LogoutButton({ closeBurger }: { closeBurger: () => void }) {
    const [open, setOpen] = useState(false);

    function confirmLogout() {
        // close the burger and perform logout POST
        closeBurger();
        router.post(route('logout'));
    }

    return (
        <>
            <button
                onClick={() => setOpen(true)}
                className="text-sm text-gold/80 hover:text-gold text-left"
            >
                Cerrar sesión
            </button>

            <Modal show={open} onClose={() => setOpen(false)} closeable={true}>
                <div className="p-6">
                    <h3 className="text-lg font-medium">Confirmar cierre de sesión</h3>
                    <p className="text-sm text-gray-600 mt-2">
                        ¿Está seguro de que desea cerrar sesión?
                    </p>

                    <div className="mt-4 flex justify-end gap-2">
                        <button
                            className="px-3 py-2 rounded bg-gray-100"
                            onClick={() => setOpen(false)}
                        >
                            Cancelar
                        </button>

                        <button
                            className="px-3 py-2 rounded bg-red-600 text-white"
                            onClick={confirmLogout}
                        >
                            Cerrar sesión
                        </button>
                    </div>
                </div>
            </Modal>
        </>
    );
}
