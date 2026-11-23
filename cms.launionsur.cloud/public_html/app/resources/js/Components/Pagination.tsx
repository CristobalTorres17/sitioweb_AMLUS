import React from 'react';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/outline';

interface PaginationComponentProps {
    currentPage: number;
    lastPage: number;
    onPageChange: (page: number) => void;
}

export default function PaginationComponent({
    currentPage,
    lastPage,
    onPageChange,
}: PaginationComponentProps) {
    const pages = [];
    const maxVisible = 5;
    const rangeStart = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    const rangeEnd = Math.min(lastPage, rangeStart + maxVisible - 1);

    for (let i = rangeStart; i <= rangeEnd; i++) {
        pages.push(i);
    }

    return (
        <div className="px-4 sm:px-6 lg:px-8 flex items-center justify-center gap-2">
            <button
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className="rounded-md p-2 hover:bg-gray-200 disabled:opacity-50"
            >
                <ChevronLeftIcon className="h-5 w-5" />
            </button>

            {rangeStart > 1 && (
                <>
                    <button
                        onClick={() => onPageChange(1)}
                        className="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        1
                    </button>
                    {rangeStart > 2 && <span className="text-gray-500">...</span>}
                </>
            )}

            {pages.map(page => (
                <button
                    key={page}
                    onClick={() => onPageChange(page)}
                    className={`rounded-md px-3 py-2 text-sm ${
                        page === currentPage
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 hover:bg-gray-100'
                    }`}
                >
                    {page}
                </button>
            ))}

            {rangeEnd < lastPage && (
                <>
                    {rangeEnd < lastPage - 1 && <span className="text-gray-500">...</span>}
                    <button
                        onClick={() => onPageChange(lastPage)}
                        className="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        {lastPage}
                    </button>
                </>
            )}

            <button
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === lastPage}
                className="rounded-md p-2 hover:bg-gray-200 disabled:opacity-50"
            >
                <ChevronRightIcon className="h-5 w-5" />
            </button>
        </div>
    );
}
