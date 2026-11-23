import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

interface TagData {
    id: number;
    name: string;
    slug: string;
    description?: string;
    color: string;
    media_count: number;
}

interface Props {
    tag: TagData;
    colorPalette: string[];
}

export default function TagEdit({ tag, colorPalette }: Props) {
    const { data, setData, patch, errors, processing } = useForm({
        name: tag.name,
        description: tag.description || '',
        color: tag.color,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('media-tags.update', tag.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit Tag: ${tag.name}`} />

            <div className="space-y-6 py-6">
                <div className="px-4 sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-bold leading-7 text-gray-900">
                        Edit Tag: {tag.name}
                    </h1>
                    <p className="mt-2 text-sm text-gray-600">
                        This tag is used in {tag.media_count} media item
                        {tag.media_count !== 1 ? 's' : ''}
                    </p>
                </div>

                <div className="px-4 sm:px-6 lg:px-8 max-w-2xl">
                    <form
                        onSubmit={handleSubmit}
                        className="space-y-6 bg-white p-6 rounded-lg shadow"
                    >
                        {/* Name field */}
                        <div>
                            <label className="block text-sm font-medium text-gray-900">
                                Tag Name *
                            </label>
                            <input
                                type="text"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                className={`mt-2 block w-full rounded-md border py-2 px-3 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                                    errors.name ? 'border-red-500' : 'border-gray-300'
                                }`}
                            />
                            {errors.name && (
                                <p className="mt-2 text-sm text-red-600">{errors.name}</p>
                            )}
                        </div>

                        {/* Description field */}
                        <div>
                            <label className="block text-sm font-medium text-gray-900">
                                Description
                            </label>
                            <textarea
                                value={data.description}
                                onChange={e => setData('description', e.target.value)}
                                className="mt-2 block w-full rounded-md border border-gray-300 py-2 px-3 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                rows={3}
                            />
                            {errors.description && (
                                <p className="mt-2 text-sm text-red-600">{errors.description}</p>
                            )}
                        </div>

                        {/* Color picker */}
                        <div>
                            <label className="block text-sm font-medium text-gray-900 mb-3">
                                Tag Color *
                            </label>
                            <div className="flex gap-3 flex-wrap">
                                {colorPalette.map(color => (
                                    <button
                                        key={color}
                                        type="button"
                                        onClick={() => setData('color', color)}
                                        className={`w-10 h-10 rounded-lg border-2 transition-all ${
                                            data.color === color
                                                ? 'border-gray-900 ring-2 ring-offset-2 ring-blue-500'
                                                : 'border-gray-300 hover:border-gray-400'
                                        }`}
                                        style={{ backgroundColor: color }}
                                        title={color}
                                    />
                                ))}
                            </div>
                            {errors.color && (
                                <p className="mt-2 text-sm text-red-600">{errors.color}</p>
                            )}

                            {/* Show selected color code */}
                            <p className="mt-3 text-sm text-gray-600">
                                Selected:{' '}
                                <code className="bg-gray-100 px-2 py-1 rounded">{data.color}</code>
                            </p>
                        </div>

                        {/* Submit buttons */}
                        <div className="flex gap-3 pt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? 'Saving...' : 'Save Changes'}
                            </button>
                            <a
                                href={route('media-tags.index')}
                                className="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-50"
                            >
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
