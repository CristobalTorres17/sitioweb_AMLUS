import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';

export default function SettingEdit() {
    const { props } = usePage<any>();
    const setting = props.setting || {};
    const [value, setValue] = useState(setting.value ?? '');

    const submit = async (e: any) => {
        e.preventDefault();
        const form = new FormData();
        form.append('value', value);
        form.append('_method', 'PUT');
        await window.axios.post(`/admin/settings/${setting.key}`, form);
        window.location.href = '/admin/settings';
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Editar setting</h2>}>
            <Head title={`Editar ${setting.key}`} />
            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <form onSubmit={submit}>
                            <div>
                                <label>Clave</label>
                                <div className="mb-2 text-sm text-gray-600">{setting.key}</div>
                            </div>
                            <div className="mt-2">
                                <label>Valor</label>
                                <input
                                    value={value}
                                    onChange={e => setValue(e.target.value)}
                                    className="border rounded px-2 py-1 w-full"
                                />
                            </div>

                            <div className="mt-4">
                                <button className="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
