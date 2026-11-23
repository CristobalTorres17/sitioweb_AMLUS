import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect } from 'react';
import RichTextEditor from '@/Components/RichTextEditor';
import RevisionHistory from '@/Components/RevisionHistory';
import { useAutoSave } from '@/Hooks/useAutoSave';
import MediaPicker from '@/Components/MediaPicker';

function resolvePrimaryImagePath(media: any[]): string {
    const primary = media.find(item => item.is_primary && item.path);
    if (primary?.path) {
        return primary.path;
    }

    return media.find(item => item.path)?.path || '';
}

export default function Edit({ article, categories, articleBlocks = [], articlePageSlug }: any) {
    const { data, setData, patch, processing, errors } = useForm({
        title: article.title || '',
        slug: article.slug || '',
        excerpt: article.excerpt || '',
        content: article.content || '',
        category_id: article.category?.id || '',
        status: (article.status || (article.is_published ? 'published' : 'draft')) as
            | 'draft'
            | 'published'
            | 'archived',
        published_at: article.published_at ? article.published_at.slice(0, 16) : '',
        is_featured: !!article.is_featured,
        meta_title: article.meta_title || '',
        meta_description: article.meta_description || '',
        image_path: article.image_path || resolvePrimaryImagePath(article.media || []),
        media: article.media
            ? article.media.map((m: any, i: number) => ({
                  media_id: m.id,
                  is_primary: !!m.pivot?.is_primary,
                  position: m.pivot?.position ?? i,
                  caption: m.pivot?.caption ?? null,
                  alt_text: m.pivot?.alt_text ?? null,
                  filename: m.filename ?? m.path,
                  path: m.path,
                  url: m.url ?? null,
              }))
            : [],
    });

    const [localErrors, setLocalErrors] = useState<{ image_path?: string }>({});
    const [activeTab, setActiveTab] = useState<'edit' | 'history' | 'blocks'>('edit');

    // Auto-save drafts every 30 seconds
    const { isSaving, lastSaved, setDataToSave } = useAutoSave({
        articleId: article.id,
        enabled: true,
        intervalMs: 30000, // 30 seconds
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
        setLocalErrors({});
        // client-side validation: ensure there's at least one media and one primary
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
        patch(route('articles.update', article.id));
    };

    return (
        <Authenticated>
            <Head title={`Editar: ${article.title}`} />

            <div className="mb-4 flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-semibold text-primary">Editar Artículo</h3>
                    <p className="mt-1 text-sm text-text-secondary">
                        Modifica el contenido del artículo.
                    </p>
                </div>
                <a
                    href={route('articles.index')}
                    className="inline-flex items-center rounded-md border border-border px-4 py-2 text-sm font-semibold text-gold bg-primary hover:bg-primary/80 transition"
                >
                    Volver a la lista
                </a>
            </div>

            {/* Tabs */}
            <div className="flex gap-4 border-b border-border mb-6">
                <button
                    onClick={() => setActiveTab('edit')}
                    className={`px-4 py-2 font-medium border-b-2 transition-colors ${
                        activeTab === 'edit'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-600 hover:text-gray-800'
                    }`}
                >
                    Editar
                </button>
                <button
                    onClick={() => setActiveTab('history')}
                    className={`px-4 py-2 font-medium border-b-2 transition-colors ${
                        activeTab === 'history'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-600 hover:text-gray-800'
                    }`}
                >
                    Historial de Revisiones
                </button>
                <button
                    onClick={() => setActiveTab('blocks')}
                    className={`px-4 py-2 font-medium border-b-2 transition-colors ${
                        activeTab === 'blocks'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-600 hover:text-gray-800'
                    }`}
                >
                    Bloques de contenido ({articleBlocks.length})
                </button>
            </div>

            {activeTab === 'edit' && (
                <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                    {/* Main column */}
                    <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Título *
                                </label>
                                <input
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
                                <label className="block text-sm font-medium text-primary">
                                    Slug (URL)
                                </label>
                                <input
                                    value={data.slug}
                                    onChange={e => setData('slug', e.target.value)}
                                    className="mt-2 block w-full px-3 py-2 border border-border rounded-md font-mono text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                />
                                {errors.slug && (
                                    <div className="text-sm text-red-600 mt-1">{errors.slug}</div>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Resumen
                                </label>
                                <textarea
                                    value={data.excerpt}
                                    onChange={e => setData('excerpt', e.target.value)}
                                    className="mt-2 block w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                    placeholder="Resumen breve visible en listados"
                                    rows={3}
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-primary">
                                    Categoría
                                </label>
                                <select
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
                                onChange={(value: any) => {
                                    const nextMedia = value || [];
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
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 rounded bg-primary text-gold font-medium hover:opacity-90 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar cambios'}
                                </button>
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

                    {/* Sidebar */}
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
                                        className="mt-2 block w-full px-3 py-2 border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
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
                                            className="mt-2 block w-full px-3 py-2 border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        />
                                        <p className="mt-1 text-xs text-gray-400">
                                            Vacío = fecha actual.
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
                                    <span className="font-medium text-primary">
                                        Artículo destacado
                                    </span>
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
                                    {data.status === 'published'
                                        ? 'Publicado'
                                        : data.status === 'archived'
                                          ? 'Archivado'
                                          : 'Borrador'}
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
                                        {(data.meta_title || '').length}/255
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
                                        placeholder="Descripción para buscadores"
                                    />
                                    <p className="mt-1 text-xs text-gray-400">
                                        {(data.meta_description || '').length}/1000
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {activeTab === 'history' && (
                <div className="rounded-lg bg-surface border border-border p-6 shadow-sm">
                    <RevisionHistory
                        articleId={article.id}
                        onRevisionRestored={() => window.location.reload()}
                    />
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
                                Inserta bloques preconstruidos para enriquecer la noticia sin
                                depender solo del editor de texto.
                            </p>
                        </div>
                        <Link
                            href={
                                route('cms-blocks.create') +
                                `?page=${encodeURIComponent(articlePageSlug)}&zone=mid_content`
                            }
                            className="shrink-0 rounded bg-primary px-4 py-2 text-sm font-medium text-gold hover:opacity-90"
                        >
                            + Agregar bloque
                        </Link>
                    </div>

                    {articleBlocks.length === 0 ? (
                        <div className="rounded-lg border border-dashed border-border bg-white p-8 text-center text-sm text-text-secondary">
                            Esta noticia no tiene bloques de contenido aún. Usa el botón{' '}
                            <span className="font-semibold">Agregar bloque</span> para insertar
                            secciones preconstruidas.
                        </div>
                    ) : (
                        <div className="space-y-2">
                            {articleBlocks.map((block: any) => (
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
                        <span className="font-mono font-semibold">mid_content</span> aparecen en el
                        detalle de la noticia, después del contenido principal. También puedes usar{' '}
                        <span className="font-mono font-semibold">after_hero</span> o{' '}
                        <span className="font-mono font-semibold">before_footer</span> según el
                        diseño.
                    </div>
                </div>
            )}
        </Authenticated>
    );
}
