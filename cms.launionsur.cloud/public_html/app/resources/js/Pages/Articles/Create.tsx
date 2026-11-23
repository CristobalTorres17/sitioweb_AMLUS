import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect } from 'react';
import MediaPicker from '@/Components/MediaPicker';
import RichTextEditor from '@/Components/RichTextEditor';
import PrimaryButton from '@/Components/PrimaryButton';
import { useAutoSave } from '@/Hooks/useAutoSave';

type SelectedMedia = {
    media_id: number;
    is_primary?: boolean;
    position?: number;
    caption?: string | null;
    alt_text?: string | null;
    filename?: string;
    path?: string;
    url?: string;
};

function resolvePrimaryImagePath(media: SelectedMedia[]): string {
    const primary = media.find(item => item.is_primary && item.path);
    if (primary?.path) {
        return primary.path;
    }

    return media.find(item => item.path)?.path || '';
}

export default function Create({ categories }: any) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        slug: '',
        excerpt: '',
        content: '',
        category_id: '',
        status: 'draft' as 'draft' | 'published' | 'archived',
        published_at: '',
        is_featured: false,
        meta_title: '',
        meta_description: '',
        image_path: '',
        media: [] as SelectedMedia[],
    });

    const [localErrors, setLocalErrors] = useState<{ image_path?: string }>({});

    // Auto-save drafts every 30 seconds
    const { isSaving, lastSaved, setDataToSave } = useAutoSave({
        articleId: undefined,
        enabled: true,
        intervalMs: 30000,
    });

    useEffect(() => {
        setDataToSave({
            title: data.title,
            slug: data.slug,
            excerpt: data.excerpt,
            content: data.content,
            category_id: data.category_id,
            status: data.status,
            published_at: data.published_at,
            is_featured: data.is_featured,
            meta_title: data.meta_title,
            meta_description: data.meta_description,
            image_path: data.image_path,
            media: data.media,
        });
    }, [data, setDataToSave]);

    const submit: FormEventHandler = e => {
        e.preventDefault();
        const hasMedia = Array.isArray(data.media) && data.media.length > 0;
        const hasPrimary = hasMedia && data.media.some((m: any) => m.is_primary);
        if (!hasMedia) {
            setLocalErrors({ image_path: 'Se requiere al menos una imagen.' });
            return;
        }
        if (!hasPrimary) {
            setLocalErrors({ image_path: 'Debe marcarse una imagen principal.' });
            return;
        }

        post(route('articles.store'));
    };

    const statusLabels: Record<string, string> = {
        draft: 'Borrador',
        published: 'Publicado',
        archived: 'Archivado',
    };

    return (
        <Authenticated>
            <Head title="Crear Artículo" />

            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                {/* Main column */}
                <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                    <h2 className="text-xl font-semibold text-primary mb-6">Crear Artículo</h2>
                    <form id="article-create-form" onSubmit={submit} className="space-y-6">
                        <div>
                            <label
                                htmlFor="title"
                                className="block text-sm font-medium text-primary"
                            >
                                Título *
                            </label>
                            <input
                                id="title"
                                value={data.title}
                                onChange={e => setData('title', e.target.value)}
                                className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                placeholder="Título del artículo"
                            />
                            {errors.title && (
                                <div className="text-sm text-red-600 mt-1">{errors.title}</div>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="slug"
                                className="block text-sm font-medium text-primary"
                            >
                                Slug (URL)
                            </label>
                            <input
                                id="slug"
                                value={data.slug}
                                onChange={e => setData('slug', e.target.value)}
                                className="mt-2 block w-full px-3 py-2 border border-border rounded-md font-mono text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                placeholder="se-genera-automaticamente"
                            />
                            {errors.slug && (
                                <div className="text-sm text-red-600 mt-1">{errors.slug}</div>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="excerpt"
                                className="block text-sm font-medium text-primary"
                            >
                                Resumen
                            </label>
                            <textarea
                                id="excerpt"
                                value={data.excerpt}
                                onChange={e => setData('excerpt', e.target.value)}
                                className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                placeholder="Resumen corto visible en listados"
                                rows={3}
                            />
                        </div>

                        <div>
                            <label
                                htmlFor="category_id"
                                className="block text-sm font-medium text-primary"
                            >
                                Categoría
                            </label>
                            <select
                                id="category_id"
                                value={data.category_id}
                                onChange={e => setData('category_id', e.target.value)}
                                className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                                <option value="">-- Sin categoría --</option>
                                {categories.map((c: any) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <MediaPicker
                            label="Galería de imágenes"
                            value={data.media}
                            onChange={value => {
                                const nextMedia = value || ([] as SelectedMedia[]);
                                setData('media', nextMedia);
                                setData('image_path', resolvePrimaryImagePath(nextMedia));
                                if (nextMedia.length > 0) setLocalErrors({});
                            }}
                        />
                        {(localErrors.image_path || errors.image_path) && (
                            <div className="text-sm text-red-600 mt-1">
                                {localErrors.image_path || errors.image_path}
                            </div>
                        )}

                        <div>
                            <label className="block text-sm font-medium text-primary">
                                Contenido
                            </label>
                            <RichTextEditor
                                value={data.content}
                                onChange={html => setData('content', html)}
                                placeholder="Escribe el contenido del artículo aquí..."
                                className="mt-2"
                            />
                        </div>

                        <div className="flex items-center gap-3 pt-4 border-t border-border">
                            <PrimaryButton type="submit" disabled={processing || isSaving}>
                                {processing ? 'Guardando...' : 'Guardar Artículo'}
                            </PrimaryButton>
                            <div className="ml-auto flex items-center gap-2 text-sm">
                                {isSaving && (
                                    <span className="text-gray-500 flex items-center gap-1">
                                        <span className="inline-block w-2 h-2 bg-blue-500 rounded-full animate-pulse" />
                                        Guardando borrador...
                                    </span>
                                )}
                                {lastSaved && !isSaving && (
                                    <span className="text-green-600">
                                        ✓ Guardado a las{' '}
                                        {lastSaved.toLocaleTimeString('es-ES', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                        })}
                                    </span>
                                )}
                            </div>
                        </div>
                    </form>
                </div>

                {/* Sidebar: publicación + SEO */}
                <div className="space-y-4">
                    {/* Publication panel */}
                    <div className="rounded-lg bg-surface border border-border p-5 shadow-sm">
                        <h3 className="text-sm font-semibold text-primary mb-4">Publicación</h3>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Estado
                                </label>
                                <select
                                    value={data.status}
                                    onChange={e =>
                                        setData('status', e.target.value as typeof data.status)
                                    }
                                    className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm"
                                >
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                    <option value="archived">Archivado</option>
                                </select>
                            </div>

                            {data.status === 'published' && (
                                <div>
                                    <label className="block text-sm font-medium text-primary">
                                        Fecha de publicación
                                    </label>
                                    <input
                                        type="datetime-local"
                                        value={data.published_at}
                                        onChange={e => setData('published_at', e.target.value)}
                                        className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm"
                                    />
                                    <p className="mt-1 text-xs text-gray-400">
                                        Vacío = se usa la fecha y hora actual.
                                    </p>
                                </div>
                            )}

                            <label className="flex items-center gap-2 text-sm cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    checked={data.is_featured}
                                    onChange={e => setData('is_featured', e.target.checked)}
                                    className="rounded border-border text-primary focus:ring-primary"
                                />
                                <span className="font-medium text-primary">Artículo destacado</span>
                            </label>
                        </div>

                        <div className="mt-4 pt-4 border-t border-border">
                            <div
                                className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ${
                                    data.status === 'published'
                                        ? 'bg-green-100 text-green-700'
                                        : data.status === 'archived'
                                          ? 'bg-gray-100 text-gray-600'
                                          : 'bg-yellow-100 text-yellow-700'
                                }`}
                            >
                                <span className="w-1.5 h-1.5 rounded-full bg-current" />
                                {statusLabels[data.status]}
                            </div>
                        </div>
                    </div>

                    {/* SEO panel */}
                    <div className="rounded-lg bg-surface border border-border p-5 shadow-sm">
                        <h3 className="text-sm font-semibold text-primary mb-4">SEO</h3>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Meta título
                                </label>
                                <input
                                    value={data.meta_title}
                                    onChange={e => setData('meta_title', e.target.value)}
                                    maxLength={255}
                                    className="mt-2 block w-full px-3 py-2 border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                    placeholder="Título para buscadores"
                                />
                                <p className="mt-1 text-xs text-gray-400">
                                    {data.meta_title.length}/255
                                </p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Meta descripción
                                </label>
                                <textarea
                                    value={data.meta_description}
                                    onChange={e => setData('meta_description', e.target.value)}
                                    maxLength={1000}
                                    rows={3}
                                    className="mt-2 block w-full px-3 py-2 border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                    placeholder="Descripción para buscadores (máx. 160 caracteres recomendado)"
                                />
                                <p className="mt-1 text-xs text-gray-400">
                                    {data.meta_description.length}/1000
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
