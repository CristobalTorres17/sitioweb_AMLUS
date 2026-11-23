import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function MenuCreate() {
    const [name, setName] = useState('');
    const [slug, setSlug] = useState('');

    const submit = async (e: any) => {
        e.preventDefault();
        const form = new FormData();
        form.append('name', name);
        form.append('slug', slug);
        await window.axios.post('/admin/menus', form);
        window.location.href = '/admin/menus';
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Crear menú</h2>}>
            <Head title="Crear menú" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <form onSubmit={submit}>
                            <div>
                                <label>Nombre</label>
                                <input
                                    value={name}
                                    onChange={e => setName(e.target.value)}
                                    required
                                    className="border rounded px-2 py-1 w-full"
                                />
                            </div>
                            <div className="mt-2">
                                <label>Slug</label>
                                <input
                                    value={slug}
                                    onChange={e => setSlug(e.target.value)}
                                    required
                                    className="border rounded px-2 py-1 w-full"
                                />
                            </div>

                            <div className="mt-4">
                                <button className="btn btn-primary">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
