import React, { useState } from 'react';
import Modal from './Modal';
import axios from 'axios';

type Props = {
    auditId: number;
    isRestoreable: boolean;
    onRestored?: () => void;
};

export default function RestoreButton({ auditId, isRestoreable, onRestored }: Props) {
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [reason, setReason] = useState('');

    if (!isRestoreable) return null;

    const submit = async () => {
        setLoading(true);
        try {
            await axios.post(`/api/audit-logs/${auditId}/restore`, { reason });
            setOpen(false);
            onRestored?.();
        } catch (e) {
            // swallow — caller can show toast via onRestored or global handler
            console.error('Restore failed', e);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            <button
                onClick={() => setOpen(true)}
                className="px-3 py-2 bg-emerald-600 text-white rounded flex items-center gap-2"
                data-testid="restore-open"
                aria-haspopup="dialog"
                aria-controls={`restore-modal-${auditId}`}
                aria-label="Restaurar cambio"
            >
                <svg
                    className="w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth={2}
                    stroke="currentColor"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" d="M3 10h4l3 8 4-16 3 8h4" />
                </svg>
                Restaurar
            </button>

            <Modal show={open} onClose={() => setOpen(false)} maxWidth="md">
                <div className="p-4">
                    <h3 className="text-lg font-medium mb-2">Confirmar restauración</h3>
                    <div className="mb-3">
                        <label className="block text-sm font-medium text-gray-700">
                            Razón (opcional)
                        </label>
                        <textarea
                            value={reason}
                            onChange={e => setReason(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                            rows={4}
                        />
                    </div>

                    <div className="flex justify-end gap-2">
                        <button
                            onClick={() => setOpen(false)}
                            className="px-3 py-2 border rounded"
                            data-testid="restore-cancel"
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={submit}
                            disabled={loading}
                            className="px-4 py-2 bg-emerald-600 text-white rounded flex items-center gap-2"
                            data-testid="restore-confirm"
                            aria-disabled={loading}
                        >
                            {loading ? (
                                <svg
                                    className="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        className="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        strokeWidth="4"
                                    ></circle>
                                    <path
                                        className="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>
                            ) : null}
                            {loading ? 'Restaurando...' : 'Confirmar'}
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
}
