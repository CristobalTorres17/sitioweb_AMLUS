import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage, router } from '@inertiajs/react';

type Banner = {
    id: number;
    title: string;
    subtitle?: string | null;
    image_path?: string | null;
    cta_text?: string | null;
    cta_url?: string | null;
    show_button: boolean;
    page: string;
    sort_order: number;
    is_active: boolean;
};

export default function Index() {
    const props: any = usePage().props;
    const banners: Banner[] = props.banners || [];

    const grouped = banners.reduce<Record<string, Banner[]>>((acc, banner) => {
        acc[banner.page] = acc[banner.page] || [];
        acc[banner.page].push(banner);
        return acc;
    }, {});

    function move(id: number, direction: 'up' | 'down') {
        router.post(
            route(direction === 'up' ? 'banners.move-up' : 'banners.move-down', id),
            {},
            { preserveScroll: true },
        );
    }

    return (
        <Authenticated>
            <Head title="Banners" />
            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h2 className="text-lg font-medium">Banners</h2>
                    <Link
                        href={route('banners.create')}
                        className="rounded bg-primary px-3 py-3 text-sm text-gold font-bold hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Crear Banner
                    </Link>
                </div>

                <div className="space-y-6">
                    {Object.keys(grouped).length === 0 && (
                        <div className="rounded border border-dashed p-6 text-center text-sm text-gray-500">
                            No se encontraron banners.
                        </div>
                    )}

                    {Object.entries(grouped).map(([page, pageBanners]) => (
                        <section key={page} className="space-y-3">
                            <div className="flex items-center justify-between">
                                <h4 className="text-base font-semibold capitalize">Page: {page}</h4>
                                <span className="text-xs text-gray-500">
                                    {pageBanners.length} items
                                </span>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                {pageBanners.map((banner, index) => (
                                    <article
                                        key={banner.id}
                                        className="overflow-hidden rounded-lg border bg-white shadow-sm"
                                    >
                                        <div className="h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                                            {banner.image_path ? (
                                                <img
                                                    src={`/storage/${banner.image_path}`}
                                                    alt={banner.title}
                                                    className="h-full w-full object-contain object-center"
                                                />
                                            ) : (
                                                <div className="flex h-full items-center justify-center text-sm text-gray-400">
                                                    No image
                                                </div>
                                            )}
                                        </div>

                                        <div className="space-y-3 p-4">
                                            <div>
                                                <div className="flex items-center justify-between gap-3">
                                                    <h5 className="font-semibold">
                                                        {banner.title}
                                                    </h5>
                                                    <span
                                                        className={`rounded px-2 py-0.5 text-xs ${banner.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}`}
                                                    >
                                                        {banner.is_active ? 'Active' : 'Inactive'}
                                                    </span>
                                                </div>
                                                {banner.subtitle && (
                                                    <p className="mt-1 text-sm text-gray-600">
                                                        {banner.subtitle}
                                                    </p>
                                                )}
                                            </div>

                                            <div className="rounded bg-gray-50 p-3 text-sm">
                                                <div className="font-medium">Preview</div>
                                                <div className="mt-1 text-gray-600">
                                                    {banner.cta_text || 'CTA hidden'}
                                                </div>
                                                <div className="mt-1 text-xs text-gray-500">
                                                    {banner.cta_url || 'No URL'}
                                                </div>
                                            </div>

                                            <div className="flex items-center justify-between gap-2 text-sm">
                                                <div className="flex gap-2">
                                                    <button
                                                        type="button"
                                                        className="rounded border px-2 py-1 disabled:opacity-40"
                                                        disabled={index === 0}
                                                        onClick={() => move(banner.id, 'up')}
                                                    >
                                                        ↑
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="rounded border px-2 py-1 disabled:opacity-40"
                                                        disabled={index === pageBanners.length - 1}
                                                        onClick={() => move(banner.id, 'down')}
                                                    >
                                                        ↓
                                                    </button>
                                                </div>

                                                <div className="flex gap-3">
                                                    <Link
                                                        href={route('banners.edit', banner.id)}
                                                        className="text-primary"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        className="text-red-600"
                                                        onClick={() =>
                                                            router.delete(
                                                                route('banners.destroy', banner.id),
                                                            )
                                                        }
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        </section>
                    ))}
                </div>
            </div>
        </Authenticated>
    );
}
