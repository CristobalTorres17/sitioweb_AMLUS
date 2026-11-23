import React from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function PermissionCreate() {
    const form = useForm({ name: '', display_name: '' });

    const submit = (e: any) => {
        e.preventDefault();
        form.post('/admin/permissions');
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Crear permiso</h2>}>
            <Head title="Crear permiso" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <form onSubmit={submit}>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Clave
                                </label>
                                <input
                                    value={form.data.name}
                                    onChange={e => form.setData('name', e.target.value)}
                                    required
                                    className="border rounded px-2 py-1 w-full"
                                />
                                {form.errors.name && (
                                    <div className="text-red-600 text-sm mt-1">
                                        {form.errors.name}
                                    </div>
                                )}
                            </div>
                            <div className="mt-2">
                                <label className="block text-sm font-medium text-gray-700">
                                    Nombre visible
                                </label>
                                <input
                                    value={form.data.display_name}
                                    onChange={e => form.setData('display_name', e.target.value)}
                                    className="border rounded px-2 py-1 w-full"
                                />
                                {form.errors.display_name && (
                                    <div className="text-red-600 text-sm mt-1">
                                        {form.errors.display_name}
                                    </div>
                                )}
                            </div>

                            <div className="mt-4">
                                <button disabled={form.processing} className="btn btn-primary">
                                    {form.processing ? 'Creando...' : 'Crear'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
