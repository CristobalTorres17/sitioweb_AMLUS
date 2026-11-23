import React from 'react';
import { TrashIcon, FilmIcon, MusicalNoteIcon, DocumentIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import type { MediaItem } from '@/types/media';

function MediaThumb({ item }: { item: MediaItem }) {
    const mime = item.mime_type ?? '';
    if (mime.startsWith('video/')) {
        return (
            <div className="h-full w-full flex flex-col items-center justify-center bg-gray-800 text-gray-200 gap-1">
                <FilmIcon className="h-10 w-10 opacity-70" />
                <span className="text-xs uppercase tracking-wide opacity-50">
                    {mime.split('/')[1]}
                </span>
            </div>
        );
    }
    if (mime.startsWith('audio/')) {
        return (
            <div className="h-full w-full flex flex-col items-center justify-center bg-gray-700 text-gray-200 gap-1">
                <MusicalNoteIcon className="h-10 w-10 opacity-70" />
                <span className="text-xs uppercase tracking-wide opacity-50">
                    {mime.split('/')[1]}
                </span>
            </div>
        );
    }
    if (!mime.startsWith('image/')) {
        return (
            <div className="h-full w-full flex flex-col items-center justify-center bg-gray-200 text-gray-500 gap-1">
                <DocumentIcon className="h-10 w-10 opacity-70" />
                <span className="text-xs uppercase tracking-wide opacity-50">
                    {mime.split('/')[1] ?? 'file'}
                </span>
            </div>
        );
    }
    return (
        <img
            src={item.urls.thumbnail}
            alt={item.original_filename}
            className="h-full w-full object-cover group-hover:scale-105 transition-transform"
            onError={e => {
                (e.target as HTMLImageElement).style.display = 'none';
            }}
        />
    );
}

interface MediaGridProps {
    media: MediaItem[];
    onMediaSelect: (item: MediaItem) => void;
    onDelete: (id: number) => void;
}

const CONTEXT_BADGES: Array<{
    key: keyof NonNullable<MediaItem['usage_counts']>;
    label: string;
    color: string;
}> = [
    { key: 'articles', label: 'Noticias', color: 'bg-blue-100 text-blue-700' },
    { key: 'events', label: 'Eventos', color: 'bg-green-100 text-green-700' },
    { key: 'banners', label: 'Banners', color: 'bg-yellow-100 text-yellow-700' },
    { key: 'services', label: 'Servicios', color: 'bg-purple-100 text-purple-700' },
    { key: 'cms_blocks', label: 'Bloques', color: 'bg-pink-100 text-pink-700' },
];

export default function MediaGrid({ media, onMediaSelect, onDelete }: MediaGridProps) {
    const formatFileSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    };

    function normalizeUrl(raw: any) {
        if (!raw) return '';
        if (typeof raw === 'string') return raw;
        try {
            // variant object: { png: '/x.png', webp: '/x.webp' } or { original: { png: '...', webp: '...' } }
            if (typeof raw === 'object') {
                if ((raw as any).png) return (raw as any).png;
                if ((raw as any).webp) return (raw as any).webp;
                if ((raw as any).original) {
                    const o = (raw as any).original;
                    if (typeof o === 'string') return o;
                    if (o.png) return o.png;
                    if (o.webp) return o.webp;
                }
                // urls map: pick first string or nested png/webp
                for (const v of Object.values(raw)) {
                    if (!v) continue;
                    if (typeof v === 'string') return v;
                    if ((v as any).png) return (v as any).png;
                    if ((v as any).webp) return (v as any).webp;
                    if ((v as any).original) {
                        const o2 = (v as any).original;
                        if (typeof o2 === 'string') return o2;
                        if (o2.png) return o2.png;
                        if (o2.webp) return o2.webp;
                    }
                }
            }
        } catch (e) {
            // fallthrough
        }
        return String(raw);
    }

    return (
        <div className="px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                {media.map(item => (
                    <div
                        key={item.id}
                        className="group relative rounded-lg overflow-hidden bg-gray-100 shadow hover:shadow-lg transition-shadow"
                    >
                        {/* Thumbnail / media type preview */}
                        <div
                            className="aspect-square overflow-hidden cursor-pointer"
                            onClick={() => onMediaSelect(item)}
                        >
                            <img
                                src={
                                    normalizeUrl((item.urls as any)?.thumbnail) ||
                                    normalizeUrl(item.urls as any)
                                }
                                alt={item.original_filename}
                                className="h-full w-full object-cover group-hover:scale-105 transition-transform"
                            />
                        </div>

                        {/* Overlay */}
                        <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex flex-col justify-between p-2">
                            {/* Top: inactive badge */}
                            <div className="flex justify-end gap-1">
                                {!item.is_active && (
                                    <span className="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                        Inactive
                                    </span>
                                )}
                            </div>

                            {/* Bottom actions */}
                            <div className="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link
                                    href={route('media.edit', item.id)}
                                    className="flex-1 inline-flex items-center justify-center rounded bg-primary px-2 py-1 text-xs font-semibold text-gold hover:bg-primary-dark"
                                >
                                    Edit
                                </Link>
                                <button
                                    onClick={e => {
                                        e.stopPropagation();
                                        onDelete(item.id);
                                    }}
                                    className="rounded bg-red-600 px-2 py-1 text-xs font-semibold text-white hover:bg-red-700"
                                >
                                    <TrashIcon className="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        {/* Usage context badges */}
                        {item.usage_counts && item.usage_counts.total > 0 && (
                            <div className="absolute top-1 left-1 flex flex-wrap gap-1 max-w-[80%]">
                                {CONTEXT_BADGES.filter(
                                    b => (item.usage_counts![b.key] as number) > 0,
                                ).map(b => (
                                    <span
                                        key={b.key}
                                        title={`${item.usage_counts![b.key]} ${b.label}`}
                                        className={`inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-semibold leading-none ${b.color}`}
                                    >
                                        {b.label}{' '}
                                        {(item.usage_counts![b.key] as number) > 1
                                            ? `×${item.usage_counts![b.key]}`
                                            : ''}
                                    </span>
                                ))}
                            </div>
                        )}

                        {/* Unused badge */}
                        {item.usage_counts && item.usage_counts.total === 0 && (
                            <div className="absolute top-1 left-1">
                                <span className="inline-flex items-center rounded-full bg-gray-200 px-1.5 py-0.5 text-[10px] font-medium text-gray-500">
                                    Sin uso
                                </span>
                            </div>
                        )}

                        {/* Info footer */}
                        <div className="p-2 text-xs bg-white">
                            <p className="font-medium text-gray-700 truncate">
                                {item.original_filename}
                            </p>
                            <p className="text-gray-500 text-[10px]">{formatFileSize(item.size)}</p>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
