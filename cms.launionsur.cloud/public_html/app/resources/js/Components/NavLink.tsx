import { InertiaLinkProps, Link } from '@inertiajs/react';

export default function NavLink({
    active = false,
    className = '',
    children,
    ...props
}: InertiaLinkProps & { active: boolean }) {
    return (
        <Link
            {...props}
            className={
                'inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out rounded focus:outline-none ' +
                (active
                    ? 'bg-gold/15 text-gold focus:ring-2 focus:ring-gold/50'
                    : 'text-gold/70 hover:bg-primary/80 hover:text-gold focus:bg-primary/80 focus:text-gold') +
                ' ' +
                className
            }
        >
            {children}
        </Link>
    );
}
