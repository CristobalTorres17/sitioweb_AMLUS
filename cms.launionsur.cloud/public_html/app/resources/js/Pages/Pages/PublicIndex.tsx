import { Head, Link, usePage } from '@inertiajs/react';
import Breadcrumb from '@/Components/Breadcrumb';

type PageSummary = {
    id: number;
    title: string;
    slug: string;
    description: string;
    published_at: string;
    creator?: { id: number; name: string } | null;
};

export default function PublicIndex() {
    const props: any = usePage().props;
    const pages: any = props.pages || { data: [], links: [] };

    const breadcrumbs = [
        {
            label: 'Inicio',
            href: '/',
            current: false,
        },
        {
            label: 'Páginas',
            href: null,
            current: true,
        },
    ];

    return (
        <>
            <Head title="Páginas">
                <meta
                    name="description"
                    content="Accede a nuestras páginas informativas y documentación."
                />
            </Head>

            <div className="min-h-screen bg-gray-50">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    {/* Breadcrumb */}
                    <Breadcrumb items={breadcrumbs} />

                    {/* Header */}
                    <header className="mb-12">
                        <h1 className="text-4xl font-bold text-gray-900 mb-2">
                            Páginas Informativas
                        </h1>
                        <p className="text-lg text-gray-600">
                            Documentación y contenido informativo disponible
                        </p>
                    </header>

                    {/* Pages Grid */}
                    {pages.data && pages.data.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {pages.data.map((page: PageSummary) => (
                                <article
                                    key={page.id}
                                    className="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 flex flex-col"
                                >
                                    <Link
                                        href={route('pages.public.show', page.slug)}
                                        className="group"
                                    >
                                        <h2 className="text-xl font-bold text-gray-900 group-hover:text-primary transition mb-2">
                                            {page.title}
                                        </h2>
                                    </Link>

                                    {page.description && (
                                        <p className="text-gray-600 text-sm mb-4 flex-grow">
                                            {page.description}
                                        </p>
                                    )}

                                    <div className="flex items-center justify-between pt-4 border-t border-gray-200">
                                        <div className="text-xs text-gray-500">
                                            {page.creator && <span>Por {page.creator.name}</span>}
                                            {page.published_at && (
                                                <span className="ml-2">
                                                    {new Date(page.published_at).toLocaleDateString(
                                                        'es-ES',
                                                    )}
                                                </span>
                                            )}
                                        </div>
                                        <Link
                                            href={route('pages.public.show', page.slug)}
                                            className="text-primary font-medium text-sm hover:underline"
                                        >
                                            Leer →
                                        </Link>
                                    </div>
                                </article>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <p className="text-gray-600 text-lg">
                                No hay páginas publicadas en este momento.
                            </p>
                        </div>
                    )}

                    {/* Pagination */}
                    {pages.links && pages.links.length > 3 && (
                        <div className="mt-12 flex justify-center gap-2">
                            {/* Simple pagination placeholder */}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
