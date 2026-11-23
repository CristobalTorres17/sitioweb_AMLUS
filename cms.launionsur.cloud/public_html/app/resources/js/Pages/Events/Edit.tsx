import Authenticated from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import MediaPicker from '@/Components/MediaPicker';
import MediaPickerModal from '@/Components/MediaPickerModal';
import RichTextEditor from '@/Components/RichTextEditor';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { FormEventHandler, useMemo, useState } from 'react';

type GalleryMedia = {
    media_id: number;
    position?: number;
    caption?: string | null;
    alt_text?: string | null;
    filename?: string;
    path?: string;
    url?: string;
    is_primary?: boolean;
};

import type { MediaRef as SelectedMedia } from '@/types/media';

function toDatetimeLocal(value?: string | null) {
    if (!value) return '';
    return value.slice(0, 16);
}

export default function Edit({
    event,
    eventBlocks,
    eventPageSlug,
    categories,
}: {
    event: any;
    eventBlocks: any[];
    eventPageSlug: string;
    categories: { id: number; name: string; slug: string; color?: string; icon?: string }[];
}) {
    const { data, setData, patch, processing, errors } = useForm({
        title: event.title || '',
        slug: event.slug || '',
        description: event.description || '',
        content: event.content || '',
        event_date: toDatetimeLocal(event.event_date),
        event_date_end: toDatetimeLocal(event.event_date_end),
        location: event.location || '',
        organizer: event.organizer || '',
        featured_image_id: event.featured_image_id ? String(event.featured_image_id) : '',
        status: event.status || 'draft',
        published_at: toDatetimeLocal(event.published_at),
        is_featured: !!event.is_featured,
        meta_title: event.meta_title || '',
        meta_description: event.meta_description || '',
        category_id: event.category_id ? String(event.category_id) : '',
    });

    const [featuredImage, setFeaturedImage] = useState<SelectedMedia | null>(
        event.featured_image
            ? {
                  id: event.featured_image.id,
                  filename: event.featured_image.filename,
                  path: event.featured_image.path,
                  url: event.featured_image.path,
              }
            : null,
    );
    const [pickerOpen, setPickerOpen] = useState(false);
    const [localErrors, setLocalErrors] = useState<{ featured_image_id?: string }>({});
    const [activeTab, setActiveTab] = useState<'details' | 'gallery' | 'blocks'>('details');
    const [savingGallery, setSavingGallery] = useState(false);
    const [galleryMessage, setGalleryMessage] = useState<string | null>(null);

    const [gallery, setGallery] = useState<GalleryMedia[]>(
        (event.gallery || []).map((item: any, index: number) => ({
            media_id: item.media_id,
            position: item.position ?? index,
            caption: item.caption ?? null,
            alt_text: null,
            filename: item.media?.filename,
            path: item.media?.path,
            url: item.media?.path,
            is_primary: false,
        })),
    );

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
        patch(route('events.update', event.id));
    };

    const saveGallery = async () => {
        setSavingGallery(true);
        setGalleryMessage(null);

        try {
            const response = await window.axios.post(route('events.gallery.sync', event.id), {
                media: gallery.map((item, index) => ({
                    media_id: item.media_id,
                    position: item.position ?? index,
                    caption: item.caption ?? null,
                    alt_text: item.alt_text ?? null,
                })),
            });

            const updated = (response.data.gallery || []).map((item: any, index: number) => ({
                media_id: item.media_id,
                position: item.position ?? index,
                caption: item.caption ?? null,
                alt_text: null,
                filename: item.media?.filename,
                path: item.media?.path,
                url: item.media?.path,
                is_primary: false,
            }));

            setGallery(updated);
            setGalleryMessage('Galería guardada correctamente.');
        } catch (error) {
            setGalleryMessage('No se pudo guardar la galería.');
            console.error(error);
        } finally {
            setSavingGallery(false);
        }
    };

    return (
        <Authenticated
            header={<h2 className="text-xl font-semibold text-primary">Editar evento</h2>}
        >
            <Head title={`Editar evento: ${event.title}`} />

            <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                <div className="mb-6 flex gap-4 border-b border-border">
                    <button
                        type="button"
                        onClick={() => setActiveTab('details')}
                        className={`border-b-2 px-4 py-2 text-sm font-medium transition ${
                            activeTab === 'details'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-600 hover:text-gray-800'
                        }`}
                    >
                        Detalles
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('gallery')}
                        className={`border-b-2 px-4 py-2 text-sm font-medium transition ${
                            activeTab === 'gallery'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-600 hover:text-gray-800'
                        }`}
                    >
                        Galería ({gallery.length})
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('blocks')}
                        className={`border-b-2 px-4 py-2 text-sm font-medium transition ${
                            activeTab === 'blocks'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-600 hover:text-gray-800'
                        }`}
                    >
                        Bloques de contenido ({eventBlocks.length})
                    </button>
                </div>

                {activeTab === 'details' && (
                    <div className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                        <div>
                            <form onSubmit={submit} className="space-y-5">
                                <div>
                                    <label className="block text-sm font-medium text-primary">
                                        Título *
                                    </label>
                                    <input
                                        value={data.title}
                                        onChange={e => setData('title', e.target.value)}
                                        className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    />
                                    <InputError message={errors.title} className="mt-1" />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-primary">
                                        Slug
                                    </label>
                                    <input
                                        value={data.slug}
                                        onChange={e => setData('slug', e.target.value)}
                                        className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
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
                                        placeholder="Describe el evento y sus detalles"
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
                                            onChange={e =>
                                                setData('event_date_end', e.target.value)
                                            }
                                            className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                        />
                                        <InputError
                                            message={errors.event_date_end}
                                            className="mt-1"
                                        />
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
                                            onChange={e =>
                                                setData('meta_description', e.target.value)
                                            }
                                            className="mt-2 block w-full rounded-md border border-border px-3 py-2 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                            rows={2}
                                        />
                                        <InputError
                                            message={errors.meta_description}
                                            className="mt-1"
                                        />
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
                                                onChange={e =>
                                                    setData('is_featured', e.target.checked)
                                                }
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
                                        <InputError
                                            message={errors.published_at}
                                            className="mt-1"
                                        />
                                    </div>
                                </div>

                                <div className="rounded-lg border border-border bg-gray-50 p-4">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <div className="text-sm font-medium text-primary">
                                                Portada
                                            </div>
                                            <p className="text-xs text-text-secondary">
                                                Cambia la imagen destacada sin salir del editor.
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => setPickerOpen(true)}
                                            className="rounded bg-primary/10 px-3 py-2 text-sm font-medium text-primary hover:bg-primary/15"
                                        >
                                            Cambiar portada
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
                                            No hay portada seleccionada.
                                        </div>
                                    )}
                                    {(localErrors.featured_image_id ||
                                        errors.featured_image_id) && (
                                        <div className="mt-2 text-sm text-red-600">
                                            {localErrors.featured_image_id ||
                                                errors.featured_image_id}
                                        </div>
                                    )}
                                </div>

                                <div className="flex items-center gap-3 border-t border-border pt-4">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="rounded bg-primary px-4 py-2 font-medium text-gold hover:opacity-90 disabled:opacity-50"
                                    >
                                        {processing ? 'Guardando...' : 'Guardar cambios'}
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => router.visit(route('events.index'))}
                                        className="rounded border border-border bg-white px-4 py-2 font-medium text-primary hover:bg-gray-50"
                                    >
                                        Volver
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                            <h3 className="text-lg font-medium text-primary">Resumen</h3>
                            <div className="mt-4 space-y-4 rounded-xl border border-border bg-white p-4">
                                <div className="text-xs uppercase tracking-wider text-text-secondary">
                                    {data.status}
                                </div>
                                <div className="text-2xl font-semibold text-primary">
                                    {data.title || 'Título del evento'}
                                </div>
                                <p className="text-sm text-text-secondary">
                                    {data.description || 'Descripción corta del evento.'}
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
                                        <strong className="text-primary">Galería:</strong>{' '}
                                        {gallery.length} imágenes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {activeTab === 'gallery' && (
                    <div className="space-y-4">
                        <div className="rounded-lg border border-border bg-gray-50 p-4">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 className="text-lg font-medium text-primary">
                                        Galería editable
                                    </h3>
                                    <p className="text-sm text-text-secondary">
                                        Selecciona, sube, reordena y agrega captions antes de
                                        guardar.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={saveGallery}
                                    disabled={savingGallery}
                                    className="rounded bg-primary px-4 py-2 font-medium text-gold hover:opacity-90 disabled:opacity-50"
                                >
                                    {savingGallery ? 'Guardando...' : 'Guardar galería'}
                                </button>
                            </div>

                            {galleryMessage && (
                                <div className="mt-3 rounded border border-border bg-white px-3 py-2 text-sm text-text-secondary">
                                    {galleryMessage}
                                </div>
                            )}
                        </div>

                        <MediaPicker
                            label="Imágenes del evento"
                            value={gallery}
                            onChange={value => setGallery((value || []) as GalleryMedia[])}
                        />

                        <div className="rounded-lg border border-border bg-white p-4 text-sm text-text-secondary">
                            <div className="font-medium text-primary">Consejo</div>
                            Usa el botón de subir imágenes para agregar material nuevo y arrastra
                            con los controles del selector para cambiar el orden.
                        </div>
                    </div>
                )}

                {activeTab === 'blocks' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between gap-4 rounded-lg border border-border bg-gray-50 p-4">
                            <div>
                                <h3 className="text-lg font-medium text-primary">
                                    Bloques de contenido
                                </h3>
                                <p className="text-sm text-text-secondary">
                                    Inserta secciones adicionales (destacados, llamadas a la acción,
                                    galería, etc.) que aparecerán dentro de la noticia.
                                </p>
                            </div>
                            <Link
                                href={
                                    route('cms-blocks.create') +
                                    `?page=${encodeURIComponent(eventPageSlug)}&zone=mid_content`
                                }
                                className="shrink-0 rounded bg-primary px-4 py-2 text-sm font-medium text-gold hover:opacity-90"
                            >
                                + Agregar bloque
                            </Link>
                        </div>

                        {eventBlocks.length === 0 ? (
                            <div className="rounded-lg border border-dashed border-border bg-white p-8 text-center text-sm text-text-secondary">
                                Esta noticia no tiene bloques de contenido aún. Usa el botón{' '}
                                <span className="font-semibold">Agregar bloque</span> para insertar
                                secciones adicionales.
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {eventBlocks.map((block: any) => (
                                    <div
                                        key={block.id}
                                        className="flex items-center justify-between gap-4 rounded-lg border border-border bg-white px-4 py-3"
                                    >
                                        <div className="min-w-0">
                                            <div className="truncate text-sm font-medium text-primary">
                                                {block.name}
                                            </div>
                                            <div className="mt-0.5 flex items-center gap-2 text-xs text-text-secondary">
                                                <span className="rounded bg-gray-100 px-1.5 py-0.5 font-mono">
                                                    {block.template_key}
                                                </span>
                                                <span>zona: {block.zone}</span>
                                                <span
                                                    className={`rounded px-1.5 py-0.5 font-semibold ${
                                                        block.is_active
                                                            ? 'bg-green-100 text-green-700'
                                                            : 'bg-gray-100 text-gray-500'
                                                    }`}
                                                >
                                                    {block.is_active ? 'activo' : 'inactivo'}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            href={route('cms-blocks.edit', block.id)}
                                            className="shrink-0 rounded border border-border px-3 py-1.5 text-xs font-medium text-primary hover:bg-gray-50"
                                        >
                                            Editar
                                        </Link>
                                    </div>
                                ))}
                            </div>
                        )}

                        <div className="rounded-lg border border-border bg-white p-4 text-sm text-text-secondary">
                            <div className="font-medium text-primary">Cómo funciona</div>
                            Los bloques en zona{' '}
                            <span className="font-mono font-semibold">mid_content</span> aparecen
                            dentro del cuerpo de la noticia, después del contenido principal.
                            También puedes usar{' '}
                            <span className="font-mono font-semibold">after_hero</span> o{' '}
                            <span className="font-mono font-semibold">before_footer</span> para
                            otras posiciones.
                        </div>
                    </div>
                )}
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
