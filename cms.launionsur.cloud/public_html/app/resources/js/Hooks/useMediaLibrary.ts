import { useCallback, useEffect, useRef, useState } from 'react';
import type { MediaApiResponse, MediaItem, MediaPagination } from '@/types/media';

/**
 * Normalise any URL that might be relative, absolute, or cross-origin (during dev)
 * so it always points to the same origin as the current page.
 */
export function normalizeMediaUrl(url: string): string {
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

/**
 * Map a raw API media item to a normalised MediaItem, resolving variant URLs.
 */
export function normalizeMediaItem(raw: any): MediaItem {
    const thumbnail = normalizeMediaUrl(
        raw.urls?.thumbnail || raw.urls?.original || raw.url || `/storage/${raw.path}`,
    );
    const original = normalizeMediaUrl(raw.urls?.original || raw.url || `/storage/${raw.path}`);
    const medium = normalizeMediaUrl(raw.urls?.medium || original);
    const full = normalizeMediaUrl(raw.urls?.full || original);

    return {
        id: raw.id,
        filename: raw.filename,
        original_filename: raw.original_filename ?? raw.filename,
        mime_type: raw.mime_type ?? '',
        size: raw.size ?? 0,
        width: raw.width,
        height: raw.height,
        disk: raw.disk,
        is_active: raw.is_active ?? true,
        urls: { original, thumbnail, medium, full },
        metadata: raw.metadata ?? {},
        tags: raw.tags ?? [],
        user: raw.user,
        created_at: raw.created_at ?? '',
        path: raw.path,
    };
}

interface UseMediaLibraryOptions {
    /** Number of items per page (default: 24). */
    perPage?: number;
    /** Auto-fetch on mount (default: true). */
    autoFetch?: boolean;
    /** Filter by collection/folder id. */
    collectionId?: number | null;
    /** Search by filename/original filename. */
    search?: string;
    /** Filter by tags. */
    tags?: number[];
}

interface UseMediaLibraryReturn {
    items: MediaItem[];
    pagination: MediaPagination;
    loading: boolean;
    error: string | null;
    page: number;
    setPage: (page: number) => void;
    /** Re-fetch the current page. */
    refresh: () => void;
    /** Optimistically prepend a newly uploaded item. */
    prependItem: (item: MediaItem) => void;
    /** Optimistically remove an item by id. */
    removeItem: (id: number) => void;
}

const DEFAULT_PAGINATION: MediaPagination = {
    total: 0,
    per_page: 24,
    current_page: 1,
    last_page: 1,
};

const EMPTY_TAGS: number[] = [];

/**
 * Central hook for reading the media library from the public API endpoint.
 *
 * Uses `/api/media` (public, no auth required) so it works in any context —
 * authenticated admin pages and unauthenticated pickers alike.
 */
export function useMediaLibrary(options: UseMediaLibraryOptions = {}): UseMediaLibraryReturn {
    const { perPage = 24, autoFetch = true, collectionId, search = '' } = options;
    const tags = options.tags ?? EMPTY_TAGS;

    const [items, setItems] = useState<MediaItem[]>([]);
    const [pagination, setPagination] = useState<MediaPagination>(DEFAULT_PAGINATION);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [page, setPage] = useState(1);

    // Abort controller ref to cancel in-flight requests on unmount or re-fetch
    const abortRef = useRef<AbortController | null>(null);

    const fetchPage = useCallback(
        async (targetPage: number) => {
            // Cancel previous in-flight request
            abortRef.current?.abort();
            const controller = new AbortController();
            abortRef.current = controller;

            setLoading(true);
            setError(null);

            try {
                const params = new URLSearchParams();
                params.set('page', String(targetPage));
                params.set('per_page', String(perPage));

                if (collectionId != null) {
                    params.set('collection_id', String(collectionId));
                }

                if (search.trim()) {
                    params.set('search', search.trim());
                }

                tags.forEach(tagId => {
                    params.append('tags[]', String(tagId));
                });

                const res = await fetch(`/api/media?${params.toString()}`, {
                    credentials: 'include',
                    headers: { Accept: 'application/json' },
                    signal: controller.signal,
                });

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }

                const data: MediaApiResponse = await res.json();

                setItems((data.data ?? []).map(normalizeMediaItem));
                setPagination(
                    data.pagination ?? {
                        ...DEFAULT_PAGINATION,
                        per_page: perPage,
                        current_page: targetPage,
                    },
                );
            } catch (err: any) {
                if (err?.name !== 'AbortError') {
                    setError(err?.message ?? 'Error al cargar medios');
                }
            } finally {
                setLoading(false);
            }
        },
        [perPage, collectionId, search, tags],
    );

    useEffect(() => {
        if (autoFetch) {
            fetchPage(page);
        }
        return () => {
            abortRef.current?.abort();
        };
    }, [page, autoFetch, fetchPage]);

    const refresh = useCallback(() => fetchPage(page), [fetchPage, page]);

    const prependItem = useCallback((item: MediaItem) => {
        setItems(prev => [item, ...prev]);
    }, []);

    const removeItem = useCallback((id: number) => {
        setItems(prev => prev.filter(m => m.id !== id));
    }, []);

    return {
        items,
        pagination,
        loading,
        error,
        page,
        setPage,
        refresh,
        prependItem,
        removeItem,
    };
}
