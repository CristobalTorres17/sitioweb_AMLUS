import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useMemo } from 'react';
import MediaPicker from '@/Components/MediaPicker';

interface Pages {
    [key: string]: string;
}

export default function Create({ pages }: { pages: Pages }) {
    const form = useForm({
        title: '',
        subtitle: '',
        cta_text: '',
        cta_url: '',
        page: 'home',
        sort_order: 0,
        show_button: true,
        is_active: true,
        image_path: '',
    });

    function resolveMediaUrl(path?: string | null) {
        if (!path) return null;
        if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('//')) {
            return path;
        }
        if (path.startsWith('/storage/') || path.startsWith('/')) {
            return path;
        }
        return `/storage/${path}`;
    }

    function resolveStoredPath(value?: string | null) {
        if (!value) return '';
        if (value.startsWith('/storage/')) return value.replace(/^\/storage\//, '');
        if (value.startsWith('/')) return value.replace(/^\//, '');
        if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('//')) {
            try {
                const parsed = new URL(value, window.location.href);
                return parsed.pathname.replace(/^\/storage\//, '').replace(/^\//, '');
            } catch {
                return '';
            }
        }
        return value;
    }

    const previewImage = useMemo(() => form.data.image_path || null, [form.data.image_path]);
    const previewSrc = resolveMediaUrl(previewImage);

    function renderPreviewTitle(text: string) {
        const words = text.trim().split(/\s+/).filter(Boolean);
        if (words.length >= 6) {
            return (
                <>
                    <span className="block">{words.slice(0, 3).join(' ')}</span>
                    <span className="block">{words.slice(3).join(' ')}</span>
                </>
            );
        }

        return text;
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        console.debug('Banners/Create submit payload', form.data);
        // No file uploads from this form (media selected from gallery), send JSON
        form.post(route('banners.store'));
    }

    return (
        <Authenticated>
            <Head title="Create Banner" />

            <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                    <div className="mb-4">
                        <h2 className="text-lg font-medium">Crear Banner</h2>
                    </div>
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium">Title</label>
                            <input
                                className="mt-1 block w-full"
                                value={form.data.title}
                                onChange={e => form.setData('title', e.target.value)}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium">Subtitle</label>
                            <textarea
                                rows={3}
                                className="mt-1 block w-full"
                                value={form.data.subtitle}
                                onChange={e => form.setData('subtitle', e.target.value)}
                            />
                        </div>

                        <MediaPicker
                            label="Banner image"
                            singleSelect
                            allowUpload={false}
                            value={
                                form.data.image_path && typeof form.data.image_path === 'string'
                                    ? [
                                          {
                                              media_id: 0,
                                              filename: form.data.image_path,
                                              path: form.data.image_path,
                                              url:
                                                  resolveMediaUrl(form.data.image_path) ||
                                                  form.data.image_path,
                                              is_primary: true,
                                          },
                                      ]
                                    : null
                            }
                            onChange={value =>
                                form.setData(
                                    'image_path',
                                    value && value.length > 0
                                        ? resolveStoredPath(
                                              value[0].path ||
                                                  value[0].url ||
                                                  value[0].filename ||
                                                  '',
                                          )
                                        : '',
                                )
                            }
                        />

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">CTA Text</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.cta_text}
                                    onChange={e => form.setData('cta_text', e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium">CTA URL</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.cta_url}
                                    onChange={e => form.setData('cta_url', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Page</label>
                                <select
                                    className="mt-1 block w-full"
                                    value={form.data.page}
                                    onChange={e => form.setData('page', e.target.value)}
                                >
                                    {Object.entries(pages).map(([key, label]) => (
                                        <option key={key} value={key}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Sort order</label>
                                <input
                                    type="number"
                                    className="mt-1 block w-full"
                                    value={form.data.sort_order}
                                    onChange={e =>
                                        form.setData('sort_order', Number(e.target.value))
                                    }
                                />
                            </div>
                        </div>

                        <div className="flex items-center gap-4 text-sm">
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={form.data.show_button}
                                    onChange={e => form.setData('show_button', e.target.checked)}
                                />
                                Show button
                            </label>
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={form.data.is_active}
                                    onChange={e => form.setData('is_active', e.target.checked)}
                                />
                                Active
                            </label>
                        </div>

                        <div className="flex items-center gap-2">
                            <button
                                type="submit"
                                className="rounded bg-primary px-3 py-1 text-white"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                onClick={async () => {
                                    try {
                                        const token =
                                            document
                                                .querySelector('meta[name="csrf-token"]')
                                                ?.getAttribute('content') || '';
                                        console.debug(
                                            'Debug: sending raw JSON to store',
                                            form.data,
                                        );
                                        const res = await fetch(route('banners.store'), {
                                            method: 'POST',
                                            credentials: 'include',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': token,
                                            },
                                            body: JSON.stringify(form.data),
                                        });
                                        console.debug('Debug store response status', res.status);
                                        const text = await res.text();
                                        console.debug('Debug store response body', text);
                                    } catch (err) {
                                        console.error('Debug store failed', err);
                                    }
                                }}
                                className="rounded border px-3 py-1 text-sm"
                            >
                                Debug: send JSON
                            </button>
                        </div>
                    </form>
                </div>

                <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                    <h3 className="text-lg font-medium">Live Preview</h3>
                    <div className="mt-4 overflow-hidden rounded-xl border bg-gray-900 text-white">
                        <div className="relative flex h-56 items-stretch overflow-hidden">
                            {previewSrc ? (
                                <img
                                    src={previewSrc}
                                    alt={form.data.title || 'Banner preview'}
                                    className="absolute inset-y-0 right-0 h-full w-full object-contain object-right"
                                />
                            ) : (
                                <div className="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-950" />
                            )}
                            <div className="absolute inset-0 bg-black/35" />
                            <div className="relative z-10 flex h-full w-full max-w-[64%] flex-col justify-end p-6 pr-8">
                                <div className="max-w-[22ch] text-[1.9rem] font-bold leading-[0.95] tracking-tight sm:text-[1.1rem] lg:text-[1.8rem]">
                                    {renderPreviewTitle(form.data.title || 'Banner title')}
                                </div>
                                <div className="mt-3 max-w-[300ch] text-[5px] leading-5 text-white/75 sm:text-[8px] sm:leading-5 lg:text-[10px]">
                                    {form.data.subtitle || 'Banner subtitle'}
                                </div>
                                {form.data.show_button && (
                                    <button
                                        type="button"
                                        className="mt-4 w-fit rounded bg-white px-4 py-2 text-sm font-medium text-gray-900"
                                    >
                                        {form.data.cta_text || 'CTA button'}
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
