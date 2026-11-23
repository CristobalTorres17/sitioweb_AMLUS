import React, { useEffect, useState } from 'react';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import AuditTable from '../../Components/AuditTable';
import AuditFilter from '../../Components/AuditFilter';

type AuditLog = {
    id: number;
    model_type: string;
    model_id: number | null;
    action: string;
    is_sensitive: boolean;
    is_restoreable: boolean;
    user?: { id: number; name: string; email: string } | null;
    created_at: string;
};

export default function Index() {
    const [logs, setLogs] = useState<AuditLog[]>([]);
    const [page, setPage] = useState(1);
    const [perPage, setPerPage] = useState(15);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(false);

    // Filters
    const [modelType, setModelType] = useState('');
    const [action, setAction] = useState('');
    const [isSensitive, setIsSensitive] = useState<string>('');
    const [userId, setUserId] = useState<string>('');
    const [startDate, setStartDate] = useState<string>('');
    const [endDate, setEndDate] = useState<string>('');

    const fetchLogs = async () => {
        setLoading(true);
        try {
            const params: any = { page, per_page: perPage };
            if (modelType) params.model_type = modelType;
            if (action) params.action = action;
            if (isSensitive !== '') params.is_sensitive = isSensitive === '1' ? 1 : 0;
            if (userId) params.user_id = userId;
            if (startDate) params.start_date = startDate + ' 00:00:00';
            if (endDate) params.end_date = endDate + ' 23:59:59';

            const res = await axios.get('/api/audit-logs', { params });
            setLogs(res.data.data || []);
            setTotal(res.data.total || 0);
        } catch (e) {
            console.error('Failed to fetch audit logs', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLogs();
    }, [page, perPage]);

    const onFilter = () => {
        setPage(1);
        fetchLogs();
    };

    return (
        <div className="p-6">
            <h1 className="text-2xl font-semibold mb-4">Audit Logs</h1>

            <section className="mb-4 bg-white p-4 rounded shadow-sm">
                <div className="grid grid-cols-1 md:grid-cols-6 gap-3">
                    <div>
                        <label
                            htmlFor="modelType"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Model Type
                        </label>
                        <input
                            id="modelType"
                            value={modelType}
                            onChange={e => setModelType(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                            placeholder="App\\Models\\User"
                        />
                    </div>
                    <div>
                        <label htmlFor="userId" className="block text-sm font-medium text-gray-700">
                            User
                        </label>
                        <input
                            id="userId"
                            value={userId}
                            onChange={e => setUserId(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                            placeholder="User ID"
                        />
                    </div>
                    <div>
                        <label htmlFor="action" className="block text-sm font-medium text-gray-700">
                            Action
                        </label>
                        <select
                            id="action"
                            value={action}
                            onChange={e => setAction(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                        >
                            <option value="">Any</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                            <option value="restore">Restore</option>
                        </select>
                    </div>
                    <div>
                        <label
                            htmlFor="isSensitive"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Sensitive
                        </label>
                        <select
                            id="isSensitive"
                            value={isSensitive}
                            onChange={e => setIsSensitive(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                        >
                            <option value="">Any</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div>
                        <label
                            htmlFor="startDate"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Start date
                        </label>
                        <input
                            id="startDate"
                            type="date"
                            value={startDate}
                            onChange={e => setStartDate(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                        />
                    </div>
                    <div>
                        <label
                            htmlFor="endDate"
                            className="block text-sm font-medium text-gray-700"
                        >
                            End date
                        </label>
                        <input
                            id="endDate"
                            type="date"
                            value={endDate}
                            onChange={e => setEndDate(e.target.value)}
                            className="mt-1 block w-full rounded border-gray-300"
                        />
                    </div>

                    <div className="flex items-end col-span-6">
                        <button
                            onClick={onFilter}
                            className="px-4 py-2 bg-indigo-600 text-white rounded"
                        >
                            Filtrar
                        </button>
                        <button
                            onClick={() => {
                                setModelType('');
                                setAction('');
                                setIsSensitive('');
                                setUserId('');
                                setStartDate('');
                                setEndDate('');
                                setPage(1);
                                fetchLogs();
                            }}
                            className="ml-2 px-3 py-2 border rounded"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </section>

            <section className="bg-white p-4 rounded shadow-sm">
                <div className="flex items-center justify-between mb-2">
                    <div>
                        Mostrando {logs.length} de {total}
                    </div>
                    <div className="flex items-center gap-2">
                        <label className="text-sm">Per page</label>
                        <select
                            value={perPage}
                            onChange={e => setPerPage(Number(e.target.value))}
                            className="ml-1 rounded border-gray-300"
                        >
                            <option value={10}>10</option>
                            <option value={15}>15</option>
                            <option value={25}>25</option>
                        </select>
                    </div>
                </div>

                <AuditTable
                    logs={logs}
                    loading={loading}
                    onRowClick={(id: any) => Inertia.visit(`/admin/audit-logs/${id}`)}
                />

                <div className="mt-4 flex items-center justify-between">
                    <button
                        disabled={page <= 1}
                        onClick={() => setPage(p => Math.max(1, p - 1))}
                        className="px-3 py-2 rounded border"
                    >
                        Prev
                    </button>
                    <div>
                        Página {page} de {Math.max(1, Math.ceil(total / perPage))}
                    </div>
                    <button
                        disabled={page >= Math.max(1, Math.ceil(total / perPage))}
                        onClick={() =>
                            setPage(p => Math.min(Math.max(1, Math.ceil(total / perPage)), p + 1))
                        }
                        className="px-3 py-2 rounded border"
                    >
                        Next
                    </button>
                </div>
            </section>
        </div>
    );
}
