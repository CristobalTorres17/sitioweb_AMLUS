import React from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';

export default function MenusIndex() {
    const { props } = usePage<any>();
    const menus = props.menus || [];

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Menus</h2>}>
            <Head title="Menus" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Menús</h3>
                                <a href="/admin/menus/create" className="btn btn-primary">
                                    Crear menú
                                </a>
                            </div>

                            <table className="w-full table-auto">
                                <thead>
                                    <tr>
                                        <th className="text-left">Name</th>
                                        <th className="text-left">Slug</th>
                                        <th className="text-left">Items</th>
                                        <th className="text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {menus.map((m: any) => (
                                        <tr key={m.id}>
                                            <td>{m.name}</td>
                                            <td>{m.slug}</td>
                                            <td>{m.items?.length || 0}</td>
                                            <td>
                                                <a
                                                    href={`/admin/menus/${m.id}/edit`}
                                                    className="me-2 text-indigo-600"
                                                >
                                                    Editar
                                                </a>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
