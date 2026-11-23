import React, { useState, useEffect } from 'react';
import axios from 'axios';

interface Revision {
    id: number;
    article_id: number;
    revision_number: number;
    title: string;
    excerpt: string | null;
    image_path: string | null;
    user_id: number | null;
    user_name: string;
    created_at: string;
    created_at_human: string;
}

interface RevisionHistoryProps {
    articleId: number;
    onRevisionRestored?: (article: any) => void;
}

export default function RevisionHistory({ articleId, onRevisionRestored }: RevisionHistoryProps) {
    const [revisions, setRevisions] = useState<Revision[]>([]);
    const [loading, setLoading] = useState(true);
    const [restoring, setRestoring] = useState<number | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [expandedRevision, setExpandedRevision] = useState<number | null>(null);

    useEffect(() => {
        fetchRevisions();
    }, [articleId]);

    const fetchRevisions = async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await axios.get(`/articles/${articleId}/revisions`);
            setRevisions(response.data.revisions);
        } catch (err: any) {
            setError(err.response?.data?.message || 'Failed to load revisions');
            console.error('Error fetching revisions:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleRestore = async (revisionId: number) => {
        if (
            !window.confirm(
                'Are you sure you want to restore this revision? Changes will be saved.',
            )
        ) {
            return;
        }

        try {
            setRestoring(revisionId);
            setError(null);
            const response = await axios.post(
                `/articles/${articleId}/revisions/${revisionId}/restore`,
            );

            // Refresh revisions list after restoration
            await fetchRevisions();

            if (onRevisionRestored) {
                onRevisionRestored(response.data.article);
            }

            // Show success message
            alert(response.data.message);
        } catch (err: any) {
            const errorMsg = err.response?.data?.message || 'Failed to restore revision';
            setError(errorMsg);
            console.error('Error restoring revision:', err);
            alert(errorMsg);
        } finally {
            setRestoring(null);
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center p-8">
                <div className="text-gray-500">Loading revision history...</div>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {error && (
                <div className="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {error}
                </div>
            )}

            {revisions.length === 0 ? (
                <div className="text-center py-8 text-gray-500">
                    No revisions yet. Revisions will be created when you save changes.
                </div>
            ) : (
                <div className="space-y-2">
                    {revisions.map(revision => (
                        <div
                            key={revision.id}
                            className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div className="flex justify-between items-start">
                                <div
                                    className="flex-1 cursor-pointer"
                                    onClick={() =>
                                        setExpandedRevision(
                                            expandedRevision === revision.id ? null : revision.id,
                                        )
                                    }
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold">
                                            #{revision.revision_number}
                                        </div>
                                        <div className="flex-1">
                                            <h4 className="font-semibold text-gray-800 line-clamp-1">
                                                {revision.title || 'Untitled Revision'}
                                            </h4>
                                            <p className="text-sm text-gray-500">
                                                by {revision.user_name} ·{' '}
                                                {revision.created_at_human}
                                            </p>
                                            {revision.excerpt && (
                                                <p className="text-sm text-gray-600 mt-1 line-clamp-1">
                                                    {revision.excerpt}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                <button
                                    onClick={() => handleRestore(revision.id)}
                                    disabled={restoring === revision.id}
                                    className="ml-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg text-sm font-medium transition-colors"
                                >
                                    {restoring === revision.id ? 'Restoring...' : 'Restore'}
                                </button>
                            </div>

                            {expandedRevision === revision.id && (
                                <div className="mt-4 pt-4 border-t border-gray-200 space-y-2 text-sm">
                                    <div>
                                        <span className="font-semibold text-gray-700">Date:</span>
                                        <span className="ml-2 text-gray-600">
                                            {revision.created_at}
                                        </span>
                                    </div>
                                    <div>
                                        <span className="font-semibold text-gray-700">Author:</span>
                                        <span className="ml-2 text-gray-600">
                                            {revision.user_name}
                                        </span>
                                    </div>
                                    {revision.image_path && (
                                        <div className="pt-2">
                                            <span className="font-semibold text-gray-700 block mb-2">
                                                Featured Image:
                                            </span>
                                            <img
                                                src={`/storage/${revision.image_path}`}
                                                alt="Featured image"
                                                className="max-w-xs max-h-40 rounded-lg border border-gray-200"
                                            />
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
