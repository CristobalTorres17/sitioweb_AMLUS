import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    PencilIcon,
    TrashIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';

interface Tag {
    id: number;
    name: string;
    slug: string;
    description?: string;
    color: string;
    media_count: number;
}

interface PaginationData {
    data: Tag[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export default function Tags() {
    const { props } = usePage();
    const { tags: paginationData = { data: [] }, search: initialSearch = '' } = props as any;
    const tags: PaginationData = paginationData;

    const [search, setSearch] = useState(initialSearch);

    const handleSearch = (value: string) => {
        setSearch(value);
        router.get(route('media-tags.index'), { search: value });
    };

    const handleDelete = (tagId: number, tagName: string) => {
        if (!confirm(`Are you sure you want to delete "${tagName}"?`)) return;

        router.delete(route('media-tags.destroy', tagId), {
            onSuccess: () => {
                router.reload();
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Media Tags" />

            <div className="space-y-6 py-6">
                {/* Header */}
                <div className="px-4 sm:px-6 lg:px-8">
                    <div className="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h1 className="text-2xl font-bold leading-7 text-gray-900">
                                Media Tags
                            </h1>
                            <p className="mt-2 text-sm text-gray-600">
                                Manage tags for organizing your media library
                            </p>
                        </div>
                        <div className="mt-4 sm:mt-0">
                            <button
                                onClick={() => router.visit(route('media-tags.create'))}
                                className="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />
                                New Tag
                            </button>
                        </div>
                    </div>
                </div>

                {/* Search */}
                <div className="px-4 sm:px-6 lg:px-8">
                    <div className="relative">
                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="text"
                            placeholder="Search tags..."
                            value={search}
                            onChange={e => handleSearch(e.target.value)}
                            className="block w-full rounded-md border border-gray-300 py-2 pl-10 pr-3 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* Tags Table */}
                {tags.data.length > 0 ? (
                    <div className="px-4 sm:px-6 lg:px-8 overflow-hidden rounded-lg border border-gray-200">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                        Name
                                    </th>
                                    <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                        Description
                                    </th>
                                    <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                        Color
                                    </th>
                                    <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                        Media Count
                                    </th>
                                    <th className="relative px-6 py-3 text-sm font-semibold text-gray-900">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white">
                                {tags.data.map(tag => (
                                    <tr key={tag.id} className="hover:bg-gray-50">
                                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {tag.name}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-500">
                                            {tag.description || '-'}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm">
                                            <div className="flex items-center gap-2">
                                                <div
                                                    className="h-6 w-6 rounded border border-gray-300"
                                                    style={{ backgroundColor: tag.color }}
                                                    title={tag.color}
                                                />
                                                <span className="text-gray-500">{tag.color}</span>
                                            </div>
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <span className="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                                {tag.media_count} media
                                            </span>
                                        </td>
                                        <td className="relative whitespace-nowrap px-6 py-4 text-sm font-medium">
                                            <div className="flex gap-2">
                                                <button
                                                    onClick={() =>
                                                        router.visit(
                                                            route('media-tags.edit', tag.id),
                                                        )
                                                    }
                                                    className="text-blue-600 hover:text-blue-900"
                                                >
                                                    <PencilIcon className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(tag.id, tag.name)}
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    <TrashIcon className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="px-4 sm:px-6 lg:px-8 text-center py-12">
                        <p className="text-gray-500">No tags found</p>
                    </div>
                )}

                {/* Pagination info */}
                {tags.data.length > 0 && (
                    <div className="px-4 sm:px-6 lg:px-8 text-sm text-gray-500">
                        Showing {(tags.current_page - 1) * tags.per_page + 1} to{' '}
                        {Math.min(tags.current_page * tags.per_page, tags.total)} of {tags.total}{' '}
                        tags
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
