import React, { useState } from 'react';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/react/24/outline';

interface Tag {
    id: number;
    name: string;
    color: string;
}

type MediaContext = '' | 'articles' | 'events' | 'banners' | 'services' | 'cms_blocks' | 'unused';

interface MediaFiltersProps {
    search: string;
    onSearchChange: (value: string) => void;
    selectedTags: number[];
    onTagsChange: (tags: number[]) => void;
    availableTags: Tag[];
    context: MediaContext;
    onContextChange: (context: MediaContext) => void;
}

const CONTEXT_OPTIONS: Array<{ value: MediaContext; label: string }> = [
    { value: '', label: 'Todos' },
    { value: 'articles', label: 'Noticias' },
    { value: 'events', label: 'Eventos' },
    { value: 'banners', label: 'Banners' },
    { value: 'services', label: 'Servicios' },
    { value: 'cms_blocks', label: 'Bloques CMS' },
    { value: 'unused', label: 'Sin uso' },
];

export default function MediaFilters({
    search,
    onSearchChange,
    selectedTags,
    onTagsChange,
    availableTags,
    context,
    onContextChange,
}: MediaFiltersProps) {
    const [isTagsOpen, setIsTagsOpen] = useState(false);

    const toggleTag = (tagId: number) => {
        const newTags = selectedTags.includes(tagId)
            ? selectedTags.filter(id => id !== tagId)
            : [...selectedTags, tagId];
        onTagsChange(newTags);
    };

    return (
        <div className="px-4 sm:px-6 lg:px-8 space-y-4">
            {/* Search */}
            <div className="relative">
                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                </div>
                <input
                    type="text"
                    placeholder="Buscar por nombre o etiquetas..."
                    value={search}
                    onChange={e => onSearchChange(e.target.value)}
                    className="block w-full rounded-md border border-gray-300 py-2 pl-10 pr-3 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
            </div>

            <div className="flex flex-wrap gap-4 items-start">
                {/* Context filter */}
                <div className="flex items-center gap-2 flex-wrap">
                    <label className="text-sm font-medium text-gray-700 whitespace-nowrap">
                        Usado en:
                    </label>
                    <div className="flex flex-wrap gap-1">
                        {CONTEXT_OPTIONS.map(opt => (
                            <button
                                key={opt.value}
                                onClick={() => onContextChange(opt.value)}
                                className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-colors ${
                                    context === opt.value
                                        ? 'bg-primary text-gold'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                }`}
                            >
                                {opt.label}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Filtros de etiquetas */}
                <div className="relative flex flex-wrap gap-2 items-center">
                    <label className="text-sm font-medium text-gray-700">Etiquetas:</label>

                    {/* Selected tags display */}
                    <div className="flex flex-wrap gap-2">
                        {selectedTags.map(tagId => {
                            const tag = availableTags.find(t => t.id === tagId);
                            return tag ? (
                                <button
                                    key={tagId}
                                    onClick={() => toggleTag(tagId)}
                                    className="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium text-white"
                                    style={{ backgroundColor: tag.color || '#6366f1' }}
                                >
                                    {tag.name}
                                    <XMarkIcon className="h-4 w-4" />
                                </button>
                            ) : null;
                        })}
                    </div>

                    {/* Tag selector dropdown */}
                    <div className="relative">
                        <button
                            onClick={() => setIsTagsOpen(!isTagsOpen)}
                            className="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Agregar filtros
                            <span className="text-xs text-gray-500">▼</span>
                        </button>

                        {isTagsOpen && (
                            <div className="absolute top-full left-0 z-10 mt-1 w-48 rounded-md border border-gray-300 bg-white shadow-lg">
                                <div className="p-3 space-y-2 max-h-48 overflow-y-auto">
                                    {availableTags
                                        .filter(tag => !selectedTags.includes(tag.id))
                                        .map(tag => (
                                            <button
                                                key={tag.id}
                                                onClick={() => toggleTag(tag.id)}
                                                className="block w-full text-left rounded px-2 py-1 hover:bg-gray-100"
                                            >
                                                <span
                                                    className="inline-block w-3 h-3 rounded-full mr-2"
                                                    style={{
                                                        backgroundColor: tag.color || '#6366f1',
                                                    }}
                                                />
                                                {tag.name}
                                            </button>
                                        ))}
                                    {availableTags.filter(tag => !selectedTags.includes(tag.id))
                                        .length === 0 && (
                                        <p className="text-xs text-gray-500 text-center py-2">
                                            Todas las etiquetas seleccionadas
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
