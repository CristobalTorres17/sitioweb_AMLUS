import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useForm } from '@inertiajs/react';
import type { CollectionNode } from '@/Components/Media/FolderSidebar';

function flattenTree(nodes: CollectionNode[], depth = 0): Array<{ id: number; label: string }> {
    const result: Array<{ id: number; label: string }> = [];

    for (const node of nodes) {
        result.push({ id: node.id, label: `${'\u00a0\u00a0'.repeat(depth)}${node.name}` });
        if (node.children.length > 0) {
            result.push(...flattenTree(node.children, depth + 1));
        }
    }

    return result;
}

export default function Edit() {
    const { props } = usePage();
    const { media = null, availableTags = [], collectionsTree = [] } = props as any;

    const form = useForm({
        is_active: media?.is_active ?? true,
        collection_id: media?.collection_id ?? null,
        tags: media?.tags ?? [],
        title: media?.original_filename ?? '',
        alt_text: media?.metadata?.alt_text ?? '',
        caption: media?.metadata?.caption ?? '',
    });

    const [saving, setSaving] = useState(false);

    // Determine readable text color (black or white) based on background hex
    const readableTextColor = (hex?: string) => {
        if (!hex) return '#000';
        const h = hex.replace('#', '');
        const r = parseInt(h.substring(0, 2), 16);
        const g = parseInt(h.substring(2, 4), 16);
        const b = parseInt(h.substring(4, 6), 16);
        const luminance = 0.2126 * (r / 255) + 0.7152 * (g / 255) + 0.0722 * (b / 255);
        return luminance > 0.6 ? '#000' : '#fff';
    };

    if (!media) return <div>Media not found</div>;

    function normalizeUrl(raw: any) {
        if (!raw) return '';
        if (typeof raw === 'string') return raw;
        try {
            if (typeof raw === 'object') {
                if ((raw as any).png) return (raw as any).png;
                if ((raw as any).webp) return (raw as any).webp;
                if ((raw as any).original) {
                    const o = (raw as any).original;
                    if (typeof o === 'string') return o;
                    if (o.png) return o.png;
                    if (o.webp) return o.webp;
                }
                for (const v of Object.values(raw)) {
                    if (!v) continue;
                    if (typeof v === 'string') return v;
                    if ((v as any).png) return (v as any).png;
                    if ((v as any).webp) return (v as any).webp;
                }
            }
        } catch (e) {
            // ignore
        }
        return String(raw);
    }

    function toggleTag(tagId: number) {
        const current = form.data.tags || [];
        const next = current.includes(tagId)
            ? current.filter((t: number) => t !== tagId)
            : [...current, tagId];
        form.setData('tags', next);
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        setSaving(true);
        form.put(route('media.update', media.id), {
            onFinish: () => setSaving(false),
            onSuccess: () => router.visit(route('media.index')),
        });
    }

    return (
        <AuthenticatedLayout>
            <Head title={`Editar Media - ${media.original_filename}`} />

            <div className="space-y-6 py-6">
                <div className="px-4 sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-bold">Editar Media</h1>
                </div>

                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 media-form">
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div className="col-span-2">
                                {media.urls?.medium ? (
                                    <img
                                        src={
                                            normalizeUrl((media.urls as any)?.medium) ||
                                            normalizeUrl(media.urls as any)
                                        }
                                        alt={media.original_filename}
                                        className="w-full rounded shadow"
                                    />
                                ) : media.filename ? (
                                    <img
                                        src={`/storage/media/medium/${media.filename}`}
                                        alt={media.original_filename}
                                        className="w-full rounded shadow"
                                    />
                                ) : (
                                    <div className="w-full rounded bg-gray-50 border border-dashed p-12 text-center text-sm text-gray-500">
                                        Imagen no disponible — revisa `storage:link` o si el archivo
                                        existe en storage.
                                    </div>
                                )}

                                <div className="mt-4 bg-white p-4 rounded shadow">
                                    <label className="block text-sm font-medium">Título</label>
                                    <input
                                        type="text"
                                        value={form.data.title}
                                        onChange={e => form.setData('title', e.target.value)}
                                        className="block w-full rounded-md border border-border py-2 px-3 text-sm placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                    />
                                    {form.errors.title && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {form.errors.title}
                                        </p>
                                    )}

                                    <p className="text-sm text-gray-600 mt-2">
                                        Nombre original: {media.original_filename}
                                    </p>
                                    <p className="text-sm text-gray-600">
                                        Tamaño: {media.size} bytes
                                    </p>
                                    <p className="text-sm text-gray-600">MIME: {media.mime_type}</p>
                                    {media.width && media.height && (
                                        <p className="text-sm text-gray-600">
                                            Dimensiones: {media.width}×{media.height} px
                                        </p>
                                    )}
                                </div>

                                <div className="mt-4 bg-white p-4 rounded shadow">
                                    <label className="block text-sm font-medium">Carpeta</label>
                                    <select
                                        value={form.data.collection_id ?? ''}
                                        onChange={e =>
                                            form.setData(
                                                'collection_id',
                                                e.target.value ? Number(e.target.value) : null,
                                            )
                                        }
                                        className="mt-2 block w-full rounded-md border border-border py-2 px-3 text-sm placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        <option value="">Sin carpeta</option>
                                        {flattenTree(collectionsTree as CollectionNode[]).map(
                                            option => (
                                                <option key={option.id} value={option.id}>
                                                    {option.label}
                                                </option>
                                            ),
                                        )}
                                    </select>
                                    {form.errors.collection_id && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {form.errors.collection_id}
                                        </p>
                                    )}
                                </div>

                                <div className="mt-4 bg-white p-4 rounded shadow">
                                    <label className="block text-sm font-medium">
                                        Texto alternativo
                                    </label>
                                    <input
                                        type="text"
                                        value={form.data.alt_text}
                                        onChange={e => form.setData('alt_text', e.target.value)}
                                        className="block w-full rounded-md border border-border py-2 px-3 text-sm placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary mt-2"
                                    />
                                    {form.errors.alt_text && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {form.errors.alt_text}
                                        </p>
                                    )}

                                    <label className="block text-sm font-medium mt-3">
                                        Leyenda
                                    </label>
                                    <textarea
                                        value={form.data.caption}
                                        onChange={e => form.setData('caption', e.target.value)}
                                        rows={3}
                                        className="block w-full rounded-md border border-border py-2 px-3 text-sm placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary mt-2"
                                    />
                                    {form.errors.caption && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {form.errors.caption}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="col-span-1 space-y-4">
                                <div className="bg-white p-4 rounded shadow">
                                    <label className="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            checked={form.data.is_active}
                                            onChange={e =>
                                                form.setData('is_active', e.target.checked)
                                            }
                                        />
                                        <span className="text-sm">Activo</span>
                                    </label>
                                </div>

                                <div className="bg-white p-4 rounded shadow">
                                    <label className="block text-sm font-medium">Etiquetas</label>
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {availableTags.map((tag: any) => {
                                            const selected = form.data.tags.includes(tag.id);
                                            const defaultBg = 'var(--color-primary)';
                                            const bg = selected
                                                ? tag.color || defaultBg
                                                : 'transparent';
                                            const color = selected
                                                ? readableTextColor(tag.color)
                                                : undefined;
                                            return (
                                                <button
                                                    key={tag.id}
                                                    type="button"
                                                    onClick={() => toggleTag(tag.id)}
                                                    aria-pressed={selected}
                                                    className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 ${selected ? '' : 'text-text-primary border border-border hover:bg-bg-secondary'}`}
                                                    style={{
                                                        backgroundColor: bg,
                                                        color: color,
                                                        cursor: 'pointer',
                                                    }}
                                                >
                                                    {tag.name}
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>

                                <div className="flex gap-2 justify-end">
                                    <button
                                        type="button"
                                        onClick={() => router.visit(route('media.index'))}
                                        className="inline-flex items-center rounded-md bg-surface px-3 py-2 text-sm font-semibold text-text-primary shadow-sm border border-border hover:bg-bg-secondary"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={saving}
                                        className="inline-flex items-center rounded-md bg-primary text-gold px-3 py-2 text-sm font-semibold shadow-sm hover:opacity-90 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-primary"
                                    >
                                        {saving ? 'Guardando...' : 'Guardar'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
