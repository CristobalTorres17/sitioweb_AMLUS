// MediaPicker component - multi-select gallery and uploader
import { useEffect, useState } from 'react';
import MediaPickerModal from './MediaPickerModal';
import { useMediaLibrary, normalizeMediaUrl, normalizeMediaItem } from '@/Hooks/useMediaLibrary';
import type { CollectionNode } from '@/Components/Media/FolderSidebar';
import type { SelectedMedia } from '@/types/media';

type MediaPickerProps = {
    value?: SelectedMedia[] | null;
    onChange?: (value: SelectedMedia[] | null) => void;
    label?: string;
    singleSelect?: boolean;
    allowUpload?: boolean;
    // compatibility with modal usage
    onSelect?: (media: any, caption?: string, altText?: string) => void;
    onClose?: () => void;
    isOpen?: boolean;
    type?: string;
};

// Flatten collection tree for a <select> dropdown, with indent prefix per depth
function flattenTree(nodes: CollectionNode[], depth = 0): Array<{ id: number; label: string }> {
    const result: Array<{ id: number; label: string }> = [];
    for (const node of nodes) {
        result.push({ id: node.id, label: '\u00a0\u00a0'.repeat(depth) + node.name });
        if (node.children.length > 0) result.push(...flattenTree(node.children, depth + 1));
    }
    return result;
}

export default function MediaPicker({
    value = null,
    onChange,
    label = 'Images',
    singleSelect = false,
    allowUpload = true,
    onSelect,
    onClose,
    isOpen,
    type,
}: MediaPickerProps) {
    // If used as a modal (pages pass onClose/onSelect), delegate to MediaPickerModal
    if (onClose) {
        return (
            <MediaPickerModal
                isOpen={isOpen ?? true}
                onClose={onClose}
                onSelect={(media: any, caption: string, altText: string) => {
                    if (onSelect) onSelect(media, caption, altText);
                }}
            />
        );
    }

    const [collectionId, setCollectionId] = useState<number | null>(null);
    const [tree, setTree] = useState<CollectionNode[]>([]);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const { items: files, prependItem, loading } = useMediaLibrary({ perPage: 100, collectionId });
    const [uploading, setUploading] = useState(false);
    const [selected, setSelected] = useState<SelectedMedia[]>(
        singleSelect ? (value || []).slice(0, 1) : value || [],
    );

    useEffect(() => {
        setSelected(singleSelect ? (value || []).slice(0, 1) : value || []);
    }, [value, singleSelect]);

    function normalizeUrl(raw: any) {
        if (!raw) return '';
        // Accept objects returned by backend (variants map) or simple objects
        try {
            if (typeof raw === 'object') {
                // If passed the variants map: { original: { png: '/..', webp: '/..' }, thumbnail: {...} }
                const obj: any = raw;
                if (obj.original) {
                    if (typeof obj.original === 'string') return normalizeUrl(obj.original);
                    if ((obj.original as any).png) return normalizeUrl((obj.original as any).png);
                    if ((obj.original as any).webp) return normalizeUrl((obj.original as any).webp);
                }
                // If object has png/webp directly
                if (obj.png) return normalizeUrl(obj.png);
                if (obj.webp) return normalizeUrl(obj.webp);
                // If it's the `urls` object with variant keys, pick first available
                for (const v of Object.values(obj)) {
                    if (!v) continue;
                    if (typeof v === 'string') return normalizeUrl(v);
                    if (typeof v === 'object') {
                        if ((v as any).png) return normalizeUrl((v as any).png);
                        if ((v as any).webp) return normalizeUrl((v as any).webp);
                        // nested original
                        if ((v as any).original) {
                            if ((v as any).original.png)
                                return normalizeUrl((v as any).original.png);
                            if ((v as any).original.webp)
                                return normalizeUrl((v as any).original.webp);
                        }
                    }
                }
                return '';
            }

            const url = String(raw);
            if (!url.startsWith('/') && !url.includes('://')) {
                // Handle media paths: ensure /original/ is inserted
                if (
                    url.startsWith('media/') &&
                    !url.includes('/original/') &&
                    !url.includes('/thumbnail/') &&
                    !url.includes('/medium/') &&
                    !url.includes('/full/')
                ) {
                    return `/storage/media/original/${url.replace(/^media\//, '')}`;
                }
                return `/storage/${url}`;
            }
            if (url.startsWith('/')) {
                // Handle absolute paths: only insert /original/ if no variant exists
                if (
                    url.includes('/storage/media/') &&
                    !url.includes('/original/') &&
                    !url.includes('/thumbnail/') &&
                    !url.includes('/medium/') &&
                    !url.includes('/full/')
                ) {
                    return url.replace(/\/storage\/media\//, '/storage/media/original/');
                }
                return url;
            }
            const u = new URL(url, window.location.href);
            if (u.origin !== window.location.origin) {
                return window.location.origin + u.pathname + u.search;
            }
            return u.href;
        } catch {
            return String(raw);
        }
    }

    function toStoragePath(raw: any) {
        const url = normalizeUrl(raw);
        if (!url) return '';
        try {
            const cleaned = url.split('?')[0].split('#')[0];
            if (cleaned.startsWith('/storage/')) return cleaned.replace(/^\/storage\//, '');
            if (cleaned.startsWith('/')) return cleaned.replace(/^\//, '');
            return cleaned;
        } catch {
            return String(raw || '');
        }
    }

    // When the central media library updates, enrich selected items with file metadata
    useEffect(() => {
        if (!files || files.length === 0) return;

        const enriched = selected.map(s => {
            const found = files.find((l: any) => l.id === s.media_id || l.path === s.path);
            if (found)
                return {
                    ...s,
                    filename: found.filename,
                    url: normalizeUrl(found.urls.thumbnail || found.urls.original),
                    path: found.path,
                };
            return s;
        });

        setSelected(enriched);
    }, [files]);

    async function onFileChange(e: React.ChangeEvent<HTMLInputElement>) {
        const selectedFiles = e.target.files;
        if (!selectedFiles || selectedFiles.length === 0) return;

        setUploading(true);

        for (let i = 0; i < selectedFiles.length; i++) {
            const file = selectedFiles[i];
            const form = new FormData();
            form.append('image', file);
            if (collectionId !== null) form.append('collection_id', String(collectionId));

            let token =
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (!token) {
                const xsrf = document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN='));
                if (xsrf) token = decodeURIComponent(xsrf.split('=')[1]);
            }

            try {
                const response = await fetch(route('media.store'), {
                    method: 'POST',
                    body: form,
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    const json = await response.json();
                    const item = normalizeMediaItem(json);
                    prependItem(item);

                    const newSelected: SelectedMedia = {
                        media_id: item.id,
                        is_primary: selected.length === 0,
                        position: selected.length,
                        caption: null,
                        alt_text: null,
                        filename: item.filename,
                        path: item.path,
                        url: item.urls.thumbnail,
                    };
                    const updated = [...selected, newSelected];
                    setSelected(updated);
                    if (onChange) onChange(updated);
                }
            } catch {
                // ignore
            }
        }

        setUploading(false);
        (e.target as HTMLInputElement).value = '';
    }

    function getFileUrl(file: { urls?: { thumbnail?: string; original?: string }; url?: string }) {
        return normalizeUrl(file.urls?.thumbnail || file.urls?.original || file.url || '');
    }

    function toggleSelectFile(file: {
        id: number;
        filename: string;
        path?: string;
        url?: string;
        urls?: { thumbnail?: string; original?: string };
    }) {
        const exists = selected.find(s => s.media_id === file.id || s.path === file.path);
        let updated: SelectedMedia[];
        if (exists) {
            updated = selected.filter(s => !(s.media_id === file.id || s.path === file.path));
        } else {
            const next: SelectedMedia = {
                media_id: file.id,
                is_primary: selected.length === 0,
                position: selected.length,
                caption: null,
                alt_text: null,
                filename: file.filename,
                path: file.path,
                url: getFileUrl(file),
            };
            updated = singleSelect ? [next] : [...selected, next];
        }
        updated = updated.map((s, i) => ({ ...s, position: i }));
        setSelected(updated);
        if (onChange) onChange(updated);
    }

    function setPrimary(mediaId: number) {
        const updated = selected.map(s => ({ ...s, is_primary: s.media_id === mediaId }));
        setSelected(updated);
        if (onChange) onChange(updated);
    }

    function move(mediaId: number, direction: 'up' | 'down') {
        const idx = selected.findIndex(s => s.media_id === mediaId);
        if (idx === -1) return;
        const copy = [...selected];
        const swapIdx = direction === 'up' ? idx - 1 : idx + 1;
        if (swapIdx < 0 || swapIdx >= copy.length) return;
        [copy[idx], copy[swapIdx]] = [copy[swapIdx], copy[idx]];
        setSelected(copy.map((s, i) => ({ ...s, position: i })));
        if (onChange) onChange(copy.map((s, i) => ({ ...s, position: i })));
    }

    function remove(mediaId: number) {
        const updated = selected
            .filter(s => s.media_id !== mediaId)
            .map((s, i) => ({ ...s, position: i }));
        setSelected(updated);
        if (onChange) onChange(updated);
    }

    function updateCaption(mediaId: number, caption: string) {
        const updated = selected.map(s => (s.media_id === mediaId ? { ...s, caption } : s));
        setSelected(updated);
        if (onChange) onChange(updated);
    }

    function handleDrawerSelect(media: any, caption?: string, altText?: string) {
        const next: SelectedMedia = {
            media_id: media.id,
            is_primary: selected.length === 0,
            position: selected.length,
            caption: caption ?? null,
            alt_text: altText ?? null,
            filename: media.filename,
            path: media.path,
            url: media.url || media.urls?.thumbnail || media.urls?.original,
        };

        let updated: SelectedMedia[];
        if (singleSelect) {
            updated = [next];
        } else {
            const exists = selected.some(
                item => item.media_id === media.id || item.path === media.path,
            );
            updated = exists
                ? selected.map(item =>
                      item.media_id === media.id || item.path === media.path ? next : item,
                  )
                : [...selected, next];
        }

        updated = updated.map((item, index) => ({ ...item, position: index }));
        setSelected(updated);
        if (onChange) onChange(updated);
    }

    return (
        <div className="rounded-lg border bg-white p-4 dark:bg-zinc-900">
            <div className="flex items-center justify-between gap-3">
                <div>
                    <div className="text-sm font-medium">{label}</div>
                    <div className="text-xs text-gray-500">
                        Galería: seleccionar, subir y ordenar imágenes
                    </div>
                </div>
                <div className="flex items-center gap-2">
                    <button
                        type="button"
                        onClick={() => setIsDrawerOpen(true)}
                        className="rounded border border-primary px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary hover:text-white"
                    >
                        Abrir media
                    </button>
                    <button
                        type="button"
                        onClick={() => {
                            setSelected([]);
                            if (onChange) onChange([]);
                        }}
                        className="rounded border px-2 py-1 text-xs text-gray-600"
                    >
                        Limpiar
                    </button>
                </div>
            </div>

            <div className="mt-3">
                <div className="mb-2 text-sm font-medium">Seleccionadas</div>
                {selected.length === 0 ? (
                    <div className="flex h-32 items-center justify-center rounded-lg border border-dashed text-sm text-gray-500">
                        No hay imágenes seleccionadas
                    </div>
                ) : (
                    <ul className="space-y-2">
                        {selected.map(s => (
                            <li
                                key={s.media_id}
                                className="flex items-center gap-3 rounded border p-2"
                            >
                                <img
                                    src={normalizeUrl(s.url || s.path || s.filename || '')}
                                    alt={s.filename}
                                    className="h-16 w-24 object-cover"
                                    onError={e => {
                                        (e.currentTarget as HTMLImageElement).style.visibility =
                                            'hidden';
                                    }}
                                />
                                <div className="flex flex-1 flex-col min-w-0">
                                    <div className="flex items-center justify-between gap-2">
                                        <div className="text-sm font-medium truncate">
                                            {s.filename}
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <button
                                                type="button"
                                                onClick={() => setPrimary(s.media_id)}
                                                className={`px-2 py-1 text-xs ${s.is_primary ? 'bg-primary text-white rounded' : 'border rounded text-gray-600'}`}
                                            >
                                                {s.is_primary ? 'Principal' : 'Set primary'}
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => move(s.media_id, 'up')}
                                                className="px-2 py-1 text-xs border rounded"
                                            >
                                                ↑
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => move(s.media_id, 'down')}
                                                className="px-2 py-1 text-xs border rounded"
                                            >
                                                ↓
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => remove(s.media_id)}
                                                className="px-2 py-1 text-xs text-red-600"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <input
                                        value={s.caption || ''}
                                        onChange={e => updateCaption(s.media_id, e.target.value)}
                                        placeholder="Caption (optional)"
                                        className="mt-2 block w-full px-2 py-1 border border-border rounded text-sm"
                                    />
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>

            {allowUpload && (
                <div className="mt-4">
                    <label className="block text-sm font-medium">Subir nuevas imágenes</label>
                    <input
                        type="file"
                        accept="image/*"
                        className="mt-1 block w-full"
                        multiple={!singleSelect}
                        onChange={onFileChange}
                    />
                    {uploading && <div className="mt-2 text-xs text-gray-500">Subiendo...</div>}
                </div>
            )}

            {isDrawerOpen && (
                <MediaPickerModal
                    isOpen={isDrawerOpen}
                    onClose={() => setIsDrawerOpen(false)}
                    onSelect={(media: any, caption: string, altText: string) => {
                        handleDrawerSelect(media, caption, altText);
                    }}
                />
            )}
        </div>
    );
}
