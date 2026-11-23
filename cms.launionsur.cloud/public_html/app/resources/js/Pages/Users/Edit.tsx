import { useEffect, useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';

export default function EditUser({ id }: { id: number }) {
    const { data, setData, put, processing } = useForm({ name: '', email: '', role_id: '' });
    const [roles, setRoles] = useState<any[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});

    useEffect(() => {
        window.axios.get(`/api/users/${id}`).then((resp: any) => {
            setData({ name: resp.data.name, email: resp.data.email, role_id: resp.data.role_id });
        });
        window.axios
            .get('/admin/roles')
            .then((r: any) => setRoles(r.data))
            .catch((err: any) => setRoles([]));
    }, [id]);

    const validate = () => {
        const e: Record<string, string> = {};
        if (!data.name.trim()) e.name = 'Name is required';
        if (!data.email.match(/^\S+@\S+\.\S+$/)) e.email = 'Valid email required';
        setErrors(e);
        return Object.keys(e).length === 0;
    };

    const submit = (e: any) => {
        e.preventDefault();
        if (!validate()) return;

        window.axios
            .put(`/api/users/${id}`, data)
            .then(() => {
                window.location.href = '/admin/users';
            })
            .catch((err: any) => {
                if (err.response?.data?.errors) {
                    const srv = err.response.data.errors;
                    const map: Record<string, string> = {};
                    Object.keys(srv).forEach(k => (map[k] = srv[k][0]));
                    setErrors(map);
                }
            });
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Edit User</h2>}>
            <Head title="Edit User" />

            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <form onSubmit={submit}>
                                <div className="mb-4">
                                    <label className="block">Name</label>
                                    <input
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.name && (
                                        <div className="text-red-600 mt-1">{errors.name}</div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label className="block">Email</label>
                                    <input
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.email && (
                                        <div className="text-red-600 mt-1">{errors.email}</div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label className="block">Role</label>
                                    <select
                                        value={data.role_id}
                                        onChange={e => setData('role_id', e.target.value)}
                                        className="mt-1 block w-full"
                                    >
                                        <option value="">-- Select role --</option>
                                        {roles.map(r => (
                                            <option key={r.id} value={r.id}>
                                                {r.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mt-4">
                                    <button
                                        type="submit"
                                        className="btn btn-primary"
                                        disabled={processing}
                                    >
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
