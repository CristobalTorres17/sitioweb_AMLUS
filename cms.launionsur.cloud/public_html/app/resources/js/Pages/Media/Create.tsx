import React, { useEffect, useState, useCallback } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FileUploadZone from '@/Components/Media/FileUploadZone';
import type { CollectionNode } from '@/Components/Media/FolderSidebar';

type UploadItem = {
    id: string;
    file: File;
    previewUrl: string;
    title: string;
    description: string;
    altText: string;
};

function flattenTree(nodes: CollectionNode[], depth = 0): Array<{ id: number; label: string }> {
    const result: Array<{ id: number; label: string }> = [];

    for (const node of nodes) {
        result.push({ id: node.id, label: `${'\u00a0\u00a0'.repeat(depth)}${node.name}` });
        if (node.children.length > 0) {
            result.push(...flattenTree(node.children, depth + 1));
        }
    }

    return result;
}

export default function Create() {
    const { props } = usePage();
    const { availableTags = [], collectionsTree = [] } = props as any;

    const [items, setItems] = useState<UploadItem[]>([]);
    const [collectionId, setCollectionId] = useState<number | null>(null);
    const [selectedTags, setSelectedTags] = useState<number[]>([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [validationError, setValidationError] = useState<string | null>(null);
    const itemsRef = React.useRef<UploadItem[]>([]);

    useEffect(() => {
        itemsRef.current = items;
    }, [items]);

    useEffect(() => {
        return () => {
            itemsRef.current.forEach(item => URL.revokeObjectURL(item.previewUrl));
        };
    }, []);

    // Determine readable text color (black or white) based on background hex
    const readableTextColor = (hex?: string) => {
        if (!hex) return '#000';
        // Strip '#'
        const h = hex.replace('#', '');
        const r = parseInt(h.substring(0, 2), 16);
        const g = parseInt(h.substring(2, 4), 16);
        const b = parseInt(h.substring(4, 6), 16);
        // Perceived luminance
        const luminance = 0.2126 * (r / 255) + 0.7152 * (g / 255) + 0.0722 * (b / 255);
        return luminance > 0.6 ? '#000' : '#fff';
    };

    const handleFileSelect = useCallback(async (files: File[]) => {
        if (files.length === 0) return;

        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        if (imageFiles.length !== files.length) {
            setValidationError('Solo se permiten archivos de imagen válidos');
            return;
        }

        const oversized = imageFiles.find(file => file.size > 10 * 1024 * 1024);
        if (oversized) {
            setValidationError('Ninguna imagen debe superar 10 MB');
            return;
        }

        const validated = await Promise.all(
            imageFiles.map(
                file =>
                    new Promise<UploadItem | { error: string }>(resolve => {
                        const objectUrl = URL.createObjectURL(file);
                        const img = new Image();

                        img.onload = () => {
                            if (img.width < 100 || img.height < 100) {
                                URL.revokeObjectURL(objectUrl);
                                resolve({ error: 'La imagen es muy pequeña (mínimo 100×100)' });
                                return;
                            }

                            if (img.width > 5000 || img.height > 5000) {
                                URL.revokeObjectURL(objectUrl);
                                resolve({
                                    error: 'La imagen excede las dimensiones máximas (5000×5000)',
                                });
                                return;
                            }

                            resolve({
                                id: `${file.name}-${file.size}-${Date.now()}-${Math.random()}`,
                                file,
                                previewUrl: objectUrl,
                                title: file.name,
                                description: '',
                                altText: '',
                            });
                        };

                        img.onerror = () => {
                            URL.revokeObjectURL(objectUrl);
                            resolve({
                                error: 'No se pudo leer una de las imágenes seleccionadas',
                            });
                        };

                        img.src = objectUrl;
                    }),
            ),
        );

        const failed = validated.find((entry): entry is { error: string } => 'error' in entry);
        if (failed) {
            setValidationError(failed.error);
            return;
        }

        setValidationError(null);
        setItems(prev => [...prev, ...(validated as UploadItem[])]);
    }, []);

    const removeItem = useCallback((id: string) => {
        setItems(prev => {
            const item = prev.find(current => current.id === id);
            if (item) {
                URL.revokeObjectURL(item.previewUrl);
            }

            return prev.filter(current => current.id !== id);
        });
    }, []);

    const updateItem = useCallback(
        (id: string, field: 'title' | 'description' | 'altText', value: string) => {
            setItems(prev =>
                prev.map(item => (item.id === id ? { ...item, [field]: value } : item)),
            );
        },
        [],
    );

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (items.length === 0) {
            setValidationError('Por favor selecciona una o varias imágenes');
            return;
        }

        const formData = new FormData();

        items.forEach((item, index) => {
            formData.append(`images[${index}]`, item.file);
            formData.append(`titles[${index}]`, item.title);
            formData.append(`descriptions[${index}]`, item.description);
            formData.append(`alt_texts[${index}]`, item.altText);
        });

        if (collectionId !== null) {
            formData.append('collection_id', String(collectionId));
        }

        selectedTags.forEach(tagId => {
            formData.append('tags[]', String(tagId));
        });

        setIsSubmitting(true);
        setValidationError(null);

        try {
            const response = await fetch(route('media.store'), {
                method: 'POST',
                body: formData,
                credentials: 'include',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN':
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
                },
            });

            if (response.ok) {
                router.visit(route('media.index'));
                return;
            }

            if (response.status === 422) {
                const payload = await response.json();
                const firstError =
                    payload?.message ||
                    Object.values(payload?.errors || {})
                        .flat()
                        .find(Boolean) ||
                    'Error al subir la imagen. Intenta nuevamente.';
                setValidationError(String(firstError));
                return;
            }

            setValidationError('Error al subir las imágenes. Intenta nuevamente.');
        } catch {
            setValidationError('Error al subir las imágenes. Intenta nuevamente.');
        } finally {
            setIsSubmitting(false);
        }
    };

    const toggleTag = (tagId: number) => {
        setSelectedTags(current =>
            current.includes(tagId) ? current.filter(id => id !== tagId) : [...current, tagId],
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Subir Imagen" />

            <div className="space-y-6 py-6">
                {/* Header */}
                <div className="px-4 sm:px-6 lg:px-8">
                    <div>
                        <h1 className="text-2xl font-bold leading-7 text-gray-900">
                            Subir Nueva Imagen
                        </h1>
                        <p className="mt-2 text-sm text-gray-600">
                            Carga una imagen, añade etiquetas e información
                        </p>
                    </div>
                </div>

                {/* Form */}
                <div className="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 media-form">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Upload Zone */}
                        <div>
                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                Selecciona una o varias imágenes
                            </label>
                            <FileUploadZone
                                onFilesSelected={handleFileSelect}
                                isLoading={isSubmitting}
                            />
                        </div>

                        {/* Validation Error */}
                        {validationError && (
                            <div className="rounded-md bg-red-50 p-4">
                                <p className="text-sm font-medium text-red-800">
                                    {validationError}
                                </p>
                            </div>
                        )}

                        {/* Preview */}
                        {items.length > 0 && (
                            <div className="flex flex-col gap-3">
                                <label className="block text-sm font-medium text-gray-900">
                                    Imágenes seleccionadas ({items.length})
                                </label>
                                <div className="grid grid-cols-1 gap-4">
                                    {items.map(item => (
                                        <div
                                            key={item.id}
                                            className="overflow-hidden rounded-lg border border-border bg-white shadow-sm"
                                        >
                                            <div className="flex items-start gap-4 p-3">
                                                <img
                                                    src={item.previewUrl}
                                                    alt={item.title}
                                                    className="h-28 w-28 flex-shrink-0 rounded-md object-cover"
                                                />
                                                <div className="min-w-0 flex-1 space-y-3">
                                                    <div className="flex items-start justify-between gap-3">
                                                        <div className="min-w-0">
                                                            <div className="text-sm font-medium text-gray-900 truncate">
                                                                {item.file.name}
                                                            </div>
                                                            <div className="text-xs text-gray-500">
                                                                {Math.round(item.file.size / 1024)}{' '}
                                                                KB
                                                            </div>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            onClick={() => removeItem(item.id)}
                                                            className="rounded-md border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                                        >
                                                            Remover
                                                        </button>
                                                    </div>

                                                    <div className="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label className="block text-xs font-medium text-gray-700 mb-1">
                                                                Nombre
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={item.title}
                                                                onChange={e =>
                                                                    updateItem(
                                                                        item.id,
                                                                        'title',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="block w-full rounded-md border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="block text-xs font-medium text-gray-700 mb-1">
                                                                Descripción
                                                            </label>
                                                            <textarea
                                                                value={item.description}
                                                                onChange={e =>
                                                                    updateItem(
                                                                        item.id,
                                                                        'description',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                rows={3}
                                                                className="block w-full rounded-md border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                                                placeholder="Descripción breve de la imagen"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div>
                            <label className="block text-sm font-medium text-gray-900 mb-1">
                                Carpeta
                            </label>
                            <select
                                value={collectionId ?? ''}
                                onChange={e =>
                                    setCollectionId(e.target.value ? Number(e.target.value) : null)
                                }
                                className="block w-full rounded-md border border-border py-2 px-3 text-sm placeholder:text-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            >
                                <option value="">Sin carpeta</option>
                                {flattenTree(collectionsTree as CollectionNode[]).map(option => (
                                    <option key={option.id} value={option.id}>
                                        {option.label}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Tags */}
                        <div>
                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                Etiquetas
                            </label>
                            <div className="flex flex-wrap gap-2">
                                {availableTags.map((tag: any) => {
                                    const selected = selectedTags.includes(tag.id);
                                    const defaultBg = 'var(--color-primary)';
                                    const bg = selected ? tag.color || defaultBg : 'transparent';
                                    const color = selected
                                        ? readableTextColor(tag.color)
                                        : undefined;
                                    return (
                                        <button
                                            key={tag.id}
                                            type="button"
                                            onClick={() => toggleTag(tag.id)}
                                            aria-pressed={selected}
                                            className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 ${selected ? '' : 'text-text-primary border border-border hover:bg-bg-secondary'}`}
                                            style={{
                                                backgroundColor: bg,
                                                color: color,
                                                cursor: 'pointer',
                                            }}
                                        >
                                            {tag.name}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex gap-3 justify-end">
                            <button
                                type="button"
                                onClick={() => router.visit(route('media.index'))}
                                className="inline-flex items-center rounded-md bg-surface px-3 py-2 text-sm font-semibold text-text-primary shadow-sm border border-border hover:bg-bg-secondary"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                disabled={isSubmitting || items.length === 0}
                                className="inline-flex items-center rounded-md bg-primary text-gold px-3 py-2 text-sm font-semibold shadow-sm hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-primary"
                            >
                                {isSubmitting ? 'Subiendo...' : 'Subir Imágenes'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
