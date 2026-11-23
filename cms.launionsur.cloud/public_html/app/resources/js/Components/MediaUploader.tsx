import { useEffect, useState } from 'react';

export default function MediaUploader() {
    const [files, setFiles] = useState<Array<any>>([]);
    const [uploading, setUploading] = useState(false);

    function normalizeUrl(url: string) {
        if (!url) return url;
        try {
            if (url.startsWith('/')) return url;
            const u = new URL(url, window.location.href);
            if (u.origin !== window.location.origin) {
                return window.location.origin + u.pathname + u.search;
            }
            return u.href;
        } catch {
            return url;
        }
    }

    useEffect(() => {
        // Use public API endpoint to avoid 403 on auth-gated web route
        fetch('/api/media?per_page=100', {
            credentials: 'include',
            headers: { Accept: 'application/json' },
        })
            .then(res => res.json())
            .then(data => {
                const list = (data.data || []).map((f: any) => ({
                    ...f,
                    url: normalizeUrl(
                        f.urls?.thumbnail || f.urls?.original || f.url || `/storage/${f.path}`,
                    ),
                }));
                setFiles(list);
            })
            .catch(() => {});
    }, []);

    async function onFileChange(e: React.ChangeEvent<HTMLInputElement>) {
        const selected = e.target.files;
        if (!selected || selected.length === 0) return;

        setUploading(true);

        for (let i = 0; i < selected.length; i++) {
            const file = selected[i];
            const form = new FormData();
            form.append('file', file);

            // prefer meta token, otherwise fall back to XSRF-TOKEN cookie
            let token =
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (!token) {
                const xsrf = document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN='));
                if (xsrf) token = decodeURIComponent(xsrf.split('=')[1]);
            }

            try {
                const res = await fetch(route('media.store'), {
                    method: 'POST',
                    body: form,
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (res.ok) {
                    const json = await res.json();
                    json.url = normalizeUrl(json.url);
                    setFiles(prev => [json, ...prev]);
                } else {
                    console.error('Upload failed');
                }
            } catch (err) {
                console.error(err);
            }
        }

        setUploading(false);
        (e.target as HTMLInputElement).value = '';
    }

    async function remove(id: number) {
        if (!confirm('Delete this media?')) return;

        let token =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (!token) {
            const xsrf = document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN='));
            if (xsrf) token = decodeURIComponent(xsrf.split('=')[1]);
        }

        const res = await fetch(route('media.destroy', id), {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (res.ok) setFiles(prev => prev.filter(f => f.id !== id));
    }

    return (
        <div className="p-4 border rounded bg-white dark:bg-zinc-900">
            <label className="block mb-2 font-medium">
                Upload media (JPG, PNG, WEBP — max 5MB)
            </label>
            <input type="file" multiple onChange={onFileChange} accept="image/*" />
            {uploading && <div className="mt-2 text-sm">Uploading...</div>}

            <div className="mt-4 grid grid-cols-4 gap-3">
                {files.map((f: any) => (
                    <div key={f.id} className="border rounded overflow-hidden">
                        <img src={f.url} alt={f.filename} className="w-full h-24 object-cover" />
                        <div className="p-2 flex items-center justify-between">
                            <div className="text-xs">{f.filename}</div>
                            <button className="text-xs text-red-600" onClick={() => remove(f.id)}>
                                Delete
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
