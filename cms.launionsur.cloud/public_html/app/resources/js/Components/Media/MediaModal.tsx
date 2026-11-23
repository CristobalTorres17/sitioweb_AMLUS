import React, { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';
import { XMarkIcon, TrashIcon } from '@heroicons/react/24/outline';
import type { MediaItem, MediaUsageDetail } from '@/types/media';

interface Tag {
    id: number;
    name: string;
    color: string;
}

interface MediaModalProps {
    media: MediaItem;
    onClose: () => void;
    onDelete: (id: number) => void;
    availableTags: Tag[];
}

const USAGE_SECTIONS: Array<{
    key: keyof Pick<
        MediaUsageDetail,
        'articles' | 'banners' | 'services' | 'cms_blocks' | 'events' | 'featured_events'
    >;
    label: string;
    color: string;
    route?: string;
}> = [
    { key: 'articles', label: 'Noticias', color: 'bg-blue-50 text-blue-700 border-blue-200' },
    {
        key: 'events',
        label: 'Galería de Eventos',
        color: 'bg-green-50 text-green-700 border-green-200',
    },
    {
        key: 'featured_events',
        label: 'Portada de Eventos',
        color: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    },
    { key: 'banners', label: 'Banners', color: 'bg-yellow-50 text-yellow-700 border-yellow-200' },
    {
        key: 'services',
        label: 'Servicios',
        color: 'bg-purple-50 text-purple-700 border-purple-200',
    },
    { key: 'cms_blocks', label: 'Bloques CMS', color: 'bg-pink-50 text-pink-700 border-pink-200' },
];

export default function MediaModal({ media, onClose, onDelete, availableTags }: MediaModalProps) {
    const [selectedTags, setSelectedTags] = useState<number[]>(media.tags.map(t => t.id));
    const [isActive, setIsActive] = useState(media.is_active);
    const [isSaving, setIsSaving] = useState(false);
    const [usageDetail, setUsageDetail] = useState<MediaUsageDetail | null>(null);
    const [isLoadingUsage, setIsLoadingUsage] = useState(true);

    useEffect(() => {
        setIsLoadingUsage(true);
        fetch(`/api/media/${media.id}/usage`)
            .then(r => r.json())
            .then(json => {
                if (json.success) setUsageDetail(json.data as MediaUsageDetail);
            })
            .catch(() => {
                /* non-fatal */
            })
            .finally(() => setIsLoadingUsage(false));
    }, [media.id]);

    const handleSave = async () => {
        setIsSaving(true);
        router.patch(
            route('media.update', media.id),
            { tags: selectedTags, is_active: isActive },
            { onSuccess: () => onClose() },
        );
    };

    const toggleTag = (tagId: number) => {
        setSelectedTags(prev =>
            prev.includes(tagId) ? prev.filter(id => id !== tagId) : [...prev, tagId],
        );
    };

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
            if (typeof raw === 'object') {
                if ((raw as any).png) return (raw as any).png;
                if ((raw as any).webp) return (raw as any).webp;
                if ((raw as any).original) {
                    const o = (raw as any).original;
                    if (typeof o === 'string') return o;
                    if (o.png) return o.png;
                    if (o.webp) return o.webp;
                }
                for (const v of Object.values(raw)) {
                    if (!v) continue;
                    if (typeof v === 'string') return v;
                    if ((v as any).png) return (v as any).png;
                    if ((v as any).webp) return (v as any).webp;
                }
            }
        } catch (e) {
            // ignore
        }
        return String(raw);
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div className="w-full max-w-2xl rounded-lg bg-white shadow-lg max-h-[90vh] overflow-y-auto">
                {/* Header */}
                <div className="flex items-center justify-between border-b border-gray-200 p-4">
                    <h2 className="text-lg font-semibold text-gray-900">
                        {media.original_filename}
                    </h2>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                        <XMarkIcon className="h-6 w-6" />
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {/* Image preview */}
                    <div className="flex justify-center">
                        <img
                            src={
                                normalizeUrl((media.urls as any)?.medium) ||
                                normalizeUrl(media.urls as any)
                            }
                            alt={media.original_filename}
                            className="max-h-64 rounded-lg object-contain"
                        />
                    </div>

                    {/* Metadata */}
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <p className="text-xs font-semibold text-gray-500 uppercase">Tamaño</p>
                            <p className="text-sm text-gray-900">{formatFileSize(media.size)}</p>
                        </div>
                        <div>
                            <p className="text-xs font-semibold text-gray-500 uppercase">Tipo</p>
                            <p className="text-sm text-gray-900">{media.mime_type}</p>
                        </div>
                        <div>
                            <p className="text-xs font-semibold text-gray-500 uppercase">
                                Subido por
                            </p>
                            <p className="text-sm text-gray-900">{media.user?.name ?? '—'}</p>
                        </div>
                        <div>
                            <p className="text-xs font-semibold text-gray-500 uppercase">Fecha</p>
                            <p className="text-sm text-gray-900">
                                {new Date(media.created_at).toLocaleDateString()}
                            </p>
                        </div>
                        {(media.metadata?.width || media.width) && (
                            <div>
                                <p className="text-xs font-semibold text-gray-500 uppercase">
                                    Dimensiones
                                </p>
                                <p className="text-sm text-gray-900">
                                    {media.metadata?.width ?? media.width}×
                                    {media.metadata?.height ?? media.height}px
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Usado en */}
                    <div className="border-t border-gray-200 pt-4">
                        <p className="text-sm font-semibold text-gray-900 mb-3">Usado en</p>
                        {isLoadingUsage ? (
                            <p className="text-xs text-gray-400">Cargando...</p>
                        ) : !usageDetail || usageDetail.total_count === 0 ? (
                            <p className="text-xs text-gray-500 italic">
                                Este archivo no está en uso.
                            </p>
                        ) : (
                            <div className="space-y-3">
                                {USAGE_SECTIONS.map(section => {
                                    const items = usageDetail[section.key] as Array<{
                                        id: number;
                                        title?: string;
                                        name?: string;
                                    }>;
                                    if (!items || items.length === 0) return null;
                                    return (
                                        <div key={section.key}>
                                            <p className="text-xs font-semibold text-gray-500 uppercase mb-1">
                                                {section.label} ({items.length})
                                            </p>
                                            <div className="flex flex-wrap gap-1">
                                                {items.map(entity => (
                                                    <span
                                                        key={entity.id}
                                                        className={`inline-flex items-center rounded-md border px-2 py-0.5 text-xs ${section.color}`}
                                                    >
                                                        {entity.title ??
                                                            entity.name ??
                                                            `#${entity.id}`}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* Status toggle */}
                    <div className="flex items-center justify-between border-t border-gray-200 pt-4">
                        <div>
                            <p className="text-sm font-medium text-gray-900">Activo</p>
                            <p className="text-xs text-gray-500">Visible en la API pública</p>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={isActive}
                                onChange={e => setIsActive(e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    {/* Tags */}
                    <div className="border-t border-gray-200 pt-4">
                        <p className="text-sm font-medium text-gray-900 mb-3">Etiquetas</p>
                        <div className="flex flex-wrap gap-2">
                            {availableTags.map(tag => (
                                <button
                                    key={tag.id}
                                    onClick={() => toggleTag(tag.id)}
                                    className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-all ${
                                        selectedTags.includes(tag.id)
                                            ? 'text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    }`}
                                    style={
                                        selectedTags.includes(tag.id)
                                            ? { backgroundColor: tag.color || '#6366f1' }
                                            : {}
                                    }
                                >
                                    {tag.name}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="flex justify-between border-t border-gray-200 px-6 py-4">
                    <button
                        onClick={() => onDelete(media.id)}
                        className="inline-flex items-center gap-2 rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        <TrashIcon className="h-4 w-4" />
                        Eliminar
                    </button>
                    <div className="flex gap-3">
                        <button
                            onClick={onClose}
                            className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={handleSave}
                            disabled={isSaving}
                            className="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {isSaving ? 'Guardando...' : 'Guardar'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
