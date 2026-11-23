import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage, useForm, router } from '@inertiajs/react';
import { useState, useCallback, useRef } from 'react';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import DangerButton from '@/Components/DangerButton';
import SecondaryButton from '@/Components/SecondaryButton';

type Page = {
    id: number;
    title: string;
    slug: string;
    description?: string;
    is_published: boolean;
    published_at?: string | null;
    updated_at?: string;
    created_at?: string;
    deleted_at?: string | null;
    creator?: { id: number; name: string } | null;
};

export default function Index() {
    const props: any = usePage().props;
    const pages = props.pages || { data: [], links: [], meta: {} };
    const initialFilters = props.filters || {};
    const [deleting, setDeleting] = useState<Page | null>(null);
    const form = useForm();

    // Filter state
    const [filters, setFilters] = useState({
        search: initialFilters.search || '',
        published: initialFilters.published || 'all',
    });

    const debounceTimerRef = useRef<NodeJS.Timeout | null>(null);

    // Debounced search handler
    const handleSearchChange = useCallback(
        (value: string) => {
            setFilters(prev => ({ ...prev, search: value }));

            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }

            debounceTimerRef.current = setTimeout(() => {
                applyFilters({ ...filters, search: value });
            }, 300);
        },
        [filters],
    );

    // Apply filters
    const applyFilters = useCallback((updatedFilters: any) => {
        const query: any = {};

        if (updatedFilters.search) query.search = updatedFilters.search;
        if (updatedFilters.published && updatedFilters.published !== 'all')
            query.published = updatedFilters.published;

        router.get(route('pages.index'), query, { preserveState: false });
    }, []);

    // Handle filter changes
    const handleFilterChange = useCallback(
        (filterName: string, value: string) => {
            const updatedFilters = { ...filters, [filterName]: value };
            setFilters(updatedFilters);
            applyFilters(updatedFilters);
        },
        [filters, applyFilters],
    );

    // Clear filters
    const clearFilters = useCallback(() => {
        setFilters({
            search: '',
            published: 'all',
        });
        router.get(route('pages.index'), {}, { preserveState: false });
    }, []);

    // Delete handler
    const handleDelete = (page: Page) => {
        setDeleting(page);
    };

    const confirmDelete = () => {
        if (!deleting) return;

        if (deleting.deleted_at) {
            // Restore deleted page
            form.post(route('pages.restore', deleting.id), {
                onSuccess: () => setDeleting(null),
            });
        } else {
            // Soft-delete page
            form.delete(route('pages.destroy', deleting.id), {
                onSuccess: () => setDeleting(null),
            });
        }
    };

    // Toggle publish
    const togglePublish = (page: Page) => {
        const endpoint = page.is_published ? 'pages.unpublish' : 'pages.publish';
        form.post(route(endpoint, page.id), {
            onSuccess: () => {
                // Page will be refreshed
            },
        });
    };

    const hasActiveFilters = filters.search || (filters.published && filters.published !== 'all');

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Páginas Estáticas</h2>}>
            <Head title="Páginas Estáticas" />

            <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                {/* Header */}
                <div className="mb-6 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <h3 className="text-lg font-medium text-primary sr-only">
                            Páginas Estáticas
                        </h3>
                        {pages.meta?.total > 0 && (
                            <span className="inline-block bg-primary text-white rounded-full px-3 py-1 text-xs font-medium">
                                {pages.meta.total} {pages.meta.total === 1 ? 'página' : 'páginas'}
                            </span>
                        )}
                    </div>
                    <Link
                        href={route('pages.create')}
                        className="rounded bg-primary text-white px-4 py-2 text-sm font-medium hover:opacity-90"
                    >
                        Crear Página
                    </Link>
                </div>

                {/* Filters Form */}
                <div className="mb-6 p-4 bg-gray-50 rounded-lg border border-border">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                        {/* Search */}
                        <div className="md:col-span-2">
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Búsqueda
                            </label>
                            <input
                                type="text"
                                placeholder="Título o slug..."
                                value={filters.search}
                                onChange={e => handleSearchChange(e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        {/* Status */}
                        <div>
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Estado
                            </label>
                            <select
                                value={filters.published}
                                onChange={e => handleFilterChange('published', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="all">Todas</option>
                                <option value="published">Publicadas</option>
                                <option value="draft">Borradores</option>
                            </select>
                        </div>
                    </div>

                    {/* Clear Filters */}
                    {hasActiveFilters && (
                        <div className="mt-3">
                            <button
                                onClick={clearFilters}
                                className="px-3 py-2 text-sm border border-gray-300 rounded bg-white text-gray-700 hover:bg-gray-100 font-medium transition"
                            >
                                Limpiar Filtros
                            </button>
                        </div>
                    )}
                </div>

                {/* Table */}
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="text-text-secondary bg-gray-50 border-b border-border">
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Título
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Slug
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Estado
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Actualizado
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {pages.data && pages.data.length > 0 ? (
                                pages.data.map((page: Page) => (
                                    <tr
                                        key={page.id}
                                        className="border-b border-border hover:bg-gray-50 transition"
                                    >
                                        <td className="py-4 px-4">
                                            <div>
                                                <p className="font-medium text-primary">
                                                    {page.title}
                                                </p>
                                                {page.creator && (
                                                    <p className="text-xs text-text-secondary">
                                                        por {page.creator.name}
                                                    </p>
                                                )}
                                            </div>
                                        </td>
                                        <td className="py-4 px-4">
                                            <code className="bg-gray-100 px-2 py-1 rounded text-xs">
                                                {page.slug}
                                            </code>
                                        </td>
                                        <td className="py-4 px-4">
                                            <div className="flex items-center gap-2">
                                                {page.deleted_at ? (
                                                    <span className="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                                        Eliminada
                                                    </span>
                                                ) : page.is_published ? (
                                                    <span className="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                                        Publicada
                                                    </span>
                                                ) : (
                                                    <span className="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                                                        Borrador
                                                    </span>
                                                )}
                                            </div>
                                        </td>
                                        <td className="py-4 px-4 text-xs text-text-secondary">
                                            {page.updated_at
                                                ? new Date(page.updated_at).toLocaleDateString(
                                                      'es-ES',
                                                  )
                                                : '-'}
                                        </td>
                                        <td className="py-4 px-4">
                                            <div className="flex gap-2 flex-wrap">
                                                {!page.deleted_at && (
                                                    <>
                                                        <Link
                                                            href={route('pages.edit', page.id)}
                                                            className="text-xs px-2 py-1 border border-border rounded hover:bg-gray-100 text-primary font-medium"
                                                        >
                                                            Editar
                                                        </Link>
                                                        <button
                                                            onClick={() => togglePublish(page)}
                                                            className={`text-xs px-2 py-1 border rounded font-medium transition ${
                                                                page.is_published
                                                                    ? 'border-orange-300 text-orange-700 hover:bg-orange-50'
                                                                    : 'border-green-300 text-green-700 hover:bg-green-50'
                                                            }`}
                                                        >
                                                            {page.is_published
                                                                ? 'Despublicar'
                                                                : 'Publicar'}
                                                        </button>
                                                    </>
                                                )}
                                                <button
                                                    onClick={() => handleDelete(page)}
                                                    className="text-xs px-2 py-1 border border-red-300 rounded hover:bg-red-50 text-red-700 font-medium"
                                                >
                                                    {page.deleted_at ? 'Restaurar' : 'Eliminar'}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={5} className="py-8 px-4 text-center text-gray-500">
                                        {filters.search || filters.published !== 'all'
                                            ? 'No se encontraron páginas con los filtros aplicados.'
                                            : 'No hay páginas todavía. '}
                                        <Link
                                            href={route('pages.create')}
                                            className="text-primary font-medium hover:underline"
                                        >
                                            Crear la primera
                                        </Link>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div className="mt-4 flex items-center justify-end gap-2">
                    {pages.links &&
                        pages.links.map((link: any, idx: number) => (
                            <Link
                                key={idx}
                                href={link.url ?? '#'}
                                className={`px-3 py-1 rounded ${link.active ? 'bg-primary text-white' : 'text-gray-600 bg-white border'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                </div>
            </div>

            {/* Delete Confirmation Modal */}
            <Modal show={!!deleting} onClose={() => setDeleting(null)}>
                <div className="p-6">
                    <h3 className="text-lg font-medium text-primary mb-4">
                        {deleting?.deleted_at ? 'Restaurar Página' : 'Eliminar Página'}
                    </h3>
                    <p className="text-gray-600 mb-4">
                        {deleting?.deleted_at
                            ? `¿Restaurar la página "${deleting.title}"?`
                            : `¿Estás seguro de que deseas eliminar la página "${deleting?.title}"? Esta acción se puede deshacer.`}
                    </p>
                    <div className="flex gap-3 justify-end">
                        <SecondaryButton onClick={() => setDeleting(null)}>
                            Cancelar
                        </SecondaryButton>
                        {deleting?.deleted_at ? (
                            <PrimaryButton onClick={confirmDelete} disabled={form.processing}>
                                {form.processing ? 'Restaurando...' : 'Restaurar'}
                            </PrimaryButton>
                        ) : (
                            <DangerButton onClick={confirmDelete} disabled={form.processing}>
                                {form.processing ? 'Eliminando...' : 'Eliminar'}
                            </DangerButton>
                        )}
                    </div>
                </div>
            </Modal>
        </Authenticated>
    );
}
