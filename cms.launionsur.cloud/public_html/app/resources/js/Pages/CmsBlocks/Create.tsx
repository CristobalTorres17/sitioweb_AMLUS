import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import CmsBlockTemplateFields from '@/Components/CmsBlocks/CmsBlockTemplateFields';

interface Props {
    pages: Record<string, string>;
    templates: Record<
        string,
        { label: string; description: string; zones: Record<string, string> }
    >;
    initialPage?: string | null;
    initialZone?: string | null;
}

export default function Create({ pages, templates, initialPage, initialZone }: Props) {
    const { data, setData, post, errors } = useForm({
        name: '',
        slug: '',
        template_key: 'rich_text',
        zone: initialZone || 'content',
        template_data: {},
        content: '',
        excerpt: '',
        is_active: true,
        pages: initialPage ? [initialPage] : ([] as string[]),
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('cms-blocks.store'));
    };

    return (
        <Authenticated>
            <Head title="Crear Bloque CMS" />

            <div className="space-y-6">
                <div className="space-y-6 rounded-lg border border-border bg-surface p-6 shadow-sm">
                    <div className="flex items-center justify-between gap-3 bg-surface py-4 shadow-sm">
                        <div>
                            <h3 className="text-lg font-semibold text-primary">Nuevo bloque</h3>
                            <p className="mt-1 text-sm text-text-secondary">
                                Define un bloque reutilizable para una página o para el footer.
                            </p>
                        </div>

                        <Link
                            href={route('cms-blocks.index')}
                            className="inline-flex items-center rounded-md border border-border px-4 py-2 text-sm font-semibold text-gold transition bg-primary hover:bg-primary/80"
                        >
                            Volver
                        </Link>
                    </div>
                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium mb-2">Nombre *</label>
                            <input
                                type="text"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                className="w-full px-3 py-2 border rounded"
                                placeholder="e.g., Quiénes Somos Intro"
                            />
                            {errors.name && (
                                <p className="text-red-600 text-sm mt-1">{errors.name}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-2">Slug</label>
                            <input
                                type="text"
                                value={data.slug}
                                onChange={e => setData('slug', e.target.value)}
                                className="w-full px-3 py-2 border rounded"
                                placeholder="Auto-generated if empty"
                            />
                            {errors.slug && (
                                <p className="text-red-600 text-sm mt-1">{errors.slug}</p>
                            )}
                        </div>

                        <CmsBlockTemplateFields
                            templates={templates}
                            data={data}
                            setData={setData}
                        />

                        <div>
                            <label className="block text-sm font-medium mb-2">Resumen</label>
                            <textarea
                                value={data.excerpt}
                                onChange={e => setData('excerpt', e.target.value)}
                                className="w-full px-3 py-2 border rounded"
                                rows={3}
                                placeholder="Resumen breve"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-2">
                                Asignar a Páginas
                            </label>
                            <p className="mb-3 text-sm text-text-secondary">
                                Selecciona una o varias páginas. Si quieres un bloque exclusivo del
                                pie de página, marca <span className="font-semibold">Footer</span>.
                            </p>
                            {initialPage && !Object.keys(pages).includes(initialPage) && (
                                <div className="mb-3 rounded border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                                    <span className="font-semibold">
                                        Página asignada automáticamente:
                                    </span>{' '}
                                    <code className="font-mono">{initialPage}</code>
                                </div>
                            )}
                            <div className="space-y-2">
                                {Object.entries(pages).map(([slug, name]) => (
                                    <label key={slug} className="flex items-center">
                                        <input
                                            type="checkbox"
                                            checked={data.pages.includes(slug)}
                                            onChange={e => {
                                                if (e.target.checked) {
                                                    setData('pages', [...data.pages, slug]);
                                                } else {
                                                    setData(
                                                        'pages',
                                                        data.pages.filter(p => p !== slug),
                                                    );
                                                }
                                            }}
                                            className="mr-2"
                                        />
                                        {name}
                                    </label>
                                ))}
                            </div>
                        </div>

                        <div className="flex items-center">
                            <input
                                type="checkbox"
                                checked={data.is_active}
                                onChange={e => setData('is_active', e.target.checked)}
                                className="mr-2"
                            />
                            <label>Activo</label>
                        </div>

                        <div className="flex gap-2">
                            <button
                                type="submit"
                                className="inline-flex items-center rounded-md bg-primary text-white px-3 py-2 text-sm font-semibold"
                            >
                                Crear Bloque
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Authenticated>
    );
}
