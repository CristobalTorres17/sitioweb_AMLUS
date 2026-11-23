import { InputHTMLAttributes, ReactNode } from 'react';

export default function InputWithIcon({
    icon,
    className = '',
    // accept isFocused for compatibility
    isFocused,
    ...props
}: {
    icon?: ReactNode;
    className?: string;
    isFocused?: boolean;
} & InputHTMLAttributes<HTMLInputElement>) {
    return (
        <div className={`relative ${className}`}>
            {icon && (
                <div className="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center h-5 w-5 text-[rgb(var(--color-text-muted))]">
                    {/* ensure svg/icon children scale to intended size */}
                    <span className="h-5 w-5 flex items-center justify-center text-[rgb(var(--color-text-muted))]">
                        {icon}
                    </span>
                </div>
            )}

            <input
                {...props}
                className={`w-full pl-12 pr-3 py-2.5 text-sm rounded-md border-[rgb(var(--color-border))] border bg-[rgb(var(--color-surface))] focus:border-[rgb(var(--color-primary))] focus:shadow-[0_0_0_3px_rgb(var(--focus-ring-color))] outline-none`}
            />
        </div>
    );
}
