import { Head, usePage } from '@inertiajs/react';
import Breadcrumb from '@/Components/Breadcrumb';
import { useEffect } from 'react';

type PageData = {
    id: number;
    title: string;
    slug: string;
    content: string;
    published_at: string;
    creator?: { id: number; name: string } | null;
};

type BreadcrumbItem = {
    label: string;
    href: string | null;
    current: boolean;
};

type PageMeta = {
    canonical: string;
    title: string;
    description: string;
    keywords: string;
    og_title: string;
    og_description: string;
    og_image: string | null;
    og_url: string;
    twitter_card: string;
    twitter_image: string | null;
    breadcrumb_schema: string;
};

export default function Show() {
    const props: any = usePage().props;
    const page: PageData = props.page;
    const breadcrumbs: BreadcrumbItem[] = props.breadcrumbs || [];
    const meta: PageMeta = props.meta || {};

    // Inject JSON-LD breadcrumb schema into page head
    useEffect(() => {
        if (meta.breadcrumb_schema) {
            const script = document.createElement('script');
            script.type = 'application/ld+json';
            script.textContent = meta.breadcrumb_schema;
            document.head.appendChild(script);

            return () => {
                document.head.removeChild(script);
            };
        }
    }, [meta.breadcrumb_schema]);

    return (
        <>
            <Head title={meta.title || page.title}>
                <meta name="description" content={meta.description} />
                {meta.keywords && <meta name="keywords" content={meta.keywords} />}
                <link rel="canonical" href={meta.canonical} />

                {/* Open Graph */}
                <meta property="og:title" content={meta.og_title} />
                <meta property="og:description" content={meta.og_description} />
                <meta property="og:url" content={meta.og_url} />
                <meta property="og:type" content="article" />
                {meta.og_image && <meta property="og:image" content={meta.og_image} />}

                {/* Twitter Card */}
                <meta name="twitter:card" content={meta.twitter_card} />
                <meta name="twitter:title" content={meta.og_title} />
                <meta name="twitter:description" content={meta.og_description} />
                {meta.twitter_image && <meta name="twitter:image" content={meta.twitter_image} />}
            </Head>

            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    {/* Breadcrumb */}
                    <Breadcrumb items={breadcrumbs} />

                    {/* Page Header */}
                    <header className="mb-8">
                        <h1 className="text-4xl font-bold text-gray-900 mb-4">{page.title}</h1>
                        {page.creator && (
                            <div className="flex items-center gap-4 text-sm text-gray-600">
                                <span>Por {page.creator.name}</span>
                                {page.published_at && (
                                    <span>
                                        {new Date(page.published_at).toLocaleDateString('es-ES', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                        })}
                                    </span>
                                )}
                            </div>
                        )}
                    </header>

                    {/* Page Content */}
                    <article className="prose prose-lg max-w-none">
                        <div
                            className="text-gray-700 leading-relaxed"
                            dangerouslySetInnerHTML={{ __html: page.content }}
                        />
                    </article>

                    {/* Footer */}
                    <footer className="mt-12 pt-8 border-t border-gray-200">
                        <p className="text-sm text-gray-600">
                            Última actualización:{' '}
                            {new Date(page.published_at).toLocaleDateString('es-ES', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                            })}
                        </p>
                    </footer>
                </div>
            </div>
        </>
    );
}
