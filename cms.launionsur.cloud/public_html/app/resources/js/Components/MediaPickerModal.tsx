import React, { useEffect, useMemo, useState } from 'react';
import {
    FolderIcon,
    FolderOpenIcon,
    PhotoIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { useMediaLibrary } from '@/Hooks/useMediaLibrary';
import type { CollectionNode } from '@/Components/Media/FolderSidebar';
import type { MediaRef } from '@/types/media';

interface MediaPickerModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSelect: (media: MediaRef, caption: string, altText: string) => void;
}

// Simple read-only folder tree node for the picker panel
function PickerFolderNode({
    node,
    depth,
    selectedId,
    onSelect,
}: {
    node: CollectionNode;
    depth: number;
    selectedId: number | null;
    onSelect: (id: number) => void;
}) {
    const [open, setOpen] = useState(depth === 0);
    const isSelected = selectedId === node.id;
    const hasChildren = node.children.length > 0;

    return (
        <div>
            <button
                type="button"
                onClick={() => onSelect(node.id)}
                style={{ paddingLeft: `${12 + depth * 12}px` }}
                className={`w-full flex items-center gap-1.5 pr-2 py-1.5 text-sm transition-colors ${
                    isSelected
                        ? 'bg-primary text-gold font-semibold'
                        : 'text-gray-700 hover:bg-gray-100'
                }`}
            >
                {hasChildren ? (
                    <span
                        onClick={e => {
                            e.stopPropagation();
                            setOpen(o => !o);
                        }}
                        className="shrink-0"
                    >
                        {open ? (
                            <FolderOpenIcon className="h-4 w-4" style={{ color: node.color }} />
                        ) : (
                            <FolderIcon className="h-4 w-4" style={{ color: node.color }} />
                        )}
                    </span>
                ) : (
                    <FolderIcon className="h-4 w-4 shrink-0" style={{ color: node.color }} />
                )}
                <span className="truncate flex-1 text-left">{node.name}</span>
                <span
                    className={`text-xs shrink-0 ${isSelected ? 'text-gold/70' : 'text-gray-400'}`}
                >
                    {node.media_count}
                </span>
            </button>
            {open && hasChildren && (
                <div>
                    {node.children.map(child => (
                        <PickerFolderNode
                            key={child.id}
                            node={child}
                            depth={depth + 1}
                            selectedId={selectedId}
                            onSelect={onSelect}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}

export default function MediaPickerModal({ isOpen, onClose, onSelect }: MediaPickerModalProps) {
    const [collectionId, setCollectionId] = useState<number | null>(null);
    const [tree, setTree] = useState<CollectionNode[]>([]);
    const [search, setSearch] = useState('');
    const [isMounted, setIsMounted] = useState(isOpen);
    const [isVisible, setIsVisible] = useState(false);

    const { items, pagination, loading, page, setPage } = useMediaLibrary({
        perPage: 20,
        autoFetch: isOpen,
        collectionId,
        search,
    });

    // Load folder tree when opened
    useEffect(() => {
        if (!isOpen) return;
        fetch('/api/media/collections')
            .then(r => r.json())
            .then(json => {
                if (json.success) setTree(json.data as CollectionNode[]);
            })
            .catch(() => {});
    }, [isOpen]);

    // Reset page when collection changes
    useEffect(() => {
        setPage(1);
    }, [collectionId]);

    useEffect(() => {
        setPage(1);
    }, [search]);

    useEffect(() => {
        let timeoutId: number | undefined;

        if (isOpen) {
            setIsMounted(true);
            requestAnimationFrame(() => setIsVisible(true));
        } else {
            setIsVisible(false);
            timeoutId = window.setTimeout(() => {
                setIsMounted(false);
            }, 250);
        }

        return () => {
            if (timeoutId) {
                window.clearTimeout(timeoutId);
            }
        };
    }, [isOpen]);

    const filteredTree = useMemo(() => tree, [tree]);

    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [caption, setCaption] = useState('');
    const [altText, setAltText] = useState('');

    const handleSelect = () => {
        if (!selectedId) return;
        const found = items.find(m => m.id === selectedId);
        if (found) {
            onSelect(
                {
                    id: found.id,
                    filename: found.filename,
                    path: found.path,
                    url: found.urls.original,
                },
                caption,
                altText,
            );
            setSelectedId(null);
            setCaption('');
            setAltText('');
            onClose();
        }
    };

    if (!isMounted) return null;

    const totalPages = pagination.last_page;

    return (
        <div
            className={`fixed inset-0 z-50 flex justify-end bg-black/50 transition-opacity duration-200 ${isVisible ? 'opacity-100' : 'opacity-0'}`}
            onClick={onClose}
        >
            <div
                className={`flex h-full w-full max-w-[min(72rem,88vw)] flex-col overflow-hidden bg-white shadow-2xl transition-transform duration-300 ease-out ${isVisible ? 'translate-x-0' : 'translate-x-full'}`}
                onClick={e => e.stopPropagation()}
            >
                {/* Header */}
                <div className="flex items-center justify-between border-b bg-white px-4 py-3">
                    <div>
                        <h2 className="text-lg font-bold text-gray-900">Galería de Imágenes</h2>
                        <p className="text-xs text-gray-500">Busca, filtra y elige una imagen</p>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Cerrar"
                    >
                        <XMarkIcon className="h-5 w-5" />
                    </button>
                </div>

                <div className="border-b bg-gray-50 px-4 py-3">
                    <div className="relative">
                        <MagnifyingGlassIcon className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="search"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Buscar por nombre de archivo..."
                            className="w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                        />
                    </div>
                </div>

                {/* Body: folder sidebar + media grid */}
                <div className="flex min-h-0 flex-1 overflow-hidden">
                    {/* Folder panel */}
                    <div className="w-56 shrink-0 overflow-y-auto border-r border-gray-200 bg-gray-50 py-2">
                        <button
                            type="button"
                            onClick={() => setCollectionId(null)}
                            className={`w-full flex items-center gap-2 px-3 py-1.5 text-sm transition-colors ${
                                collectionId === null
                                    ? 'bg-primary text-gold font-semibold'
                                    : 'text-gray-700 hover:bg-gray-100'
                            }`}
                        >
                            <PhotoIcon className="h-4 w-4 shrink-0 text-gray-400" />
                            Todos
                        </button>
                        {filteredTree.map(node => (
                            <PickerFolderNode
                                key={node.id}
                                node={node}
                                depth={0}
                                selectedId={collectionId}
                                onSelect={setCollectionId}
                            />
                        ))}
                    </div>

                    {/* Media Grid */}
                    <div className="flex-1 overflow-auto p-6">
                        {loading ? (
                            <div className="text-center text-gray-500 py-12">
                                Cargando imágenes...
                            </div>
                        ) : items.length === 0 ? (
                            <div className="text-center text-gray-500 py-12">
                                No hay imágenes para mostrar
                            </div>
                        ) : (
                            <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                {items.map(m => (
                                    <button
                                        key={m.id}
                                        onClick={() => setSelectedId(m.id)}
                                        className={`relative aspect-square rounded-lg overflow-hidden border-4 transition ${
                                            selectedId === m.id
                                                ? 'border-blue-500 shadow-lg ring-2 ring-blue-400 ring-offset-2'
                                                : 'border-gray-200 hover:border-gray-400'
                                        }`}
                                        type="button"
                                    >
                                        <img
                                            src={m.urls.thumbnail}
                                            alt={m.filename}
                                            className="w-full h-full object-cover"
                                            onError={e => {
                                                (e.target as HTMLImageElement).src =
                                                    '/placeholder.png';
                                            }}
                                        />
                                        {selectedId === m.id && (
                                            <div className="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                                                <svg
                                                    className="w-8 h-8 text-white"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    />
                                                </svg>
                                            </div>
                                        )}
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Metadata Editor */}
                {selectedId && (
                    <div className="border-t bg-blue-50 p-4 space-y-3">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Caption (subtítulo)
                                </label>
                                <input
                                    type="text"
                                    value={caption}
                                    onChange={e => setCaption(e.target.value)}
                                    placeholder="Ej: Foto del evento"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Alt Text (accesibilidad)
                                </label>
                                <input
                                    type="text"
                                    value={altText}
                                    onChange={e => setAltText(e.target.value)}
                                    placeholder="Ej: Personas en un evento"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                )}

                {/* Pagination */}
                {totalPages > 1 && (
                    <div className="border-t p-3 flex justify-center items-center gap-3 bg-gray-50">
                        <button
                            type="button"
                            onClick={() => setPage(Math.max(1, page - 1))}
                            disabled={page === 1}
                            className="px-3 py-1 text-sm border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            ← Anterior
                        </button>
                        <span className="text-sm text-gray-600">
                            Página <span className="font-semibold">{page}</span> de{' '}
                            <span className="font-semibold">{totalPages}</span>
                        </span>
                        <button
                            type="button"
                            onClick={() => setPage(Math.min(totalPages, page + 1))}
                            disabled={page === totalPages}
                            className="px-3 py-1 text-sm border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Siguiente →
                        </button>
                    </div>
                )}

                {/* Footer */}
                <div className="border-t p-4 bg-gray-50 flex justify-end gap-3">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        onClick={handleSelect}
                        disabled={!selectedId}
                        className="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        Insertar Imagen
                    </button>
                </div>
            </div>
        </div>
    );
}
