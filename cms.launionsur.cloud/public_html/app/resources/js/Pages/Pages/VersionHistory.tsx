import { useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

type Version = {
    id: number;
    version_number: number;
    title: string;
    content: string;
    created_at: string;
    creator?: { id: number; name: string } | null;
    change_reason?: string | null;
    restored_from?: number | null;
};

type VersionHistoryProps = {
    pageId: number;
    onClose: () => void;
    canRestore: boolean;
};

export default function VersionHistory({ pageId, onClose, canRestore }: VersionHistoryProps) {
    const [versions, setVersions] = useState<Version[]>([]);
    const [loading, setLoading] = useState(true);
    const [selectedVersion, setSelectedVersion] = useState<Version | null>(null);
    const [showRestoreModal, setShowRestoreModal] = useState(false);
    const form = useForm();

    // Load versions on mount
    useEffect(() => {
        const fetchVersions = async () => {
            try {
                const response = await fetch(route('pages.versions.index', pageId));
                const data = await response.json();
                setVersions(data.versions || []);
            } catch (error) {
                console.error('Error loading versions:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchVersions();
    }, [pageId]);

    const handleRestoreClick = (version: Version) => {
        setSelectedVersion(version);
        setShowRestoreModal(true);
    };

    const confirmRestore = () => {
        if (!selectedVersion) return;

        form.post(route('pages.versions.restore', [pageId, selectedVersion.id]), {
            onSuccess: () => {
                setShowRestoreModal(false);
                setSelectedVersion(null);
                onClose();
            },
        });
    };

    return (
        <>
            {/* Version History Modal */}
            <Modal show={true} onClose={onClose}>
                <div className="p-6 max-h-[80vh] overflow-y-auto">
                    <h3 className="text-lg font-medium text-primary mb-2">
                        Historial de Versiones
                    </h3>
                    <p className="text-sm text-text-secondary mb-4">
                        {versions.length === 0
                            ? 'No hay versiones registradas'
                            : `${versions.length} versión${versions.length !== 1 ? 'es' : ''} guardada${versions.length !== 1 ? 's' : ''}`}
                    </p>

                    {loading ? (
                        <div className="text-center py-8">
                            <p className="text-text-secondary">Cargando versiones...</p>
                        </div>
                    ) : versions.length > 0 ? (
                        <div className="space-y-3 mb-6">
                            {versions.map((version, index) => (
                                <div
                                    key={version.id}
                                    className="border border-border rounded-lg p-4 hover:bg-gray-50 transition"
                                >
                                    <div className="flex items-start justify-between mb-2">
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2">
                                                <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                                    v{version.version_number}
                                                </span>
                                                {version.restored_from && (
                                                    <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                        Restaurada
                                                    </span>
                                                )}
                                                {index === 0 && (
                                                    <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                                        Actual
                                                    </span>
                                                )}
                                            </div>
                                            <h4 className="font-medium text-primary mt-2">
                                                {version.title}
                                            </h4>
                                            <p className="text-sm text-text-secondary mt-1">
                                                {version.creator?.name &&
                                                    `Por ${version.creator.name} • `}
                                                {new Date(version.created_at).toLocaleDateString(
                                                    'es-ES',
                                                    {
                                                        year: 'numeric',
                                                        month: 'short',
                                                        day: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                    },
                                                )}
                                            </p>
                                            {version.change_reason && (
                                                <p className="text-xs text-gray-600 mt-2 italic">
                                                    "{version.change_reason}"
                                                </p>
                                            )}
                                        </div>
                                        {canRestore && index !== 0 && (
                                            <button
                                                onClick={() => handleRestoreClick(version)}
                                                className="ml-4 px-3 py-2 border border-primary text-primary text-xs font-medium rounded hover:bg-blue-50 transition whitespace-nowrap"
                                            >
                                                Restaurar
                                            </button>
                                        )}
                                    </div>

                                    {/* Content Preview */}
                                    <div className="mt-3 p-3 bg-gray-50 rounded text-xs max-h-20 overflow-hidden">
                                        <p className="text-text-secondary line-clamp-3">
                                            {version.content
                                                .replace(/<[^>]*>/g, '')
                                                .substring(0, 150)}
                                            ...
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : null}

                    {/* Modal Actions */}
                    <div className="flex justify-end">
                        <SecondaryButton onClick={onClose}>Cerrar</SecondaryButton>
                    </div>
                </div>
            </Modal>

            {/* Restore Confirmation Modal */}
            <Modal show={showRestoreModal} onClose={() => setShowRestoreModal(false)}>
                <div className="p-6">
                    <h3 className="text-lg font-medium text-primary mb-4">
                        Confirmar Restauración
                    </h3>
                    {selectedVersion && (
                        <div className="mb-4">
                            <p className="text-gray-600 mb-2">¿Restaurar esta versión?</p>
                            <div className="border border-border rounded p-3 bg-gray-50">
                                <p className="font-medium text-primary">{selectedVersion.title}</p>
                                <p className="text-xs text-text-secondary mt-1">
                                    Versión {selectedVersion.version_number} • Guardada el{' '}
                                    {new Date(selectedVersion.created_at).toLocaleDateString(
                                        'es-ES',
                                    )}
                                </p>
                            </div>
                            <p className="text-xs text-gray-600 mt-3">
                                Se creará una nueva versión con el contenido de esta versión
                                anterior.
                            </p>
                        </div>
                    )}
                    <div className="flex gap-3 justify-end">
                        <SecondaryButton
                            onClick={() => {
                                setShowRestoreModal(false);
                                setSelectedVersion(null);
                            }}
                        >
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton onClick={confirmRestore} disabled={form.processing}>
                            {form.processing ? 'Restaurando...' : 'Restaurar'}
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </>
    );
}
