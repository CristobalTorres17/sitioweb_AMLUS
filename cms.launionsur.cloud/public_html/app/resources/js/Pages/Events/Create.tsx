import Authenticated from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import { Head, router, useForm } from '@inertiajs/react';
import { FormEventHandler, useMemo, useState } from 'react';
import RichTextEditor from '@/Components/RichTextEditor';
import MediaPickerModal from '@/Components/MediaPickerModal';
import type { MediaRef as SelectedMedia } from '@/types/media';

function toDatetimeLocal(value?: string | null) {
    if (!value) return '';
    return value.slice(0, 16);
}

export default function Create({
    categories,
}: {
    categories: { id: number; name: string; slug: string; color?: string; icon?: string }[];
}) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        slug: '',
        description: '',
        content: '',
        event_date: '',
        event_date_end: '',
        location: '',
        organizer: '',
        featured_image_id: '',
        status: 'draft',
        published_at: '',
        is_featured: false,
        meta_title: '',
        meta_description: '',
        category_id: '',
    });

    const [featuredImage, setFeaturedImage] = useState<SelectedMedia | null>(null);
    const [pickerOpen, setPickerOpen] = useState(false);
    const [localErrors, setLocalErrors] = useState<{ featured_image_id?: string }>({});

    const selectedPublishedAt = useMemo(
        () => toDatetimeLocal(data.published_at),
        [data.published_at],
    );

    const submit: FormEventHandler = e => {
        e.preventDefault();
        if (!data.featured_image_id) {
            setLocalErrors({ featured_image_id: 'Se requiere una imagen de portada.' });
            return;
        }
        setLocalErrors({});
        post(route('events.store'));
    };

    return (
        <Authenticated
            header={<h2 className="text-xl font-semibold text-primary">Crear evento</h2>}
        >
            <Head title="Crear evento" />

            <div className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label className="block text-sm font-medium text-primary">
                                Título *
                            </label>
                            <input
                                value={data.title}
                                onChange={e => setData('title', e.target.value)}
                                className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                placeholder="Nombre del evento"
                            />
                            <InputError message={errors.title} className="mt-1" />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-primary">Slug</label>
                            <input
                                value={data.slug}
                                onChange={e => setData('slug', e.target.value)}
                                className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                placeholder="opcional"
                            />
                            <InputError message={errors.slug} className="mt-1" />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-primary">
                                Descripción
                            </label>
                            <textarea
                                value={data.description}
                                onChange={e => setData('description', e.target.value)}
                                className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                rows={3}
                            />
                            <InputError message={errors.description} className="mt-1" />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-primary">
                                Contenido
                            </label>
                            <RichTextEditor
                                value={data.content}
                                onChange={html => setData('content', html)}
                                placeholder="Describe el evento, agenda, contexto o requisitos..."
                                className="mt-2"
                            />
                            <InputError message={errors.content} className="mt-1" />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Fecha y hora *
                                </label>
                                <input
                                    type="datetime-local"
                                    value={data.event_date}
                                    onChange={e => setData('event_date', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                />
                                <InputError message={errors.event_date} className="mt-1" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Fin del evento
                                </label>
                                <input
                                    type="datetime-local"
                                    value={data.event_date_end}
                                    onChange={e => setData('event_date_end', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                />
                                <InputError message={errors.event_date_end} className="mt-1" />
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Ubicación
                                </label>
                                <input
                                    value={data.location}
                                    onChange={e => setData('location', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    placeholder="Parque central, salón de actos..."
                                />
                                <InputError message={errors.location} className="mt-1" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Organizador
                                </label>
                                <input
                                    value={data.organizer}
                                    onChange={e => setData('organizer', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    placeholder="Alcaldía, comunidad, dirección..."
                                />
                                <InputError message={errors.organizer} className="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-primary">
                                Categoría
                            </label>
                            <select
                                value={data.category_id}
                                onChange={e => setData('category_id', e.target.value)}
                                className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                            >
                                <option value="">Sin categoría</option>
                                {categories.map(cat => (
                                    <option key={cat.id} value={String(cat.id)}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                            <InputError message={errors.category_id} className="mt-1" />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Meta title
                                </label>
                                <input
                                    value={data.meta_title}
                                    onChange={e => setData('meta_title', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                />
                                <InputError message={errors.meta_title} className="mt-1" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Meta description
                                </label>
                                <textarea
                                    value={data.meta_description}
                                    onChange={e => setData('meta_description', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    rows={2}
                                />
                                <InputError message={errors.meta_description} className="mt-1" />
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-3">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Estado
                                </label>
                                <select
                                    value={data.status}
                                    onChange={e => setData('status', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                >
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                    <option value="archived">Archivado</option>
                                </select>
                                <InputError message={errors.status} className="mt-1" />
                            </div>

                            <div className="flex items-end">
                                <label className="flex items-center gap-2 text-sm font-medium text-primary">
                                    <input
                                        type="checkbox"
                                        checked={data.is_featured}
                                        onChange={e => setData('is_featured', e.target.checked)}
                                        className="rounded border-border text-primary focus:ring-primary"
                                    />
                                    Destacado
                                </label>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Publicado el
                                </label>
                                <input
                                    type="datetime-local"
                                    value={selectedPublishedAt}
                                    onChange={e => setData('published_at', e.target.value)}
                                    className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                />
                                <InputError message={errors.published_at} className="mt-1" />
                            </div>
                        </div>

                        <div className="rounded-lg border border-border bg-gray-50 p-4">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <div className="text-sm font-medium text-primary">
                                        Imagen destacada
                                    </div>
                                    <p className="text-xs text-text-secondary">
                                        Se puede seleccionar desde la librería de medios existente.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setPickerOpen(true)}
                                    className="rounded bg-primary/10 px-3 py-2 text-sm font-medium text-primary hover:bg-primary/15"
                                >
                                    Seleccionar portada
                                </button>
                            </div>

                            {featuredImage ? (
                                <div className="mt-4 flex items-center gap-4 rounded border border-border bg-white p-3">
                                    <img
                                        src={featuredImage.url}
                                        alt={featuredImage.filename}
                                        className="h-20 w-28 rounded object-cover"
                                    />
                                    <div className="min-w-0">
                                        <div className="truncate text-sm font-medium text-primary">
                                            {featuredImage.filename}
                                        </div>
                                        <div className="text-xs text-text-secondary">
                                            ID #{featuredImage.id}
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setFeaturedImage(null);
                                                setData('featured_image_id', '');
                                            }}
                                            className="mt-2 text-xs font-medium text-red-600 hover:text-red-700"
                                        >
                                            Quitar portada
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <div className="mt-4 rounded border border-dashed border-border bg-white p-4 text-sm text-text-secondary">
                                    No se ha elegido portada.
                                </div>
                            )}
                            {(localErrors.featured_image_id || errors.featured_image_id) && (
                                <div className="mt-2 text-sm text-red-600">
                                    {localErrors.featured_image_id || errors.featured_image_id}
                                </div>
                            )}
                        </div>

                        <div className="flex items-center gap-3 border-t border-border pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded bg-primary px-4 py-2 font-medium text-gold hover:opacity-90 disabled:opacity-50"
                            >
                                {processing ? 'Guardando...' : 'Guardar evento'}
                            </button>
                            <button
                                type="button"
                                onClick={() => router.visit(route('events.index'))}
                                className="rounded border border-border bg-white px-4 py-2 font-medium text-primary hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                    <h3 className="text-lg font-medium text-primary">Vista previa</h3>
                    <div className="mt-4 space-y-4 rounded-xl border border-border bg-white p-4">
                        <div className="text-xs uppercase tracking-wider text-text-secondary">
                            {data.status}
                        </div>
                        <div className="text-2xl font-semibold text-primary">
                            {data.title || 'Título del evento'}
                        </div>
                        <p className="text-sm text-text-secondary">
                            {data.description || 'Describe el evento en una línea para el resumen.'}
                        </p>

                        <div className="grid gap-3 text-sm text-text-secondary">
                            <div>
                                <strong className="text-primary">Fecha:</strong>{' '}
                                {data.event_date || 'No definida'}
                            </div>
                            <div>
                                <strong className="text-primary">Ubicación:</strong>{' '}
                                {data.location || 'Sin ubicación'}
                            </div>
                            <div>
                                <strong className="text-primary">Organizador:</strong>{' '}
                                {data.organizer || 'Sin organizador'}
                            </div>
                            <div>
                                <strong className="text-primary">Destacado:</strong>{' '}
                                {data.is_featured ? 'Sí' : 'No'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {pickerOpen && (
                <MediaPickerModal
                    isOpen={pickerOpen}
                    onClose={() => setPickerOpen(false)}
                    onSelect={(media: SelectedMedia, _caption: string, _altText: string) => {
                        setFeaturedImage(media);
                        setData('featured_image_id', String(media.id));
                        setLocalErrors({});
                        setPickerOpen(false);
                    }}
                />
            )}
        </Authenticated>
    );
}
