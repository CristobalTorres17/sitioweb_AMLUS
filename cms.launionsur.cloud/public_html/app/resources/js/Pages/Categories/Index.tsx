import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

interface Category {
    id: number;
    name: string;
    slug: string;
    type: string;
    description: string | null;
    icon: string | null;
    color: string | null;
    is_active: boolean;
    position: number;
    deleted_at: string | null;
}

const TYPE_LABELS: Record<string, string> = {
    article: 'Noticias',
    event: 'Eventos',
};

const TYPE_COLORS: Record<string, string> = {
    article: 'bg-blue-100 text-blue-700',
    event: 'bg-purple-100 text-purple-700',
};

export default function Index({ categories }: { categories: Category[] }) {
    function handleDelete(id: number) {
        if (!confirm('¿Seguro que deseas eliminar esta categoría?')) return;
        router.delete(route('categories.destroy', id));
    }

    function handleRestore(id: number) {
        router.post(route('categories.restore', id));
    }

    const byType: Record<string, Category[]> = {};
    for (const c of categories) {
        if (!byType[c.type]) byType[c.type] = [];
        byType[c.type].push(c);
    }

    return (
        <Authenticated>
            <Head title="Categorías" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <div className="mb-6 flex items-center justify-between">
                    <h3 className="text-lg font-medium">Categorías de Noticias y Eventos</h3>
                    <Link
                        href={route('categories.create')}
                        className="rounded bg-primary px-3 py-2 text-sm font-semibold text-gold hover:bg-primary/80"
                    >
                        + Nueva categoría
                    </Link>
                </div>

                {Object.entries(byType).map(([type, items]) => (
                    <div key={type} className="mb-8">
                        <h4 className="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">
                            {TYPE_LABELS[type] ?? type}
                        </h4>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="text-gray-500">
                                        <th className="py-2 pr-3 w-10">Pos.</th>
                                        <th className="py-2 pr-4">Nombre</th>
                                        <th className="py-2 pr-4">Slug</th>
                                        <th className="py-2 pr-4">Ícono</th>
                                        <th className="py-2 pr-4">Color</th>
                                        <th className="py-2 pr-4">Tipo</th>
                                        <th className="py-2 pr-4">Estado</th>
                                        <th className="py-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan={8}
                                                className="py-6 text-center text-gray-400"
                                            >
                                                Sin categorías de este tipo.
                                            </td>
                                        </tr>
                                    )}
                                    {items.map(c => (
                                        <tr
                                            key={c.id}
                                            className={`border-t ${c.deleted_at ? 'opacity-40' : ''}`}
                                        >
                                            <td className="py-3 pr-3 text-gray-400">
                                                {c.position}
                                            </td>
                                            <td className="py-3 pr-4 font-medium">{c.name}</td>
                                            <td className="py-3 pr-4 font-mono text-xs text-gray-500">
                                                {c.slug}
                                            </td>
                                            <td className="py-3 pr-4 text-gray-500">
                                                {c.icon ?? '-'}
                                            </td>
                                            <td className="py-3 pr-4">
                                                {c.color ? (
                                                    <span className="inline-flex items-center gap-1">
                                                        <span
                                                            className="inline-block h-4 w-4 rounded border"
                                                            style={{ backgroundColor: c.color }}
                                                        />
                                                        <span className="text-xs text-gray-500">
                                                            {c.color}
                                                        </span>
                                                    </span>
                                                ) : (
                                                    '—'
                                                )}
                                            </td>
                                            <td className="py-3 pr-4">
                                                <span
                                                    className={`inline-block rounded px-2 py-0.5 text-xs font-medium ${TYPE_COLORS[c.type] ?? 'bg-gray-100 text-gray-600'}`}
                                                >
                                                    {TYPE_LABELS[c.type] ?? c.type}
                                                </span>
                                            </td>
                                            <td className="py-3 pr-4">
                                                <span
                                                    className={`inline-block rounded px-2 py-0.5 text-xs font-medium ${c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}
                                                >
                                                    {c.is_active ? 'Activa' : 'Inactiva'}
                                                </span>
                                            </td>
                                            <td className="py-3">
                                                <div className="flex items-center gap-2">
                                                    {!c.deleted_at && (
                                                        <>
                                                            <button
                                                                type="button"
                                                                title="Subir"
                                                                className="text-xs text-gray-500 hover:text-primary"
                                                                onClick={() =>
                                                                    router.post(
                                                                        route(
                                                                            'categories.move-up',
                                                                            c.id,
                                                                        ),
                                                                    )
                                                                }
                                                            >
                                                                ↑
                                                            </button>
                                                            <button
                                                                type="button"
                                                                title="Bajar"
                                                                className="text-xs text-gray-500 hover:text-primary"
                                                                onClick={() =>
                                                                    router.post(
                                                                        route(
                                                                            'categories.move-down',
                                                                            c.id,
                                                                        ),
                                                                    )
                                                                }
                                                            >
                                                                ↓
                                                            </button>
                                                            <Link
                                                                href={route(
                                                                    'categories.edit',
                                                                    c.id,
                                                                )}
                                                                className="text-sm text-primary hover:underline"
                                                            >
                                                                Editar
                                                            </Link>
                                                            <button
                                                                type="button"
                                                                className="text-sm text-red-500 hover:underline"
                                                                onClick={() => handleDelete(c.id)}
                                                            >
                                                                Eliminar
                                                            </button>
                                                        </>
                                                    )}
                                                    {c.deleted_at && (
                                                        <button
                                                            type="button"
                                                            className="text-sm text-green-600 hover:underline"
                                                            onClick={() => handleRestore(c.id)}
                                                        >
                                                            Restaurar
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                ))}

                {categories.length === 0 && (
                    <p className="py-12 text-center text-gray-400">
                        No hay categorías registradas.{' '}
                        <Link href={route('categories.create')} className="text-primary underline">
                            Crear la primera
                        </Link>
                        .
                    </p>
                )}
            </div>
        </Authenticated>
    );
}
