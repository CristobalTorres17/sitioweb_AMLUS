import { PropsWithChildren } from 'react';

export default function Card({ children }: PropsWithChildren) {
    return (
        <div className="w-full bg-[rgb(var(--color-surface))] border border-[rgb(var(--color-border))] shadow-sm rounded-lg p-8">
            {children}
        </div>
    );
}
