import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useRef, useState } from 'react';

type AssignedPage = {
    id: number;
    page: string;
    position?: number;
};

type CmsBlockItem = {
    id: number;
    name: string;
    slug: string;
    template_key?: string | null;
    zone?: string | null;
    excerpt?: string | null;
    content?: string | null;
    is_active: boolean;
    deleted_at?: string | null;
    updated_at?: string | null;
    revisions?: Array<{ id: number }>;
    pages?: AssignedPage[];
};

type CmsBlockStats = {
    total: number;
    active: number;
    inactive: number;
    trashed: number;
};

type TemplateDefinition = {
    label: string;
    description: string;
    zones: Record<string, string>;
};

function stripHtml(input?: string | null) {
    if (!input) return '';
    return input
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function formatDate(value?: string | null) {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    });
}

export default function Index() {
    const props: any = usePage().props;
    const blocks: CmsBlockItem[] = props.blocks || [];
    const pageLabels: Record<string, string> = props.pages || {};
    const templates: Record<string, TemplateDefinition> = props.templates || {};
    const stats: CmsBlockStats = props.stats || { total: 0, active: 0, inactive: 0, trashed: 0 };
    const initialFilters = props.filters || {};

    const [filters, setFilters] = useState({
        search: initialFilters.search || '',
        status: initialFilters.status || 'all',
    });
    const debounceTimerRef = useRef<NodeJS.Timeout | null>(null);

    const applyFilters = useCallback((updatedFilters: { search: string; status: string }) => {
        const query: Record<string, string> = {};

        if (updatedFilters.search) {
            query.search = updatedFilters.search;
        }

        if (updatedFilters.status && updatedFilters.status !== 'all') {
            query.status = updatedFilters.status;
        }

        router.get(route('cms-blocks.index'), query, {
            preserveScroll: true,
            preserveState: false,
        });
    }, []);

    useEffect(() => {
        return () => {
            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }
        };
    }, []);

    const handleSearchChange = useCallback(
        (value: string) => {
            const updatedFilters = { ...filters, search: value };
            setFilters(updatedFilters);

            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }

            debounceTimerRef.current = setTimeout(() => {
                applyFilters(updatedFilters);
            }, 300);
        },
        [filters, applyFilters],
    );

    const handleFilterChange = useCallback(
        (filterName: 'search' | 'status', value: string) => {
            const updatedFilters = { ...filters, [filterName]: value };
            setFilters(updatedFilters);
            applyFilters(updatedFilters);
        },
        [filters, applyFilters],
    );

    const clearFilters = useCallback(() => {
        setFilters({ search: '', status: 'all' });
        router.get(route('cms-blocks.index'), {}, { preserveScroll: true });
    }, []);

    const hasActiveFilters = filters.search || (filters.status && filters.status !== 'all');

    const restoreBlock = useCallback((blockId: number) => {
        router.post(route('cms-blocks.restore', blockId), undefined, {
            preserveScroll: true,
        });
    }, []);

    const deleteBlock = useCallback((blockId: number) => {
        if (!confirm('Delete this block?')) return;

        router.delete(route('cms-blocks.destroy', blockId), {
            preserveScroll: true,
        });
    }, []);

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">CMS Blocks</h2>}>
            <Head title="CMS Blocks" />

            <div className="space-y-6">
                <div className="grid gap-4 md:grid-cols-4">
                    <div className="rounded-lg border border-border bg-surface p-4 shadow-sm">
                        <div className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                            Total
                        </div>
                        <div className="mt-2 text-2xl font-bold text-primary">{stats.total}</div>
                    </div>
                    <div className="rounded-lg border border-border bg-surface p-4 shadow-sm">
                        <div className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                            Activos
                        </div>
                        <div className="mt-2 text-2xl font-bold text-primary">{stats.active}</div>
                    </div>
                    <div className="rounded-lg border border-border bg-surface p-4 shadow-sm">
                        <div className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                            Inactivos
                        </div>
                        <div className="mt-2 text-2xl font-bold text-primary">{stats.inactive}</div>
                    </div>
                    <div className="rounded-lg border border-border bg-surface p-4 shadow-sm">
                        <div className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                            Eliminados
                        </div>
                        <div className="mt-2 text-2xl font-bold text-primary">{stats.trashed}</div>
                    </div>
                </div>

                <div className="rounded-lg border border-border bg-surface p-6 shadow-sm">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 className="text-lg font-semibold text-primary">
                                Gestión de bloques
                            </h3>
                            <p className="mt-1 text-sm text-text-secondary">
                                Organiza contenido reutilizable por página, estado y zona de uso.
                            </p>
                        </div>

                        <Link
                            href={route('cms-blocks.create')}
                            className="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                        >
                            Crear bloque
                        </Link>
                    </div>

                    <div className="mt-6 grid gap-3 md:grid-cols-3">
                        <div className="md:col-span-2">
                            <label className="mb-1 block text-xs font-medium uppercase tracking-wider text-text-secondary">
                                Búsqueda
                            </label>
                            <input
                                type="text"
                                value={filters.search}
                                onChange={e => handleSearchChange(e.target.value)}
                                placeholder="Buscar por nombre, slug, excerpt o contenido"
                                className="w-full rounded-md border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium uppercase tracking-wider text-text-secondary">
                                Estado
                            </label>
                            <select
                                value={filters.status}
                                onChange={e => handleFilterChange('status', e.target.value)}
                                className="w-full rounded-md border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="all">Todos</option>
                                <option value="active">Activos</option>
                                <option value="inactive">Inactivos</option>
                                <option value="trashed">Eliminados</option>
                            </select>
                        </div>
                    </div>

                    {hasActiveFilters && (
                        <div className="mt-4">
                            <button
                                type="button"
                                onClick={clearFilters}
                                className="rounded-md border border-border px-3 py-2 text-sm font-medium text-text-secondary transition hover:bg-gray-50"
                            >
                                Limpiar filtros
                            </button>
                        </div>
                    )}
                </div>

                <div className="overflow-hidden rounded-lg border border-border bg-surface shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm">
                            <thead className="bg-gray-50 text-xs uppercase tracking-wider text-text-secondary">
                                <tr>
                                    <th className="px-4 py-3 font-medium">Bloque</th>
                                    <th className="px-4 py-3 font-medium">Páginas</th>
                                    <th className="px-4 py-3 font-medium">Estado</th>
                                    <th className="px-4 py-3 font-medium">Revisiones</th>
                                    <th className="px-4 py-3 font-medium">Actualizado</th>
                                    <th className="px-4 py-3 font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {blocks.length > 0 ? (
                                    blocks.map(block => {
                                        const assignedPages = block.pages || [];
                                        const preview = stripHtml(
                                            block.excerpt || block.content || '',
                                        );
                                        const isTrashed = !!block.deleted_at;
                                        const isActive = block.is_active && !isTrashed;
                                        const templateLabel =
                                            templates[block.template_key || 'rich_text']?.label ||
                                            block.template_key ||
                                            'rich_text';

                                        return (
                                            <tr
                                                key={block.id}
                                                className="border-t border-border align-top"
                                            >
                                                <td className="px-4 py-4">
                                                    <div className="space-y-2">
                                                        <div>
                                                            <div className="text-base font-semibold text-primary">
                                                                {block.name}
                                                            </div>
                                                            <div className="mt-2 flex flex-wrap gap-2">
                                                                <code className="inline-flex rounded bg-gray-100 px-2 py-1 text-xs text-text-secondary">
                                                                    {block.slug}
                                                                </code>
                                                                <span className="inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">
                                                                    {templateLabel}
                                                                </span>
                                                                <span className="inline-flex rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-medium text-secondary">
                                                                    {block.zone || 'content'}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        {preview && (
                                                            <p className="max-w-xl text-sm leading-6 text-text-secondary">
                                                                {preview.length > 160
                                                                    ? `${preview.slice(0, 160)}...`
                                                                    : preview}
                                                            </p>
                                                        )}
                                                    </div>
                                                </td>

                                                <td className="px-4 py-4">
                                                    {assignedPages.length > 0 ? (
                                                        <div className="flex flex-wrap gap-2">
                                                            {assignedPages.map(page => (
                                                                <span
                                                                    key={page.id}
                                                                    className="inline-flex rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-medium text-secondary"
                                                                >
                                                                    {pageLabels[page.page] ||
                                                                        page.page}
                                                                </span>
                                                            ))}
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-text-secondary">
                                                            Sin páginas asignadas
                                                        </span>
                                                    )}
                                                </td>

                                                <td className="px-4 py-4">
                                                    {isTrashed ? (
                                                        <span className="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                                            Eliminado
                                                        </span>
                                                    ) : isActive ? (
                                                        <span className="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                            Activo
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                            Inactivo
                                                        </span>
                                                    )}
                                                </td>

                                                <td className="px-4 py-4 text-text-secondary">
                                                    {(block.revisions || []).length}
                                                </td>

                                                <td className="px-4 py-4 text-text-secondary">
                                                    {formatDate(block.updated_at)}
                                                </td>

                                                <td className="px-4 py-4">
                                                    <div className="flex flex-wrap gap-2">
                                                        {isTrashed ? (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    restoreBlock(block.id)
                                                                }
                                                                className="inline-flex items-center rounded-md border border-green-600 px-3 py-2 text-xs font-semibold text-green-700 transition hover:bg-green-50"
                                                            >
                                                                Restaurar
                                                            </button>
                                                        ) : (
                                                            <>
                                                                <Link
                                                                    href={route(
                                                                        'cms-blocks.edit',
                                                                        block.id,
                                                                    )}
                                                                    className="inline-flex items-center rounded-md border border-border px-3 py-2 text-xs font-semibold text-primary transition hover:bg-gray-50"
                                                                >
                                                                    Editar
                                                                </Link>

                                                                <Link
                                                                    href={route(
                                                                        'cms-blocks.revisions.index',
                                                                        block.id,
                                                                    )}
                                                                    className="inline-flex items-center rounded-md border border-border px-3 py-2 text-xs font-semibold text-primary transition hover:bg-gray-50"
                                                                >
                                                                    Revisiones
                                                                </Link>

                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        deleteBlock(block.id)
                                                                    }
                                                                    className="inline-flex items-center rounded-md border border-red-600 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50"
                                                                >
                                                                    Eliminar
                                                                </button>
                                                            </>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                ) : (
                                    <tr>
                                        <td
                                            className="px-4 py-10 text-center text-sm text-text-secondary"
                                            colSpan={6}
                                        >
                                            No hay bloques con esos filtros.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
