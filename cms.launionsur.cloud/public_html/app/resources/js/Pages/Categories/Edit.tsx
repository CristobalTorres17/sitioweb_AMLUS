import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

interface Category {
    id: number;
    name: string;
    slug: string;
    type: string;
    description: string | null;
    icon: string | null;
    color: string | null;
    is_active: boolean;
    position: number;
}

const TYPE_LABELS: Record<string, string> = {
    article: 'Noticias (article)',
    event: 'Eventos (event)',
};

export default function Edit({ category, types }: { category: Category; types: string[] }) {
    const form = useForm({
        name: category.name,
        type: category.type,
        description: category.description ?? '',
        icon: category.icon ?? '',
        color: category.color ?? '',
        is_active: category.is_active,
        position: category.position,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(route('categories.update', category.id));
    }

    return (
        <Authenticated>
            <Head title={`Editar: ${category.name}`} />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <h2 className="mb-6 text-xl font-semibold">Editar categoría: {category.name}</h2>

                <form onSubmit={submit} className="space-y-5 max-w-2xl">
                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <label className="block text-sm font-medium">
                                Nombre <span className="text-red-500">*</span>
                            </label>
                            <input
                                className="mt-1 block w-full"
                                value={form.data.name}
                                onChange={e => form.setData('name', e.target.value)}
                            />
                            {form.errors.name && (
                                <p className="mt-1 text-xs text-red-600">{form.errors.name}</p>
                            )}
                            <p className="mt-1 text-xs text-gray-400">
                                Slug actual: <span className="font-mono">{category.slug}</span> — se
                                recalculará al guardar si el nombre cambia.
                            </p>
                        </div>

                        <div>
                            <label className="block text-sm font-medium">
                                Tipo <span className="text-red-500">*</span>
                            </label>
                            <select
                                className="mt-1 block w-full"
                                value={form.data.type}
                                onChange={e => form.setData('type', e.target.value)}
                            >
                                {types.map(t => (
                                    <option key={t} value={t}>
                                        {TYPE_LABELS[t] ?? t}
                                    </option>
                                ))}
                            </select>
                            {form.errors.type && (
                                <p className="mt-1 text-xs text-red-600">{form.errors.type}</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Descripción</label>
                        <textarea
                            rows={3}
                            className="mt-1 block w-full"
                            value={form.data.description}
                            onChange={e => form.setData('description', e.target.value)}
                        />
                    </div>

                    <div className="grid gap-4 md:grid-cols-3">
                        <div>
                            <label className="block text-sm font-medium">
                                Ícono{' '}
                                <span className="text-xs text-gray-400">(Material Icons slug)</span>
                            </label>
                            <input
                                className="mt-1 block w-full"
                                placeholder="newspaper"
                                value={form.data.icon}
                                onChange={e => form.setData('icon', e.target.value)}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium">Color</label>
                            <div className="mt-1 flex items-center gap-2">
                                <input
                                    type="color"
                                    className="h-9 w-14 cursor-pointer rounded border p-0.5"
                                    value={form.data.color || '#1d4ed8'}
                                    onChange={e => form.setData('color', e.target.value)}
                                />
                                <input
                                    className="block w-full font-mono text-sm"
                                    placeholder="#1d4ed8"
                                    value={form.data.color}
                                    onChange={e => form.setData('color', e.target.value)}
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium">Posición</label>
                            <input
                                type="number"
                                min={0}
                                className="mt-1 block w-full"
                                value={form.data.position}
                                onChange={e =>
                                    form.setData('position', parseInt(e.target.value) || 0)
                                }
                            />
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <input
                            id="is_active"
                            type="checkbox"
                            checked={form.data.is_active}
                            onChange={e => form.setData('is_active', e.target.checked)}
                        />
                        <label htmlFor="is_active" className="text-sm font-medium">
                            Activa
                        </label>
                    </div>

                    <div className="flex gap-3 pt-2">
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded bg-primary px-4 py-2 text-sm font-semibold text-gold hover:bg-primary/80 disabled:opacity-50"
                        >
                            Guardar cambios
                        </button>
                        <a
                            href={route('categories.index')}
                            className="rounded border px-4 py-2 text-sm hover:bg-gray-50"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </Authenticated>
    );
}
