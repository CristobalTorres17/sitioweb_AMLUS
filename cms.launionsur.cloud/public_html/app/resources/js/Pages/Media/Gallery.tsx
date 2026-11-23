import React, { useState, useCallback } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FileUploadZone from '@/Components/Media/FileUploadZone';
import MediaGrid from '@/Components/Media/MediaGrid';
import MediaFilters from '@/Components/Media/MediaFilters';
import MediaModal from '@/Components/Media/MediaModal';
import FolderSidebar, { CollectionNode } from '@/Components/Media/FolderSidebar';
import PaginationComponent from '@/Components/Pagination';
import type { MediaItem } from '@/types/media';

interface Tag {
    id: number;
    name: string;
    color: string;
}

type MediaContext = '' | 'articles' | 'events' | 'banners' | 'services' | 'cms_blocks' | 'unused';

export default function Gallery() {
    const { props } = usePage();
    const {
        media = [],
        pagination = {},
        filters = {},
        availableTags = [],
        collectionsTree = [],
    } = props as any;

    const [search, setSearch] = useState(filters.search || '');
    const [selectedTags, setSelectedTags] = useState<number[]>(filters.tags || []);
    const [context, setContext] = useState<MediaContext>(filters.context || '');
    const [collectionId, setCollectionId] = useState<number | null>(filters.collection_id ?? null);
    const [selectedMedia, setSelectedMedia] = useState<MediaItem | null>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [uploadProgress, setUploadProgress] = useState(0);
    const [isUploading, setIsUploading] = useState(false);
    const [uploadLabel, setUploadLabel] = useState('');

    const handleSearch = useCallback(
        (value: string) => {
            setSearch(value);
            router.get(route('media.index'), {
                search: value,
                tags: selectedTags,
                context,
                collection_id: collectionId,
                per_page: pagination.per_page,
            });
        },
        [selectedTags, context, collectionId],
    );

    const handleFilterTags = useCallback(
        (tags: number[]) => {
            setSelectedTags(tags);
            router.get(route('media.index'), {
                search,
                tags,
                context,
                collection_id: collectionId,
                per_page: pagination.per_page,
            });
        },
        [search, context, collectionId],
    );

    const handleContextChange = useCallback(
        (newContext: MediaContext) => {
            setContext(newContext);
            router.get(route('media.index'), {
                search,
                tags: selectedTags,
                context: newContext,
                collection_id: collectionId,
                per_page: pagination.per_page,
            });
        },
        [search, selectedTags, collectionId],
    );

    const handleCollectionSelect = useCallback(
        (id: number | null) => {
            setCollectionId(id);
            router.get(route('media.index'), {
                search,
                tags: selectedTags,
                context,
                collection_id: id,
                per_page: pagination.per_page,
            });
        },
        [search, selectedTags, context],
    );

    const handlePageChange = (page: number) => {
        router.get(route('media.index'), {
            page,
            search,
            tags: selectedTags,
            context,
            collection_id: collectionId,
            per_page: pagination.per_page,
        });
    };

    const uploadFile = useCallback(
        (file: File, index: number, total: number) => {
            return new Promise<void>((resolve, reject) => {
                if (!file.type.startsWith('image/')) {
                    reject(new Error('Por favor, selecciona archivos de imagen'));
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    reject(new Error('El tamaño del archivo debe ser menor a 10MB'));
                    return;
                }

                const formData = new FormData();
                formData.append('image', file);
                if (collectionId !== null) {
                    formData.append('collection_id', String(collectionId));
                }

                setIsUploading(true);
                setUploadLabel(`Subiendo ${index + 1} de ${total}`);
                setUploadProgress(0);

                try {
                    const xhr = new XMLHttpRequest();

                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            setUploadProgress(Math.round(percentComplete));
                        }
                    });

                    xhr.addEventListener('load', () => {
                        if (xhr.status === 302 || xhr.status === 201) {
                            resolve();
                        } else {
                            reject(new Error('Error al subir el archivo'));
                        }
                    });

                    xhr.addEventListener('error', () => {
                        reject(new Error('Error al subir el archivo'));
                    });

                    xhr.open('POST', route('media.store'));
                    xhr.setRequestHeader(
                        'X-CSRF-TOKEN',
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
                    );
                    xhr.send(formData);
                } catch (error) {
                    reject(error instanceof Error ? error : new Error('Error al subir el archivo'));
                }
            });
        },
        [collectionId],
    );

    const handleUpload = useCallback(
        async (files: File[]) => {
            if (files.length === 0) return;

            const images = files.filter(file => file.type.startsWith('image/'));
            if (images.length === 0) {
                alert('Por favor, selecciona al menos una imagen');
                return;
            }

            setIsUploading(true);

            try {
                for (let index = 0; index < images.length; index++) {
                    await uploadFile(images[index], index, images.length);
                }

                router.reload();
            } catch (error) {
                alert(error instanceof Error ? error.message : 'Error al subir los archivos');
            } finally {
                setIsUploading(false);
                setUploadProgress(0);
                setUploadLabel('');
            }
        },
        [uploadFile],
    );

    const handleUploadZone = useCallback(
        (files: File[]) => {
            void handleUpload(files);
        },
        [handleUpload],
    );

    const showUploadZone = true;

    const uploadMessage = uploadLabel || 'Subiendo imágenes...';

    const handleMediaSelect = (item: MediaItem) => {
        setSelectedMedia(item);
        setIsModalOpen(true);
    };

    const handleDelete = (mediaId: number) => {
        if (!confirm('¿Estás seguro de que deseas eliminar este archivo multimedia?')) return;

        router.delete(route('media.destroy', mediaId), {
            onSuccess: () => {
                setIsModalOpen(false);
                router.reload();
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Galería de Medios" />

            <div className="flex h-[calc(100vh-4rem)]">
                {/* Folder Sidebar */}
                <FolderSidebar
                    tree={collectionsTree as CollectionNode[]}
                    selectedCollectionId={collectionId}
                    onSelect={handleCollectionSelect}
                />

                {/* Main content */}
                <div className="flex-1 min-w-0 overflow-y-auto">
                    <div className="space-y-6 py-6">
                        {/* Header */}
                        <div className="px-4 sm:px-6 lg:px-8">
                            <div className="sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <h1 className="text-2xl font-bold leading-7 text-gray-900">
                                        Galería de Medios
                                    </h1>
                                    <p className="mt-2 text-sm text-gray-600">
                                        Gestiona tus archivos multimedia aquí. Puedes subir,
                                        organizar y eliminar imágenes según sea necesario.
                                    </p>
                                </div>
                                <div className="mt-4 sm:mt-0">
                                    <button
                                        onClick={() => router.visit(route('media.create'))}
                                        className="inline-flex items-center rounded-md  bg-primary text-gold px-3 py-2 text-sm font-semibold hover:bg-primary/70"
                                    >
                                        Subir Nuevo Archivo
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Upload Zone */}
                        {showUploadZone && (
                            <div className="px-4 sm:px-6 lg:px-8">
                                {isUploading && (
                                    <div className="mb-3 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                                        {uploadMessage} {uploadProgress}%
                                    </div>
                                )}
                                <FileUploadZone
                                    onFilesSelected={handleUploadZone}
                                    isLoading={isUploading}
                                    progress={uploadProgress}
                                />
                            </div>
                        )}

                        {/* Filters */}
                        <MediaFilters
                            search={search}
                            onSearchChange={handleSearch}
                            selectedTags={selectedTags}
                            onTagsChange={handleFilterTags}
                            availableTags={availableTags}
                            context={context}
                            onContextChange={handleContextChange}
                        />

                        {/* Media Grid */}
                        {media.length > 0 ? (
                            <>
                                <MediaGrid
                                    media={media}
                                    onMediaSelect={handleMediaSelect}
                                    onDelete={handleDelete}
                                />

                                {/* Pagination */}
                                {pagination.last_page > 1 && (
                                    <PaginationComponent
                                        currentPage={pagination.current_page}
                                        lastPage={pagination.last_page}
                                        onPageChange={handlePageChange}
                                    />
                                )}
                            </>
                        ) : (
                            <div className="text-center py-12">
                                <p className="text-gray-500">No media found</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Modal */}
            {isModalOpen && selectedMedia && (
                <MediaModal
                    media={selectedMedia}
                    onClose={() => setIsModalOpen(false)}
                    onDelete={handleDelete}
                    availableTags={availableTags}
                />
            )}
        </AuthenticatedLayout>
    );
}
