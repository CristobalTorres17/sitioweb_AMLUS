import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, Link } from '@inertiajs/react';

export default function PermissionsIndex() {
    const { props } = usePage<any>();
    const permissions = props.permissions?.data || props.permissions || [];
    const [filter, setFilter] = useState('');
    const [list, setList] = useState<any[]>(permissions);

    React.useEffect(() => setList(permissions), [permissions]);

    const onDelete = async (id: number) => {
        if (!confirm('¿Eliminar permiso?')) return;
        try {
            await window.axios.delete(`/admin/permissions/${id}`);
            setList(prev => prev.filter((p: any) => p.id !== id));
        } catch (err) {
            alert('Error al eliminar permiso');
            console.error(err);
        }
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Permisos</h2>}>
            <Head title="Permisos" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <div>
                                    <h3 className="text-lg font-medium">Permisos</h3>
                                    <p className="text-sm text-gray-500">
                                        Lista de permisos disponibles en el sistema
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
                                        href="/admin/permissions/create"
                                        className="inline-flex items-center px-3 py-2 rounded-md bg-primary text-gold font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    >
                                        Crear permiso
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
                                            .filter((p: any) =>
                                                filter
                                                    ? (p.name || '')
                                                          .toLowerCase()
                                                          .includes(filter.toLowerCase()) ||
                                                      (p.display_name || '')
                                                          .toLowerCase()
                                                          .includes(filter.toLowerCase())
                                                    : true,
                                            )
                                            .map((p: any) => (
                                                <tr
                                                    key={p.id}
                                                    className="odd:bg-white even:bg-gray-50 hover:bg-primary/10"
                                                >
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {p.name}
                                                    </td>
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {p.display_name}
                                                    </td>
                                                    <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <div className="inline-flex items-center">
                                                            <Link
                                                                href={`/admin/permissions/${p.id}/edit`}
                                                                className="text-primary hover:underline mr-3 px-2 py-1 rounded"
                                                            >
                                                                Editar
                                                            </Link>
                                                            <button
                                                                onClick={() => onDelete(p.id)}
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
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
