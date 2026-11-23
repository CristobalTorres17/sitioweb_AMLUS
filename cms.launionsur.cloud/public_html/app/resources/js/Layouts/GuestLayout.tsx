import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            {/* Removed Laravel ApplicationLogo to match branded login */}

            {/* Render children directly so pages that provide their own shells (like LoginShell) control layout */}
            <div className="w-full">{children}</div>
        </div>
    );
}
