import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Categories() {
    const props: any = usePage().props;
    const categories = props.categories || { data: [], links: [], meta: {} };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Categorías de servicios</h2>}>
            <Head title="Categorías de servicios" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-medium">Categorías de servicios</h3>
                    <Link
                        href={route('service-categories.create')}
                        className="rounded bg-primary/10 text-primary px-3 py-1 text-sm"
                    >
                        Nueva categoría
                    </Link>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="text-gray-600">
                                <th className="py-2">Name</th>
                                <th className="py-2">Type</th>
                                <th className="py-2">Position</th>
                                <th className="py-2">Active</th>
                                <th className="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {categories.data.map((c: any) => (
                                <tr key={c.id} className="border-t">
                                    <td className="py-3">{c.name}</td>
                                    <td className="py-3">
                                        {c.type === 'tramite' ? 'Trámite' : 'Servicio'}
                                    </td>
                                    <td className="py-3">{c.position}</td>
                                    <td className="py-3">{c.is_active ? 'Yes' : 'No'}</td>
                                    <td className="py-3">
                                        <div className="flex gap-2">
                                            <Link
                                                href={route('service-categories.edit', c.id)}
                                                className="text-sm text-primary"
                                            >
                                                Edit
                                            </Link>
                                            <form
                                                method="post"
                                                action={route('service-categories.destroy', c.id)}
                                            >
                                                <input
                                                    type="hidden"
                                                    name="_method"
                                                    value="delete"
                                                />
                                                <button
                                                    type="submit"
                                                    className="text-sm text-red-600"
                                                >
                                                    Delete
                                                </button>
                                            </form>
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
