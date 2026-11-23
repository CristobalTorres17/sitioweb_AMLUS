import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Modal from '@/Components/Modal';
import RestoreButton from '@/Components/RestoreButton';
import { usePage } from '@inertiajs/react';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

type DiffRow = { field: string; old: any; new: any };

export default function Detail() {
    const props: any = usePage().props;
    const user = props?.auth?.user;
    const isSuperAdmin =
        Boolean(user?.is_super_admin) ||
        Boolean(
            user?.role &&
            (user.role.name === 'Super Admin' ||
                user.role.name === 'super_admin' ||
                user.role.slug === 'super_admin'),
        );

    const [loading, setLoading] = useState(true);
    const [audit, setAudit] = useState<any | null>(null);
    const [diffRows, setDiffRows] = useState<DiffRow[]>([]);

    const id = Number(window.location.pathname.split('/').pop() || 0);

    const fetchAudit = async () => {
        setLoading(true);
        try {
            const res = await axios.get(`/api/audit-logs/${id}`);
            const data = res.data;
            setAudit(data);

            const oldValues = data.old_values || {};
            const newValues = data.new_values || {};
            const keys = Array.from(
                new Set([...Object.keys(oldValues), ...Object.keys(newValues)]),
            );
            const rows = keys
                .filter(k => {
                    const o = oldValues[k] ?? null;
                    const n = newValues[k] ?? null;
                    return o !== n;
                })
                .map(k => ({ field: k, old: oldValues[k] ?? null, new: newValues[k] ?? null }));

            setDiffRows(rows);
        } catch (e) {
            console.error('Failed to load audit log', e);
            alert('Error cargando el registro');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchAudit();
    }, [id]);

    const [toast, setToast] = useState<{ type: 'success' | 'error'; message: string } | null>(null);

    const onRestored = async () => {
        setToast({ type: 'success', message: 'Restauración exitosa' });
        // refetch to reflect restored state
        await fetchAudit();
        window.setTimeout(() => setToast(null), 3500);
    };

    if (loading) return <div className="p-6">Cargando...</div>;
    if (!audit) return <div className="p-6">Registro no encontrado.</div>;

    return (
        <div className="p-6">
            <div className="flex items-start justify-between">
                <div>
                    <h1 className="text-2xl font-semibold">
                        {`Audit Log #${audit.id}`}{' '}
                        <span className="text-sm text-gray-400">{audit.action_label || ''}</span>
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        {audit.model_type}#{audit.model_id}
                    </p>
                </div>
                <div className="flex gap-2">
                    {audit.is_restoreable && isSuperAdmin && (
                        <RestoreButton
                            auditId={audit.id}
                            isRestoreable={true}
                            onRestored={onRestored}
                        />
                    )}
                    <button
                        onClick={() => Inertia.visit('/admin/audit-logs')}
                        className="px-3 py-2 border rounded flex items-center gap-2"
                        aria-label="Volver a lista de auditoría"
                    >
                        <ArrowLeftIcon className="w-4 h-4 text-gray-600" />
                        Volver
                    </button>
                </div>
            </div>

            <section className="mt-4 bg-white p-4 rounded shadow-sm">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="flex items-start gap-3">
                        <div className="text-gray-500 mt-1">Usuario</div>
                        <div>
                            <div className="font-medium">{audit.user?.name ?? 'Sistema'}</div>
                            <div className="text-sm text-gray-500">{audit.user?.email}</div>
                        </div>
                    </div>
                    <div className="flex items-start gap-3">
                        <div className="text-gray-500 mt-1">IP</div>
                        <div>{audit.ip_address ?? '-'}</div>
                    </div>
                    <div className="flex items-start gap-3">
                        <div className="text-gray-500 mt-1">Fecha</div>
                        <div>{new Date(audit.created_at).toLocaleString()}</div>
                    </div>
                </div>

                {audit.reason && (
                    <div className="mt-3">
                        <strong>Razón:</strong>
                        <div>{audit.reason}</div>
                    </div>
                )}
            </section>

            <section className="mt-4 bg-white p-4 rounded shadow-sm">
                <h2 className="text-lg font-medium mb-2">Cambios</h2>
                {diffRows.length === 0 ? (
                    <div>No hay cambios registrados.</div>
                ) : (
                    <div className="overflow-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th className="px-4 py-2 text-left text-sm text-gray-500">
                                        Campo
                                    </th>
                                    <th className="px-4 py-2 text-left text-sm text-gray-500">
                                        Anterior
                                    </th>
                                    <th className="px-4 py-2 text-left text-sm text-gray-500">
                                        Nuevo
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {diffRows.map(r => (
                                    <tr key={r.field}>
                                        <td className="px-4 py-2 align-top text-sm text-gray-700">
                                            {r.field}
                                        </td>
                                        <td className="px-4 py-2 align-top text-sm text-red-600">
                                            {typeof r.old === 'object' ? (
                                                <pre className="text-xs font-mono max-w-lg overflow-auto">
                                                    {JSON.stringify(r.old, null, 2)}
                                                </pre>
                                            ) : (
                                                <span>{String(r.old ?? '—')}</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-2 align-top text-sm text-green-600">
                                            {typeof r.new === 'object' ? (
                                                <pre className="text-xs font-mono max-w-lg overflow-auto">
                                                    {JSON.stringify(r.new, null, 2)}
                                                </pre>
                                            ) : (
                                                <span>{String(r.new ?? '—')}</span>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </section>
            {toast && (
                <div className="fixed right-4 bottom-4 z-50" aria-live="polite">
                    <div
                        className={`max-w-sm w-full rounded shadow-lg px-4 py-3 flex items-start gap-3 ${
                            toast.type === 'success'
                                ? 'bg-green-50 border border-green-200 text-green-800'
                                : 'bg-red-50 border border-red-200 text-red-800'
                        }`}
                        role="status"
                    >
                        <div className="flex-1">
                            <div className="text-sm font-medium">
                                {toast.type === 'success' ? 'Success' : 'Error'}
                            </div>
                            <div className="text-sm mt-1">{toast.message}</div>
                        </div>

                        <button
                            aria-label="Close"
                            onClick={() => setToast(null)}
                            className="text-sm opacity-80"
                        >
                            ×
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
