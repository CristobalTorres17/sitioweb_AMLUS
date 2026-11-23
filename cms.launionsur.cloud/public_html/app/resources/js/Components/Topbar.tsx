import ApplicationLogo from '@/Components/ApplicationLogo';
import ThemeSwitcher from '@/Components/ThemeSwitcher';
import Dropdown from '@/Components/Dropdown';
import { usePage, Link } from '@inertiajs/react';
import { useSidebar } from '@/Context/SidebarContext';

export default function Topbar() {
    const user = usePage().props.auth.user;

    const { openBurger, toggleCollapse, desktopState } = useSidebar();

    return (
        <header className="sticky top-0 z-30 w-full bg-slate-100 dark:bg-gray-800 shadow">
            <div className="flex h-16 items-center px-6">
                {/* Burger for mobile */}
                <div className="lg:hidden">
                    <button
                        id="mobile-burger-button"
                        aria-label="Open menu"
                        onClick={() => openBurger()}
                        className="p-2"
                    >
                        ☰
                    </button>
                </div>

                {/* Logo: centered on small screens, left-aligned on sm+ */}
                <div className="flex-1 flex items-center justify-center sm:justify-start">
                    <span className="text-lg font-semibold text-gold">CMS</span>
                </div>

                <div className="flex items-center gap-3">
                    <ThemeSwitcher />
                </div>
            </div>
        </header>
    );
}
