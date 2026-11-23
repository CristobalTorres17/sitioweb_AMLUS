import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import Modal from '@/Components/Modal';

type EventItem = {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    event_date?: string | null;
    event_date_end?: string | null;
    location?: string | null;
    status?: string | null;
    is_featured?: boolean;
    published_at?: string | null;
    deleted_at?: string | null;
    gallery_count?: number;
    comments_count?: number;
    author?: { id: number; name: string } | null;
};

function statusBadge(status?: string | null) {
    if (status === 'published') return 'bg-emerald-100 text-emerald-700';
    if (status === 'archived') return 'bg-slate-100 text-slate-700';
    return 'bg-amber-100 text-amber-700';
}

export default function Index() {
    const props: any = usePage().props;
    const events = props.events || { data: [], links: [], meta: {} };
    const initialFilters = props.filters || {};
    const [deleting, setDeleting] = useState<EventItem | null>(null);

    const [filters, setFilters] = useState({
        search: initialFilters.search || '',
        status: initialFilters.status || 'all',
        date_from: initialFilters.date_from || '',
        date_to: initialFilters.date_to || '',
        featured: initialFilters.featured || 'all',
        sort: initialFilters.sort || 'event_date',
    });

    const debounceTimerRef = useRef<number | null>(null);

    const applyFilters = useCallback((updatedFilters: typeof filters) => {
        const query: Record<string, string> = {};

        if (updatedFilters.search) query.search = updatedFilters.search;
        if (updatedFilters.status && updatedFilters.status !== 'all')
            query.status = updatedFilters.status;
        if (updatedFilters.date_from) query.date_from = updatedFilters.date_from;
        if (updatedFilters.date_to) query.date_to = updatedFilters.date_to;
        if (updatedFilters.featured && updatedFilters.featured !== 'all')
            query.featured = updatedFilters.featured;
        if (updatedFilters.sort && updatedFilters.sort !== 'event_date')
            query.sort = updatedFilters.sort;

        router.get(route('events.index'), query, {
            preserveState: false,
            preserveScroll: true,
        });
    }, []);

    const handleSearchChange = useCallback(
        (value: string) => {
            const updated = { ...filters, search: value };
            setFilters(updated);

            if (debounceTimerRef.current) {
                window.clearTimeout(debounceTimerRef.current);
            }

            debounceTimerRef.current = window.setTimeout(() => {
                applyFilters(updated);
            }, 300);
        },
        [filters, applyFilters],
    );

    const handleFilterChange = useCallback(
        (key: keyof typeof filters, value: string) => {
            const updated = { ...filters, [key]: value };
            setFilters(updated);
            applyFilters(updated);
        },
        [filters, applyFilters],
    );

    const clearFilters = useCallback(() => {
        const reset = {
            search: '',
            status: 'all',
            date_from: '',
            date_to: '',
            featured: 'all',
            sort: 'event_date',
        };
        setFilters(reset);
        router.get(route('events.index'), {}, { preserveState: false, preserveScroll: true });
    }, []);

    const hasActiveFilters = useMemo(
        () =>
            filters.search ||
            (filters.status && filters.status !== 'all') ||
            filters.date_from ||
            filters.date_to ||
            (filters.featured && filters.featured !== 'all') ||
            (filters.sort && filters.sort !== 'event_date'),
        [filters],
    );

    return (
        <Authenticated header={<h2 className="text-xl font-semibold text-primary">Eventos</h2>}>
            <Head title="Eventos" />

            <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                <div className="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h3 className="text-lg font-medium text-primary">Eventos</h3>
                        <p className="text-sm text-text-secondary">
                            Publica actividades, jornadas y actos institucionales con galería.
                        </p>
                    </div>
                    <Link
                        href={route('events.create')}
                        className="rounded bg-primary text-gold px-4 py-2 text-sm font-medium hover:opacity-90"
                    >
                        Crear evento
                    </Link>
                </div>

                <div className="mb-6 rounded-lg border border-border bg-gray-50 p-4">
                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-6">
                        <div className="lg:col-span-2">
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Búsqueda
                            </label>
                            <input
                                type="text"
                                value={filters.search}
                                onChange={e => handleSearchChange(e.target.value)}
                                placeholder="Título, lugar, organizador..."
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Estado
                            </label>
                            <select
                                value={filters.status}
                                onChange={e => handleFilterChange('status', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="all">Todos</option>
                                <option value="published">Publicado</option>
                                <option value="draft">Borrador</option>
                                <option value="archived">Archivado</option>
                            </select>
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Destacados
                            </label>
                            <select
                                value={filters.featured}
                                onChange={e => handleFilterChange('featured', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="all">Todos</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <div>
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Ordenar
                            </label>
                            <select
                                value={filters.sort}
                                onChange={e => handleFilterChange('sort', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="event_date">Fecha ↓</option>
                                <option value="created_at">Creación ↓</option>
                                <option value="title">Título A→Z</option>
                            </select>
                        </div>
                    </div>

                    <div className="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Desde
                            </label>
                            <input
                                type="date"
                                value={filters.date_from}
                                onChange={e => handleFilterChange('date_from', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-text-secondary">
                                Hasta
                            </label>
                            <input
                                type="date"
                                value={filters.date_to}
                                onChange={e => handleFilterChange('date_to', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>
                        <div className="flex items-end">
                            {hasActiveFilters && (
                                <button
                                    type="button"
                                    onClick={clearFilters}
                                    className="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    Limpiar filtros
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="overflow-x-auto rounded-lg border border-border bg-white">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="border-b border-border bg-gray-50 text-xs uppercase tracking-wider text-text-secondary">
                                <th className="px-4 py-3">Evento</th>
                                <th className="px-4 py-3">Fecha</th>
                                <th className="px-4 py-3">Ubicación</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3">Galería</th>
                                <th className="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {events.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className="px-4 py-8 text-center text-text-secondary"
                                    >
                                        {hasActiveFilters
                                            ? 'No se encontraron eventos con los filtros aplicados.'
                                            : 'No hay eventos creados todavía.'}
                                    </td>
                                </tr>
                            )}

                            {events.data.map((event: EventItem) => (
                                <tr
                                    key={event.id}
                                    className="border-b border-border/80 hover:bg-gray-50/70"
                                >
                                    <td className="px-4 py-4 align-top">
                                        <div className="font-medium text-primary">
                                            {event.title}
                                        </div>
                                        <div className="mt-1 text-xs text-text-secondary">
                                            {event.author?.name
                                                ? `Por ${event.author.name}`
                                                : 'Sin autor'}
                                        </div>
                                        {event.description && (
                                            <div className="mt-2 max-w-md text-xs text-text-secondary">
                                                {event.description}
                                            </div>
                                        )}
                                        {event.is_featured && (
                                            <span className="mt-2 inline-flex rounded-full bg-indigo-100 px-2 py-1 text-[11px] font-medium text-indigo-700">
                                                Destacado
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-4 text-text-secondary align-top">
                                        <div>
                                            {event.event_date
                                                ? new Date(event.event_date).toLocaleDateString(
                                                      'es-ES',
                                                  )
                                                : '-'}
                                        </div>
                                        <div className="mt-1 text-xs text-text-secondary">
                                            {event.event_date_end
                                                ? `Fin: ${new Date(event.event_date_end).toLocaleDateString('es-ES')}`
                                                : ''}
                                        </div>
                                    </td>
                                    <td className="px-4 py-4 text-text-secondary align-top">
                                        {event.location || '-'}
                                    </td>
                                    <td className="px-4 py-4 align-top">
                                        <span
                                            className={`inline-flex rounded-full px-3 py-1 text-xs font-medium ${statusBadge(event.status)}`}
                                        >
                                            {event.status || 'draft'}
                                        </span>
                                        <div className="mt-2 text-xs text-text-secondary">
                                            {event.deleted_at ? 'Eliminado' : 'Activo'}
                                        </div>
                                    </td>
                                    <td className="px-4 py-4 text-text-secondary align-top">
                                        <div>{event.gallery_count ?? 0} fotos</div>
                                        <div>{event.comments_count ?? 0} comentarios</div>
                                    </td>
                                    <td className="px-4 py-4 align-top">
                                        <div className="flex flex-wrap gap-3">
                                            <Link
                                                href={route('events.edit', event.id)}
                                                className="font-medium text-primary hover:text-primary/80"
                                            >
                                                Editar
                                            </Link>
                                            {event.deleted_at ? (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        router.post(
                                                            route('events.restore', event.id),
                                                        )
                                                    }
                                                    className="font-medium text-emerald-600 hover:text-emerald-700"
                                                >
                                                    Restaurar
                                                </button>
                                            ) : (
                                                <button
                                                    type="button"
                                                    onClick={() => setDeleting(event)}
                                                    className="font-medium text-red-600 hover:text-red-700"
                                                >
                                                    Eliminar
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="mt-4 flex flex-wrap items-center justify-end gap-2">
                    {events.links?.map((link: any, index: number) => (
                        <Link
                            key={index}
                            href={link.url ?? '#'}
                            className={`rounded px-3 py-1 text-sm ${link.active ? 'bg-primary text-white' : 'border border-border bg-white text-gray-600 hover:bg-gray-50'}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            </div>

            <Modal show={!!deleting} onClose={() => setDeleting(null)} maxWidth="sm">
                <div className="p-6">
                    <h3 className="text-lg font-medium text-primary">Eliminar evento</h3>
                    <p className="mt-2 text-sm text-text-secondary">
                        ¿Seguro que quieres eliminar <strong>{deleting?.title}</strong>? El registro
                        quedará en papelera.
                    </p>

                    <div className="mt-6 flex justify-end gap-2">
                        <button
                            onClick={() => setDeleting(null)}
                            className="rounded bg-gray-100 px-4 py-2 font-medium text-primary hover:bg-gray-200"
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={() => {
                                if (!deleting) return;
                                router.delete(route('events.destroy', deleting.id), {
                                    onSuccess: () => setDeleting(null),
                                });
                            }}
                            className="rounded bg-red-600 px-4 py-2 font-medium text-white hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </Modal>
        </Authenticated>
    );
}
