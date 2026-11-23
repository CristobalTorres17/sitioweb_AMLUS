import { useEffect, useMemo, useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';

export default function UsersIndex() {
    const page = usePage();
    const props: any = page.props as any;
    const users = useMemo(
        () => (props.users && (props.users.data || props.users)) || [],
        [props.users],
    );
    const roles = props.roles || [];
    const [selectedRole, setSelectedRole] = useState<number | null>(null);

    const getBadgeClass = (roleName: string | undefined) => {
        switch ((roleName || '').toLowerCase()) {
            case 'super_admin':
            case 'super-admin':
            case 'super admin':
                return 'bg-red-100 text-red-800';
            case 'editor':
                return 'bg-blue-100 text-blue-800';
            case 'reviewer':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    useEffect(() => {
        if (selectedRole === null) {
            return;
        }

        router.get(
            route('admin.users.index'),
            { role_id: selectedRole },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['users'],
                replace: true,
            },
        );
    }, [selectedRole]);

    const handleDelete = async (id: number) => {
        if (!confirm('Are you sure?')) return;
        await window.axios.delete(`/api/users/${id}`);
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Users</h2>}>
            <Head title="Users" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <div className="flex items-center space-x-2">
                                    <label className="text-sm">Filter by role:</label>
                                    <select
                                        value={selectedRole || ''}
                                        onChange={e =>
                                            setSelectedRole(
                                                e.target.value ? Number(e.target.value) : null,
                                            )
                                        }
                                        className="border rounded px-2 py-1"
                                    >
                                        <option value="">All roles</option>
                                        {roles.map((r: any) => (
                                            <option key={r.id} value={r.id}>
                                                {r.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <a href="/admin/users/create" className="btn btn-primary">
                                    Create User
                                </a>
                            </div>

                            <table className="w-full table-auto">
                                <thead>
                                    <tr>
                                        <th className="text-left">Name</th>
                                        <th className="text-left">Email</th>
                                        <th className="text-left">Role</th>
                                        <th className="text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.map((u: any) => (
                                        <tr key={u.id}>
                                            <td>{u.name}</td>
                                            <td>{u.email}</td>
                                            <td>
                                                {u.role?.name ? (
                                                    <span
                                                        className={`inline-block rounded px-2 py-1 text-sm ${getBadgeClass(u.role?.name)}`}
                                                    >
                                                        {u.role?.name}
                                                    </span>
                                                ) : (
                                                    '—'
                                                )}
                                            </td>
                                            <td>
                                                <a
                                                    href={`/admin/users/${u.id}/edit`}
                                                    className="me-2 text-indigo-600"
                                                >
                                                    Edit
                                                </a>
                                                <button
                                                    onClick={() => handleDelete(u.id)}
                                                    className="text-red-600"
                                                >
                                                    Delete
                                                </button>
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
