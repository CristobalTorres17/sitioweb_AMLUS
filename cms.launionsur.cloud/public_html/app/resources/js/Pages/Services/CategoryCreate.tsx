import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

type CategoryKind = 'service' | 'tramite';

export default function CategoryCreate() {
    const form = useForm({
        name: '',
        type: 'service' as CategoryKind,
        description: '',
        is_active: true,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.post(route('service-categories.store'));
    }

    return (
        <Authenticated>
            <Head title="Crear categoría" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <h2 className="mb-4 text-xl font-semibold">Crear categoría</h2>
                <form onSubmit={submit} className="space-y-5">
                    <div>
                        <label className="block text-sm font-medium">Nombre</label>
                        <input
                            className="mt-1 block w-full"
                            value={form.data.name}
                            onChange={e => form.setData('name', e.target.value)}
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Tipo</label>
                        <select
                            className="mt-1 block w-full"
                            value={form.data.type}
                            onChange={e => form.setData('type', e.target.value as CategoryKind)}
                        >
                            <option value="service">Servicio</option>
                            <option value="tramite">Trámite</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Descripción</label>
                        <textarea
                            className="mt-1 block w-full"
                            rows={4}
                            value={form.data.description}
                            onChange={e => form.setData('description', e.target.value)}
                        />
                    </div>

                    <label className="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={form.data.is_active}
                            onChange={e => form.setData('is_active', e.target.checked)}
                        />
                        Activa
                    </label>

                    <button type="submit" className="rounded bg-primary px-4 py-2 text-white">
                        Guardar
                    </button>
                </form>
            </div>
        </Authenticated>
    );
}
