import { Link } from '@inertiajs/react';

export type BreadcrumbItem = {
    label: string;
    href: string | null;
    current: boolean;
};

interface BreadcrumbProps {
    items: BreadcrumbItem[];
}

export default function Breadcrumb({ items }: BreadcrumbProps) {
    return (
        <nav aria-label="Breadcrumb" className="mb-6">
            <ol className="flex items-center gap-2 text-sm">
                {items.map((item, index) => (
                    <li key={index} className="flex items-center gap-2">
                        {item.current ? (
                            <>
                                {index > 0 && <span className="text-gray-400">/</span>}
                                <span className="text-gray-700 font-medium">{item.label}</span>
                            </>
                        ) : (
                            <>
                                {index > 0 && <span className="text-gray-400">/</span>}
                                <Link
                                    href={item.href || '#'}
                                    className="text-primary hover:underline"
                                >
                                    {item.label}
                                </Link>
                            </>
                        )}
                    </li>
                ))}
            </ol>
        </nav>
    );
}
