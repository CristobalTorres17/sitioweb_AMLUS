import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage, router } from '@inertiajs/react';

export default function Index() {
    const props: any = usePage().props;
    const services = props.services || { data: [], links: [], meta: {} };

    return (
        <Authenticated>
            <Head title="Services" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-medium">Servicios / Trámites</h3>
                    <div className="flex gap-2">
                        <Link
                            href={route('service-categories.index')}
                            className="rounded border border-primary/20 bg-white px-3 py-2 text-sm font-semibold text-primary hover:bg-primary/5"
                        >
                            Categorías
                        </Link>
                        <Link
                            href={route('services.create')}
                            className="rounded bg-primary px-3 py-2 text-sm font-semibold text-gold hover:bg-primary/60"
                        >
                            Crear Servicio
                        </Link>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="text-gray-600">
                                <th className="py-2">Titulo</th>
                                <th className="py-2">Tipo</th>
                                <th className="py-2">Categoría</th>
                                <th className="py-2">Costo</th>
                                <th className="py-2">Estimado</th>
                                <th className="py-2">Unidad</th>
                                <th className="py-2">Pasos</th>
                                <th className="py-2">Requisitos</th>
                                <th className="py-2">Estado</th>
                                <th className="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {services.data.length === 0 && (
                                <tr>
                                    <td colSpan={9} className="py-6 text-center text-gray-500">
                                        No se encontraron servicios.
                                    </td>
                                </tr>
                            )}
                            {services.data.map((s: any) => (
                                <tr key={s.id} className="border-t">
                                    <td className="py-3">{s.title}</td>
                                    <td className="py-3">{s.kind_label ?? s.kind ?? '-'}</td>
                                    <td className="py-3">{s.category?.name ?? '-'}</td>
                                    <td className="py-3">{s.cost ?? '-'}</td>
                                    <td className="py-3">{s.estimated_time ?? '-'}</td>
                                    <td className="py-3">{s.unit ?? '-'}</td>
                                    <td className="py-3">{s.steps_count ?? 0}</td>
                                    <td className="py-3">{s.requirements_count ?? 0}</td>
                                    <td className="py-3">{s.is_active ? 'Activo' : 'Inactivo'}</td>
                                    <td className="py-3">
                                        <div className="flex gap-2">
                                            <Link
                                                href={route('services.edit', s.id)}
                                                className="text-sm text-primary"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                className="text-sm text-red-600"
                                                onClick={() =>
                                                    router.delete(route('services.destroy', s.id))
                                                }
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
        </Authenticated>
    );
}
