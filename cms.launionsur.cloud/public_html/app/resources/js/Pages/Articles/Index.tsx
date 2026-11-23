import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage, useForm, router } from '@inertiajs/react';
import { useState, useCallback, useEffect, useRef } from 'react';
import Modal from '@/Components/Modal';

type Article = {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    is_published: boolean;
    published_at?: string | null;
    category?: { id: number; name: string } | null;
    deleted_at?: string | null;
};

type Category = {
    id: number;
    name: string;
};

export default function Index() {
    const props: any = usePage().props;
    const articles = props.articles || { data: [], links: [], meta: {} };
    const categories = props.categories || [];
    const initialFilters = props.filters || {};
    const [deleting, setDeleting] = useState<{ id: number; title: string } | null>(null);
    const form = useForm();

    // Filter state
    const [filters, setFilters] = useState({
        search: initialFilters.search || '',
        status: initialFilters.status || 'all',
        date_from: initialFilters.date_from || '',
        date_to: initialFilters.date_to || '',
        category_id: initialFilters.category_id || '',
        sort: initialFilters.sort || 'published_at',
    });

    const debounceTimerRef = useRef<NodeJS.Timeout | null>(null);

    // Debounced search handler
    const handleSearchChange = useCallback(
        (value: string) => {
            setFilters(prev => ({ ...prev, search: value }));

            // Clear previous timer
            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }

            // Set new timer
            debounceTimerRef.current = setTimeout(() => {
                applyFilters({ ...filters, search: value });
            }, 300);
        },
        [filters],
    );

    // Apply filters by updating URL params
    const applyFilters = useCallback((updatedFilters: any) => {
        const query: any = {};

        // Only include non-empty filters in URL
        if (updatedFilters.search) query.search = updatedFilters.search;
        if (updatedFilters.status && updatedFilters.status !== 'all')
            query.status = updatedFilters.status;
        if (updatedFilters.date_from) query.date_from = updatedFilters.date_from;
        if (updatedFilters.date_to) query.date_to = updatedFilters.date_to;
        if (updatedFilters.category_id) query.category_id = updatedFilters.category_id;
        if (updatedFilters.sort && updatedFilters.sort !== 'published_at')
            query.sort = updatedFilters.sort;

        // Update URL with filters
        router.get(route('articles.index'), query, { preserveState: false });
    }, []);

    // Handle non-search filter changes
    const handleFilterChange = useCallback(
        (filterName: string, value: string) => {
            const updatedFilters = { ...filters, [filterName]: value };
            setFilters(updatedFilters);
            applyFilters(updatedFilters);
        },
        [filters, applyFilters],
    );

    // Clear all filters
    const clearFilters = useCallback(() => {
        setFilters({
            search: '',
            status: 'all',
            date_from: '',
            date_to: '',
            category_id: '',
            sort: 'published_at',
        });
        router.get(route('articles.index'), {}, { preserveState: false });
    }, []);

    // Check if any filters are active
    const hasActiveFilters =
        filters.search ||
        (filters.status && filters.status !== 'all') ||
        filters.date_from ||
        filters.date_to ||
        filters.category_id ||
        (filters.sort && filters.sort !== 'published_at');

    return (
        <Authenticated>
            <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                {/* Header */}
                <div className="mb-6 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <h3 className="text-lg font-medium text-primary">Artículos</h3>
                        {articles.meta?.total > 0 && (
                            <span className="inline-block bg-primary text-white rounded-full px-3 py-1 text-xs font-medium">
                                {articles.meta.total}{' '}
                                {articles.meta.total === 1 ? 'artículo' : 'artículos'}
                            </span>
                        )}
                    </div>
                    <Link
                        href={route('articles.create')}
                        className="rounded bg-primary text-gold px-4 py-2 text-sm font-medium hover:opacity-90"
                    >
                        Crear
                    </Link>
                </div>

                {/* Filters Form */}
                <div className="mb-6 p-4 bg-gray-50 rounded-lg border border-border">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                        {/* Search */}
                        <div className="lg:col-span-2">
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Búsqueda
                            </label>
                            <input
                                type="text"
                                placeholder="Título, excerpt, contenido..."
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
                                value={filters.status}
                                onChange={e => handleFilterChange('status', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="all">Todos</option>
                                <option value="published">Publicado</option>
                                <option value="draft">Borrador</option>
                            </select>
                        </div>

                        {/* Category */}
                        <div>
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Categoría
                            </label>
                            <select
                                value={filters.category_id}
                                onChange={e => handleFilterChange('category_id', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="">Todas</option>
                                {categories.map((cat: Category) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Sort */}
                        <div>
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Ordenar
                            </label>
                            <select
                                value={filters.sort}
                                onChange={e => handleFilterChange('sort', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="published_at">Publicación ↓</option>
                                <option value="created_at">Creación ↓</option>
                                <option value="title">Título A→Z</option>
                            </select>
                        </div>
                    </div>

                    {/* Date Range - Second Row */}
                    <div className="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                        {/* Date From */}
                        <div>
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Desde
                            </label>
                            <input
                                type="date"
                                value={filters.date_from}
                                onChange={e => handleFilterChange('date_from', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        {/* Date To */}
                        <div>
                            <label className="block text-xs font-medium text-text-secondary mb-1">
                                Hasta
                            </label>
                            <input
                                type="date"
                                value={filters.date_to}
                                onChange={e => handleFilterChange('date_to', e.target.value)}
                                className="w-full px-3 py-2 text-sm border border-border rounded bg-white text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        {/* Clear Filters */}
                        {hasActiveFilters && (
                            <div className="flex items-end">
                                <button
                                    onClick={clearFilters}
                                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded bg-white text-gray-700 hover:bg-gray-100 font-medium transition"
                                >
                                    Limpiar Filtros
                                </button>
                            </div>
                        )}
                    </div>
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
                                    Categoría
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Publicado
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Estado
                                </th>
                                <th className="py-3 px-4 font-medium text-xs uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {articles.data.length === 0 && (
                                <tr>
                                    <td colSpan={5} className="py-8 text-center text-text-muted">
                                        {hasActiveFilters
                                            ? 'No se encontraron artículos con los filtros aplicados.'
                                            : 'No hay artículos.'}
                                    </td>
                                </tr>
                            )}
                            {articles.data.map((a: Article) => (
                                <tr key={a.id} className="border-b border-border hover:bg-gray-50">
                                    <td className="py-4 px-4">
                                        <div className="font-medium text-primary">{a.title}</div>
                                        {a.excerpt && (
                                            <div className="text-xs text-text-secondary mt-1">
                                                {a.excerpt}
                                            </div>
                                        )}
                                    </td>
                                    <td className="py-4 px-4 text-text-secondary">
                                        {a.category?.name ?? '-'}
                                    </td>
                                    <td className="py-4 px-4 text-text-secondary text-xs">
                                        {a.published_at
                                            ? new Date(a.published_at).toLocaleDateString()
                                            : '-'}
                                    </td>
                                    <td className="py-4 px-4">
                                        <span
                                            className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${
                                                a.is_published
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-yellow-100 text-yellow-700'
                                            }`}
                                        >
                                            {a.is_published ? 'Published' : 'Draft'}
                                        </span>
                                    </td>
                                    <td className="py-4 px-4">
                                        <div className="flex gap-3">
                                            <Link
                                                href={route('articles.edit', a.id)}
                                                className="text-sm text-primary hover:text-primary/80 font-medium"
                                            >
                                                Edit
                                            </Link>
                                            {a.deleted_at ? (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        form.post(route('articles.restore', a.id), {
                                                            onSuccess: () => {},
                                                        })
                                                    }
                                                    className="text-sm text-green-600 hover:text-green-700 font-medium"
                                                >
                                                    Restore
                                                </button>
                                            ) : (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setDeleting({ id: a.id, title: a.title })
                                                    }
                                                    className="text-sm text-red-600 hover:text-red-700 font-medium"
                                                >
                                                    Delete
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div className="mt-4 flex items-center justify-end gap-2">
                    {articles.links &&
                        articles.links.map((link: any, idx: number) => (
                            <Link
                                key={idx}
                                href={link.url ?? '#'}
                                className={`px-3 py-1 rounded ${link.active ? 'bg-primary text-white' : 'text-gray-600 bg-white border'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                </div>
            </div>

            {/* Delete Modal */}
            <Modal show={!!deleting} onClose={() => setDeleting(null)} maxWidth="sm">
                <div className="p-6">
                    <h3 className="text-lg font-medium text-primary">Confirm delete</h3>
                    <p className="mt-2 text-sm text-text-secondary">
                        Are you sure you want to delete <strong>{deleting?.title}</strong>? This
                        action cannot be undone.
                    </p>

                    <div className="mt-6 flex justify-end gap-2">
                        <button
                            onClick={() => setDeleting(null)}
                            className="px-4 py-2 rounded bg-gray-100 text-primary hover:bg-gray-200 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={() => {
                                if (!deleting) return;
                                form.delete(route('articles.destroy', deleting.id), {
                                    onSuccess: () => setDeleting(null),
                                });
                            }}
                            className="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-medium"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </Modal>
        </Authenticated>
    );
}
