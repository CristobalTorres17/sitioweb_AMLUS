import { PropsWithChildren } from 'react';

type Props = PropsWithChildren<{
    className?: string;
    type?: 'button' | 'submit' | 'reset';
    onClick?: () => void;
    disabled?: boolean;
}>;

export default function PrimaryButton({
    children,
    className = '',
    type = 'submit',
    onClick,
    disabled,
}: Props) {
    return (
        <button
            type={type}
            onClick={onClick}
            disabled={disabled}
            className={`px-4 py-2 rounded bg-primary text-gold font-medium hover:opacity-90 disabled:opacity-50 ${className}`}
        >
            {children}
        </button>
    );
}
