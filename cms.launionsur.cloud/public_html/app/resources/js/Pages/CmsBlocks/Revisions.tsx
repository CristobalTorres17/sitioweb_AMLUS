import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

interface Props {
    block: any;
    revisions: any[];
}

export default function Revisions({ block, revisions }: Props) {
    const restore = (id: number) => {
        if (!confirm('Restore this revision?')) return;
        router.post(route('cms-blocks.revisions.restore', [block.id, id]), undefined, {
            preserveScroll: true,
        });
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">CMS Block Revisions</h2>}>
            <Head title={`Revisions: ${block.name}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between gap-3 rounded-lg border border-border bg-surface px-6 py-4 shadow-sm">
                    <div>
                        <h3 className="text-lg font-semibold text-primary">
                            Revisions for: {block.name}
                        </h3>
                        <p className="mt-1 text-sm text-text-secondary">
                            Restaurar una revisión reemplaza el contenido actual del bloque.
                        </p>
                    </div>

                    <Link
                        href={route('cms-blocks.index')}
                        className="inline-flex items-center rounded-md border border-border px-4 py-2 text-sm font-semibold text-primary transition hover:bg-gray-50"
                    >
                        Volver
                    </Link>
                </div>

                <div className="grid gap-4">
                    {revisions.map(rev => (
                        <div
                            key={rev.id}
                            className="rounded-lg border border-border bg-surface p-5 shadow-sm"
                        >
                            <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                        Revision #{rev.revision_number}
                                    </div>
                                    <div className="mt-1 text-lg font-semibold text-primary">
                                        {rev.name}
                                    </div>
                                    <div className="mt-1 text-xs text-text-secondary">
                                        By: {rev.author ? rev.author.name : 'system'} at{' '}
                                        {new Date(rev.created_at).toLocaleString('es-ES')}
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    className="inline-flex items-center rounded-md border border-border px-3 py-2 text-sm font-semibold text-primary transition hover:bg-gray-50"
                                    onClick={() => restore(rev.id)}
                                >
                                    Restore
                                </button>
                            </div>

                            <div
                                className="prose prose-slate mt-4 max-w-none rounded-lg border border-border bg-white p-4"
                                dangerouslySetInnerHTML={{ __html: rev.content }}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </Authenticated>
    );
}
