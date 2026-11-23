import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

interface District {
    id: number;
    name: string;
    slug: string;
    svg_path_id: string | null;
    color: string | null;
    position: number;
    is_active: boolean;
    deleted_at: string | null;
}

export default function Index({ districts }: { districts: District[] }) {
    function handleDelete(id: number) {
        if (!confirm('¿Seguro que deseas eliminar este distrito?')) return;
        router.delete(route('districts.destroy', id));
    }

    return (
        <Authenticated>
            <Head title="Distritos" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-medium">Distritos Municipales</h3>
                    <Link
                        href={route('districts.create')}
                        className="rounded bg-primary px-3 py-2 text-sm font-semibold text-gold hover:bg-primary/60"
                    >
                        Crear Distrito
                    </Link>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="text-gray-600">
                                <th className="py-2 pr-4">Pos.</th>
                                <th className="py-2 pr-4">Nombre</th>
                                <th className="py-2 pr-4">Slug</th>
                                <th className="py-2 pr-4">SVG ID</th>
                                <th className="py-2 pr-4">Color</th>
                                <th className="py-2 pr-4">Estado</th>
                                <th className="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {districts.length === 0 && (
                                <tr>
                                    <td colSpan={7} className="py-6 text-center text-gray-500">
                                        No hay distritos registrados.
                                    </td>
                                </tr>
                            )}
                            {districts.map(d => (
                                <tr
                                    key={d.id}
                                    className={`border-t ${d.deleted_at ? 'opacity-40' : ''}`}
                                >
                                    <td className="py-3 pr-4 text-gray-500">{d.position}</td>
                                    <td className="py-3 pr-4 font-medium">{d.name}</td>
                                    <td className="py-3 pr-4 font-mono text-xs text-gray-500">
                                        {d.slug}
                                    </td>
                                    <td className="py-3 pr-4 font-mono text-xs text-gray-500">
                                        {d.svg_path_id ?? '-'}
                                    </td>
                                    <td className="py-3 pr-4">
                                        {d.color ? (
                                            <span className="inline-flex items-center gap-1">
                                                <span
                                                    className="inline-block h-4 w-4 rounded border"
                                                    style={{ backgroundColor: d.color }}
                                                />
                                                <span className="text-xs text-gray-500">
                                                    {d.color}
                                                </span>
                                            </span>
                                        ) : (
                                            '-'
                                        )}
                                    </td>
                                    <td className="py-3 pr-4">
                                        <span
                                            className={`inline-block rounded px-2 py-0.5 text-xs font-medium ${
                                                d.is_active
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-gray-100 text-gray-500'
                                            }`}
                                        >
                                            {d.is_active ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td className="py-3">
                                        <div className="flex items-center gap-2">
                                            <button
                                                type="button"
                                                title="Subir"
                                                className="text-xs text-gray-500 hover:text-primary"
                                                onClick={() =>
                                                    router.post(route('districts.move-up', d.id))
                                                }
                                            >
                                                ↑
                                            </button>
                                            <button
                                                type="button"
                                                title="Bajar"
                                                className="text-xs text-gray-500 hover:text-primary"
                                                onClick={() =>
                                                    router.post(route('districts.move-down', d.id))
                                                }
                                            >
                                                ↓
                                            </button>
                                            <Link
                                                href={route('districts.edit', d.id)}
                                                className="text-sm text-primary hover:underline"
                                            >
                                                Editar
                                            </Link>
                                            {!d.deleted_at && (
                                                <button
                                                    type="button"
                                                    className="text-sm text-red-600 hover:underline"
                                                    onClick={() => handleDelete(d.id)}
                                                >
                                                    Eliminar
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </Authenticated>
    );
}
