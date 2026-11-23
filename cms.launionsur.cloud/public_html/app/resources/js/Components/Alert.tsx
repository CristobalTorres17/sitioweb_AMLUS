import { PropsWithChildren } from 'react';

export default function Alert({ children }: PropsWithChildren) {
    return (
        <div className="w-full mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">
            {children}
        </div>
    );
}
