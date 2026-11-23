import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, Link } from '@inertiajs/react';

export default function RolesIndex() {
    const { props } = usePage<any>();
    const roles = props.roles?.data || props.roles || [];
    const [filter, setFilter] = useState('');
    const [list, setList] = useState<any[]>(roles);

    React.useEffect(() => setList(roles), [roles]);

    const onDelete = async (id: number) => {
        if (!confirm('¿Eliminar rol? Esta acción no se puede deshacer.')) return;
        try {
            await window.axios.delete(`/admin/roles/${id}`);
            setList(prev => prev.filter((r: any) => r.id !== id));
        } catch (err) {
            // show basic feedback
            alert('Error al eliminar. Revisa la consola.');
            console.error(err);
        }
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Roles</h2>}>
            <Head title="Roles" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <div>
                                    <h3 className="text-lg font-medium">Roles</h3>
                                    <p className="text-sm text-gray-500">
                                        Gestiona roles y permisos de usuario
                                    </p>
                                </div>
                                <div className="flex items-center gap-2">
                                    <input
                                        placeholder="Buscar..."
                                        value={filter}
                                        onChange={e => setFilter(e.target.value)}
                                        className="border border-border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    />
                                    <Link
                                        href="/admin/roles/create"
                                        className="inline-flex items-center px-3 py-2 rounded-md bg-primary text-gold font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    >
                                        Crear rol
                                    </Link>
                                </div>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                                Name
                                            </th>
                                            <th className="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                                Display
                                            </th>
                                            <th className="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {list
                                            .filter((r: any) =>
                                                filter
                                                    ? (r.name || '')
                                                          .toLowerCase()
                                                          .includes(filter.toLowerCase()) ||
                                                      (r.display_name || '')
                                                          .toLowerCase()
                                                          .includes(filter.toLowerCase())
                                                    : true,
                                            )
                                            .map((r: any) => (
                                                <tr
                                                    key={r.id}
                                                    className="odd:bg-white even:bg-gray-50 hover:bg-primary/10"
                                                >
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {r.name}
                                                    </td>
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {r.display_name}
                                                    </td>
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <div className="inline-flex items-center">
                                                            <Link
                                                                href={`/admin/roles/${r.id}/edit`}
                                                                className="text-primary hover:underline mr-3 px-2 py-1 rounded"
                                                            >
                                                                Editar
                                                            </Link>
                                                            <button
                                                                onClick={() => onDelete(r.id)}
                                                                className="text-red-600 hover:bg-red-50 px-2 py-1 rounded"
                                                            >
                                                                Eliminar
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Simple pagination controls if present */}
                            <div style={{ marginTop: 12 }}>
                                {/* Inertia provides links via props if server-side rendered; fallback to nothing */}
                                {props.roles?.links && (
                                    <div dangerouslySetInnerHTML={{ __html: props.roles.links }} />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
