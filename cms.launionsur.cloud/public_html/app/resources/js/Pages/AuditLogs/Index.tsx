import { useEffect, useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function AuditLogsIndex() {
    const [page, setPage] = useState(1);
    const [data, setData] = useState<any | null>(null);
    const [loading, setLoading] = useState(true);

    const fetch = async (p = 1) => {
        setLoading(true);
        const resp = await window.axios.get('/api/audit-logs', { params: { page: p } });
        setData(resp.data);
        setLoading(false);
    };

    useEffect(() => {
        fetch(1);
    }, []);

    const goTo = (p: number) => {
        setPage(p);
        fetch(p);
    };

    const logs = data?.data || [];

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Audit Logs</h2>}>
            <Head title="Audit Logs" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {loading ? (
                                <div>Loading...</div>
                            ) : (
                                <>
                                    <table className="w-full table-auto">
                                        <thead>
                                            <tr>
                                                <th>When</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Target</th>
                                                <th>IP</th>
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {logs.map((l: any) => (
                                                <tr key={l.id}>
                                                    <td>{l.created_at}</td>
                                                    <td>{l.user_id}</td>
                                                    <td>{l.action}</td>
                                                    <td>
                                                        {l.auditable_type}:{l.auditable_id}
                                                    </td>
                                                    <td>{l.ip_address}</td>
                                                    <td>
                                                        <pre className="text-xs max-h-36 overflow-auto">
                                                            {JSON.stringify(
                                                                l.new_values || l.old_values || {},
                                                                null,
                                                                2,
                                                            )}
                                                        </pre>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>

                                    <div className="mt-4 flex justify-between">
                                        <div>
                                            Page {data.current_page} of {data.last_page}
                                        </div>
                                        <div className="space-x-2">
                                            {data.prev_page_url && (
                                                <button
                                                    onClick={() => goTo(data.current_page - 1)}
                                                    className="btn"
                                                >
                                                    Prev
                                                </button>
                                            )}
                                            {data.next_page_url && (
                                                <button
                                                    onClick={() => goTo(data.current_page + 1)}
                                                    className="btn"
                                                >
                                                    Next
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
