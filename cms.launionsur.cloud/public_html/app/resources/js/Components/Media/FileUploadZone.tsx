import React, { useRef, useState } from 'react';

interface FileUploadZoneProps {
    onFilesSelected: (files: File[]) => void;
    isLoading?: boolean;
    progress?: number;
    accept?: string;
}

export default function FileUploadZone({
    onFilesSelected,
    isLoading = false,
    progress = 0,
    accept = 'image/*',
}: FileUploadZoneProps) {
    const [isDragActive, setIsDragActive] = useState(false);
    const inputRef = useRef<HTMLInputElement>(null);

    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setIsDragActive(true);
        } else if (e.type === 'dragleave') {
            setIsDragActive(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragActive(false);

        const files = Array.from(e.dataTransfer.files);
        onFilesSelected(files);
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = Array.from(e.target.files || []);
        onFilesSelected(files);
    };

    return (
        <div className="px-4 sm:px-6 lg:px-8">
            <div
                onDragEnter={handleDrag}
                onDragLeave={handleDrag}
                onDragOver={handleDrag}
                onDrop={handleDrop}
                className={`relative rounded-lg border-2 border-dashed px-6 py-10 text-center ${
                    isDragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'
                }`}
            >
                <input
                    ref={inputRef}
                    type="file"
                    multiple
                    accept={accept}
                    onChange={handleChange}
                    disabled={isLoading}
                    className="hidden"
                    id="file-upload"
                />

                {isLoading ? (
                    <div className="space-y-2">
                        <div className="text-sm font-semibold text-gray-700">
                            Uploading... {progress}%
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                            <div
                                className="bg-blue-600 h-2 rounded-full transition-all"
                                style={{ width: `${progress}%` }}
                            />
                        </div>
                    </div>
                ) : (
                    <>
                        <div className="text-gray-600">
                            <label htmlFor="file-upload" className="cursor-pointer">
                                <span className="font-semibold text-blue-600">
                                    Haz clic para subir una o varias imágenes
                                </span>{' '}
                                o arrastra y suelta tus archivos aquí
                            </label>
                        </div>
                        <div className="mt-2 text-xs text-gray-500">
                            Solo se permiten archivos de imagen (PNG, JPG, WebP) de hasta 10MB.
                        </div>
                        <p className="text-xs text-gray-500 mt-1">PNG, JPG, WebP hasta 10MB</p>
                    </>
                )}
            </div>
        </div>
    );
}
