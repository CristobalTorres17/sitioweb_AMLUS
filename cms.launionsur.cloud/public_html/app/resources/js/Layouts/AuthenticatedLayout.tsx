import { PropsWithChildren, ReactNode } from 'react';
import AutoBreadcrumb from '@/Components/AutoBreadcrumb';
import Topbar from '@/Components/Topbar';
import Sidebar from '@/Components/Sidebar';
import Toast from '@/Components/Toast';
import { SidebarProvider, useSidebar } from '@/Context/SidebarContext';

function InnerAuthenticated({ header, children }: PropsWithChildren<{ header?: ReactNode }>) {
    const { desktopState } = useSidebar();
    const desktopMarginClass = desktopState === 'collapsed' ? 'lg:ml-16' : 'lg:ml-64';

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-black text-primary dark:text-white">
            <Toast />

            <div className="flex">
                <Sidebar />
                <div className={`flex-1 ml-0 ${desktopMarginClass}`}>
                    <Topbar />
                    <div className="mx-auto max-w-7xl px-6 py-8">
                        <AutoBreadcrumb />

                        {header && (
                            <div className="mb-6 rounded-lg bg-surface border border-border px-6 py-4 shadow-sm dark:bg-zinc-900">
                                {header}
                            </div>
                        )}

                        <main>{children}</main>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Authenticated(props: PropsWithChildren<{ header?: ReactNode }>) {
    return (
        <SidebarProvider>
            <InnerAuthenticated {...props} />
        </SidebarProvider>
    );
}
