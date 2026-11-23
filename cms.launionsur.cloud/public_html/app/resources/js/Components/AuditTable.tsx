import React from 'react';

const AuditTable = ({ logs, loading, onRowClick }: any) => {
    if (loading) return <div>Cargando...</div>;

    if (!logs || logs.length === 0) return <div>No hay registros.</div>;

    return (
        <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
                <tr>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">Fecha</th>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                        Usuario
                    </th>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                        Acción
                    </th>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                        Modelo
                    </th>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">IP</th>
                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                        Sensible
                    </th>
                    <th className="px-4 py-2" />
                </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
                {logs.map((log: any) => (
                    <tr
                        key={log.id}
                        className="hover:bg-gray-50 cursor-pointer"
                        onClick={() => onRowClick(log.id)}
                    >
                        <td className="px-4 py-2 text-sm text-gray-700">
                            {new Date(log.created_at).toLocaleString()}
                        </td>
                        <td className="px-4 py-2 text-sm text-gray-700">
                            {log.user?.name ?? 'Sistema'}
                        </td>
                        <td className="px-4 py-2 text-sm text-gray-700">{log.action}</td>
                        <td className="px-4 py-2 text-sm text-gray-700">{log.model_type}</td>
                        <td className="px-4 py-2 text-sm text-gray-700">{log.ip_address ?? '-'}</td>
                        <td className="px-4 py-2 text-sm text-gray-700">
                            {log.is_sensitive ? 'Sí' : 'No'}
                        </td>
                        <td className="px-4 py-2 text-sm text-right text-indigo-600">Ver</td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
};

export default AuditTable;
