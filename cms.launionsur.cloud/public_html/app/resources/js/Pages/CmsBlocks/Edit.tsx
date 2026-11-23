import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import CmsBlockTemplateFields from '@/Components/CmsBlocks/CmsBlockTemplateFields';

interface Props {
    block: any;
    pages: Record<string, string>;
    templates: Record<
        string,
        { label: string; description: string; zones: Record<string, string> }
    >;
}

export default function Edit({ block, pages, templates }: Props) {
    const { data, setData, patch, errors } = useForm({
        name: block.name || '',
        slug: block.slug || '',
        template_key: block.template_key || 'rich_text',
        zone: block.zone || 'content',
        template_data: block.template_data || {},
        content: block.content || '',
        excerpt: block.excerpt || '',
        is_active: block.is_active ?? true,
        pages: (block.pages || []).map((p: any) => p.page) as string[],
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('cms-blocks.update', block.id));
    };

    const destroy = () => {
        if (!confirm('Delete this block?')) return;
        router.delete(route('cms-blocks.destroy', block.id), {
            preserveScroll: true,
        });
    };

    return (
        <Authenticated>
            <Head title={`Edit CMS Block: ${block.name}`} />

            <div className="space-y-6">
                <div className="grid gap-6 lg:grid-col">
                    <div className="space-y-6 rounded-lg border border-border bg-surface p-6 shadow-sm">
                        <div className="flex items-center justify-between gap-3 bg-surface py-4 shadow-sm">
                            <div>
                                <h3 className="text-lg font-semibold text-primary">
                                    Editar Bloque CMS: {block.name}
                                </h3>
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
                            <div className="grid gap-6 lg:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium mb-2">
                                        Nombre *
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="w-full px-3 py-2 border rounded"
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
                                        placeholder="Se generará automáticamente si está vacío"
                                    />
                                    {errors.slug && (
                                        <p className="text-red-600 text-sm mt-1">{errors.slug}</p>
                                    )}
                                </div>
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
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium mb-2">
                                    Assign to Pages
                                </label>
                                <p className="mb-3 text-sm text-text-secondary">
                                    Puedes asignar un bloque a varias páginas. Usa{' '}
                                    <span className="font-semibold">Footer</span> si debe mostrarse
                                    solo en el pie.
                                </p>

                                {/* Freeform pages (event:slug, district:slug, etc.) not in the config */}
                                {data.pages.filter(p => !Object.keys(pages).includes(p)).length >
                                    0 && (
                                    <div className="mb-4 rounded border border-blue-200 bg-blue-50 p-3">
                                        <p className="mb-2 text-xs font-semibold uppercase tracking-wider text-blue-700">
                                            Páginas específicas asignadas
                                        </p>
                                        <div className="flex flex-wrap gap-2">
                                            {data.pages
                                                .filter(p => !Object.keys(pages).includes(p))
                                                .map(p => (
                                                    <span
                                                        key={p}
                                                        className="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800"
                                                    >
                                                        <code>{p}</code>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                setData(
                                                                    'pages',
                                                                    data.pages.filter(x => x !== p),
                                                                )
                                                            }
                                                            className="ml-0.5 text-blue-500 hover:text-blue-800"
                                                            aria-label={`Quitar ${p}`}
                                                        >
                                                            ×
                                                        </button>
                                                    </span>
                                                ))}
                                        </div>
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

                            <div>
                                <label className="block text-sm font-medium mb-2">
                                    Assigned Pages (order)
                                </label>
                                <div className="space-y-2">
                                    {(block.pages || [])
                                        .sort((a: any, b: any) => a.position - b.position)
                                        .map((p: any, idx: number) => (
                                            <div
                                                key={p.id}
                                                className="flex items-center justify-between p-2 border rounded"
                                            >
                                                <div>{pages[p.page] || p.page}</div>
                                                <div className="space-x-2">
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            fetch(
                                                                route(
                                                                    'cms-blocks.move-up',
                                                                    block.id,
                                                                ),
                                                                {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type':
                                                                            'application/json',
                                                                        'X-CSRF-TOKEN': (
                                                                            document.querySelector(
                                                                                'meta[name="csrf-token"]',
                                                                            ) as HTMLMetaElement
                                                                        ).content,
                                                                    },
                                                                    body: JSON.stringify({
                                                                        page: p.page,
                                                                    }),
                                                                },
                                                            ).then(() => location.reload());
                                                        }}
                                                        disabled={idx === 0}
                                                        className="px-2 py-1 border rounded"
                                                    >
                                                        ↑
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            fetch(
                                                                route(
                                                                    'cms-blocks.move-down',
                                                                    block.id,
                                                                ),
                                                                {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type':
                                                                            'application/json',
                                                                        'X-CSRF-TOKEN': (
                                                                            document.querySelector(
                                                                                'meta[name="csrf-token"]',
                                                                            ) as HTMLMetaElement
                                                                        ).content,
                                                                    },
                                                                    body: JSON.stringify({
                                                                        page: p.page,
                                                                    }),
                                                                },
                                                            ).then(() => location.reload());
                                                        }}
                                                        disabled={
                                                            idx === (block.pages || []).length - 1
                                                        }
                                                        className="px-2 py-1 border rounded"
                                                    >
                                                        ↓
                                                    </button>
                                                </div>
                                            </div>
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
                                <label>Active</label>
                            </div>

                            <div className="flex gap-2">
                                <button
                                    type="submit"
                                    className="inline-flex items-center rounded-md bg-primary text-white px-3 py-2 text-sm font-semibold"
                                >
                                    Save Changes
                                </button>
                                <button
                                    type="button"
                                    onClick={destroy}
                                    className="inline-flex items-center rounded-md border border-border px-3 py-2 text-sm font-semibold"
                                >
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
