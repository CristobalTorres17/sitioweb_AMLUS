import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import MediaPicker from '@/Components/MediaPicker';
import RichTextEditor from '@/Components/RichTextEditor';
import type { SelectedMedia } from '@/types/media';

function mediaToId(value: SelectedMedia[] | null | undefined): number | null {
    if (!value || value.length === 0) return null;
    return value[0].media_id ?? null;
}

function idToMedia(id: number | null | undefined): SelectedMedia[] {
    if (!id) return [];
    return [{ media_id: id, is_primary: true }];
}

interface District {
    id: number;
    name: string;
    slug: string;
    svg_path_id: string | null;
    short_summary: string | null;
    description: string | null;
    content: string | null;
    color: string | null;
    icon: string | null;
    hero_image_id: number | null;
    map_image_id: number | null;
    thumbnail_id: number | null;
    position: number;
    is_active: boolean;
    meta_title: string | null;
    meta_description: string | null;
}

export default function Edit({ district }: { district: District }) {
    const form = useForm({
        name: district.name,
        slug: district.slug,
        svg_path_id: district.svg_path_id ?? '',
        short_summary: district.short_summary ?? '',
        description: district.description ?? '',
        content: district.content ?? '',
        color: district.color ?? '#f5a800',
        icon: district.icon ?? '',
        hero_image_id: district.hero_image_id ?? (null as number | null),
        map_image_id: district.map_image_id ?? (null as number | null),
        thumbnail_id: district.thumbnail_id ?? (null as number | null),
        position: district.position,
        is_active: district.is_active,
        meta_title: district.meta_title ?? '',
        meta_description: district.meta_description ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(route('districts.update', district.id));
    }

    return (
        <Authenticated>
            <Head title={`Editar: ${district.name}`} />

            <form onSubmit={submit}>
                <div className="xl:grid xl:grid-cols-[minmax(0,1fr)_320px] xl:gap-6">
                    {/* ── Main column ── */}
                    <div className="space-y-5">
                        <div className="rounded-md bg-white p-6 shadow">
                            <h2 className="mb-4 text-xl font-semibold">
                                Editar Distrito: {district.name}
                            </h2>

                            {/* Basic info */}
                            <div className="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium">
                                        Nombre <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        className="mt-1 block w-full"
                                        value={form.data.name}
                                        onChange={e => form.setData('name', e.target.value)}
                                    />
                                    {form.errors.name && (
                                        <p className="mt-1 text-xs text-red-600">
                                            {form.errors.name}
                                        </p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">Slug (URL)</label>
                                    <input
                                        className="mt-1 block w-full font-mono text-sm"
                                        value={form.data.slug}
                                        onChange={e => form.setData('slug', e.target.value)}
                                    />
                                    {form.errors.slug && (
                                        <p className="mt-1 text-xs text-red-600">
                                            {form.errors.slug}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* SVG, Color, Icon */}
                            <div className="mt-4 grid gap-4 md:grid-cols-3">
                                <div>
                                    <label className="block text-sm font-medium">SVG Path ID</label>
                                    <input
                                        className="mt-1 block w-full font-mono text-sm"
                                        placeholder="path23"
                                        value={form.data.svg_path_id}
                                        onChange={e => form.setData('svg_path_id', e.target.value)}
                                    />
                                    <p className="mt-1 text-xs text-gray-400">
                                        ID del &lt;path&gt; en el SVG del mapa
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">Color</label>
                                    <div className="mt-1 flex items-center gap-2">
                                        <input
                                            type="color"
                                            className="h-9 w-14 cursor-pointer rounded border p-0.5"
                                            value={form.data.color}
                                            onChange={e => form.setData('color', e.target.value)}
                                        />
                                        <input
                                            className="block w-full font-mono text-sm"
                                            value={form.data.color}
                                            onChange={e => form.setData('color', e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">
                                        Ícono (clase CSS)
                                    </label>
                                    <input
                                        className="mt-1 block w-full"
                                        placeholder="fas fa-map-marker-alt"
                                        value={form.data.icon}
                                        onChange={e => form.setData('icon', e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Content */}
                        <div className="rounded-md bg-white p-6 shadow">
                            <h3 className="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">
                                Contenido
                            </h3>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium">
                                        Resumen corto
                                    </label>
                                    <input
                                        className="mt-1 block w-full"
                                        maxLength={500}
                                        placeholder="Breve texto para listados y tarjetas"
                                        value={form.data.short_summary}
                                        onChange={e =>
                                            form.setData('short_summary', e.target.value)
                                        }
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">
                                        Descripción (cabecera)
                                    </label>
                                    <textarea
                                        rows={3}
                                        className="mt-1 block w-full"
                                        maxLength={1000}
                                        placeholder="Texto introductorio visible en la cabecera de la página"
                                        value={form.data.description}
                                        onChange={e => form.setData('description', e.target.value)}
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">
                                        Contenido completo
                                    </label>
                                    <div className="mt-1">
                                        <RichTextEditor
                                            value={form.data.content}
                                            onChange={v => form.setData('content', v)}
                                        />
                                    </div>
                                    {form.errors.content && (
                                        <p className="mt-1 text-xs text-red-600">
                                            {form.errors.content}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Images */}
                        <div className="rounded-md bg-white p-6 shadow">
                            <h3 className="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">
                                Imágenes
                            </h3>
                            <div className="grid gap-6 md:grid-cols-3">
                                <MediaPicker
                                    label="Imagen Hero"
                                    singleSelect
                                    allowUpload={false}
                                    value={idToMedia(form.data.hero_image_id)}
                                    onChange={v => form.setData('hero_image_id', mediaToId(v))}
                                />
                                <MediaPicker
                                    label="Imagen del mapa"
                                    singleSelect
                                    allowUpload={false}
                                    value={idToMedia(form.data.map_image_id)}
                                    onChange={v => form.setData('map_image_id', mediaToId(v))}
                                />
                                <MediaPicker
                                    label="Miniatura (tarjeta)"
                                    singleSelect
                                    allowUpload={false}
                                    value={idToMedia(form.data.thumbnail_id)}
                                    onChange={v => form.setData('thumbnail_id', mediaToId(v))}
                                />
                            </div>
                        </div>

                        <div className="flex gap-3 pb-6">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded bg-primary px-5 py-2 text-sm font-semibold text-gold hover:bg-primary/80 disabled:opacity-60"
                            >
                                {form.processing ? 'Guardando…' : 'Guardar cambios'}
                            </button>
                            <a
                                href={route('districts.index')}
                                className="rounded border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                            >
                                Cancelar
                            </a>
                        </div>
                    </div>

                    {/* ── Right sidebar ── */}
                    <div className="mt-5 space-y-4 xl:mt-0">
                        {/* Visibility */}
                        <div className="rounded-md bg-white p-5 shadow">
                            <h3 className="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">
                                Visibilidad
                            </h3>
                            <div className="space-y-3">
                                <div>
                                    <label className="block text-sm font-medium">
                                        Posición en el mapa
                                    </label>
                                    <input
                                        type="number"
                                        min={0}
                                        className="mt-1 block w-full"
                                        value={form.data.position}
                                        onChange={e =>
                                            form.setData('position', Number(e.target.value))
                                        }
                                    />
                                </div>
                                <label className="flex cursor-pointer items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        className="h-4 w-4 rounded border-gray-300 text-primary"
                                        checked={form.data.is_active}
                                        onChange={e => form.setData('is_active', e.target.checked)}
                                    />
                                    <span>Publicado / Activo</span>
                                </label>
                            </div>
                        </div>

                        {/* SEO */}
                        <div className="rounded-md bg-white p-5 shadow">
                            <h3 className="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">
                                SEO
                            </h3>
                            <div className="space-y-3">
                                <div>
                                    <label className="block text-sm font-medium">Meta título</label>
                                    <input
                                        className="mt-1 block w-full text-sm"
                                        maxLength={255}
                                        value={form.data.meta_title}
                                        onChange={e => form.setData('meta_title', e.target.value)}
                                    />
                                    <p className="mt-1 text-right text-xs text-gray-400">
                                        {form.data.meta_title.length}/255
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">
                                        Meta descripción
                                    </label>
                                    <textarea
                                        rows={3}
                                        className="mt-1 block w-full text-sm"
                                        maxLength={1000}
                                        value={form.data.meta_description}
                                        onChange={e =>
                                            form.setData('meta_description', e.target.value)
                                        }
                                    />
                                    <p className="mt-1 text-right text-xs text-gray-400">
                                        {form.data.meta_description.length}/1000
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </Authenticated>
    );
}
