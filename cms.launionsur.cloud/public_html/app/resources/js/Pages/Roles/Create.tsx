import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function RoleCreate({ permissions }: any) {
    const form = useForm({ name: '', display_name: '', permissions: [] as number[] });
    const [selectAll, setSelectAll] = useState(false);

    const toggle = (id: number) => {
        const present = form.data.permissions.includes(id);
        form.setData(
            'permissions',
            present
                ? form.data.permissions.filter((p: number) => p !== id)
                : [...form.data.permissions, id],
        );
    };

    const toggleAll = () => {
        if (selectAll) {
            form.setData('permissions', []);
        } else {
            form.setData('permissions', permissions?.map((p: any) => p.id) || []);
        }
        setSelectAll(!selectAll);
    };

    const submit = (e: any) => {
        e.preventDefault();
        form.post('/admin/roles', {
            onFinish: () => {},
        });
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Crear rol</h2>}>
            <Head title="Crear rol" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <form onSubmit={submit}>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Clave (slug)
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
                                <div className="flex items-center justify-between mb-2">
                                    <label className="block mb-0 font-medium">Permisos</label>
                                    <label className="inline-flex items-center text-sm">
                                        <input
                                            type="checkbox"
                                            checked={selectAll}
                                            onChange={toggleAll}
                                        />
                                        <span className="ml-2">Seleccionar todo</span>
                                    </label>
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    {permissions?.map((p: any) => (
                                        <label key={p.id} className="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={form.data.permissions.includes(p.id)}
                                                onChange={() => toggle(p.id)}
                                            />
                                            <span className="ml-2">{p.name}</span>
                                        </label>
                                    ))}
                                </div>
                                {form.errors.permissions && (
                                    <div className="text-red-600 text-sm mt-1">
                                        {form.errors.permissions}
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
