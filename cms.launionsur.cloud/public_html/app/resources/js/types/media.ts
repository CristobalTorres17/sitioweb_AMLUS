/**
 * Shared media types used across the CMS.
 * All media-related components and hooks must import from here.
 */

export interface MediaUrls {
    original: string;
    thumbnail: string;
    medium: string;
    full: string;
}

/** Full media item as returned by MediaController (web) and MediaApiController (API). */
export interface MediaItem {
    id: number;
    filename: string;
    original_filename: string;
    mime_type: string;
    size: number;
    width?: number;
    height?: number;
    disk?: string;
    is_active: boolean;
    /** Variant URLs keyed by size name. */
    urls: MediaUrls;
    metadata: Record<string, any>;
    tags: Array<{ id: number; name: string }>;
    user?: { id: number; name: string };
    created_at: string;
    /** Convenience path (relative, without leading slash) */
    path?: string;
    /** Usage counts per entity type, populated by admin gallery view. */
    usage_counts?: {
        articles: number;
        banners: number;
        services: number;
        cms_blocks: number;
        events: number;
        total: number;
    };
}

/** Slim version used inside pickers when only identity + display URL are needed. */
export interface MediaRef {
    id: number;
    filename: string;
    path?: string;
    url: string;
}

export interface MediaPagination {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
}

export interface MediaApiResponse {
    success: boolean;
    data: MediaItem[];
    pagination: MediaPagination;
}

/** Tag entity */
export interface MediaTag {
    id: number;
    name: string;
    color?: string;
    slug?: string;
    media_count?: number;
}

/** Entry in the article/event/banner/service media pivot. */
export interface SelectedMedia {
    media_id: number;
    is_primary?: boolean;
    position?: number;
    caption?: string | null;
    alt_text?: string | null;
    // convenience fields populated from gallery
    filename?: string;
    path?: string;
    url?: string;
}

/** Detailed usage response from GET /api/media/{id}/usage */
export interface MediaUsageDetail {
    articles: Array<{ id: number; title: string }>;
    banners: Array<{ id: number; title: string }>;
    cms_blocks: Array<{ id: number; name: string }>;
    services: Array<{ id: number; title: string }>;
    events: Array<{ id: number; title: string }>;
    featured_events: Array<{ id: number; title: string }>;
    total_count: number;
}
