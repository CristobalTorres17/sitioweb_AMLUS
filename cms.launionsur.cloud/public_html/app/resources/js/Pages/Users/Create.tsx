import { FormEventHandler, useEffect, useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';

export default function CreateUser() {
    const { data, setData, processing } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
    });

    const [roles, setRoles] = useState<any[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});

    useEffect(() => {
        window.axios
            .get('/admin/roles')
            .then((r: any) => setRoles(r.data))
            .catch((err: any) => setRoles([]));
    }, []);

    const validate = () => {
        const e: Record<string, string> = {};
        if (!data.name.trim()) e.name = 'Name is required';
        if (!data.email.match(/^\S+@\S+\.\S+$/)) e.email = 'Valid email required';
        if (data.password.length < 8) e.password = 'Password must be at least 8 characters';
        if (data.password !== data.password_confirmation)
            e.password_confirmation = 'Passwords do not match';
        setErrors(e);
        return Object.keys(e).length === 0;
    };

    const submit: FormEventHandler = e => {
        e.preventDefault();
        if (!validate()) return;

        window.axios
            .post('/api/users', data)
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
        <Authenticated header={<h2 className="text-xl font-semibold">Create User</h2>}>
            <Head title="Create User" />

            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <form onSubmit={submit}>
                                <div className="mb-4">
                                    <label htmlFor="name" className="block">
                                        Name
                                    </label>
                                    <input
                                        id="name"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.name && (
                                        <div className="text-red-600 mt-1">{errors.name}</div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label htmlFor="email" className="block">
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.email && (
                                        <div className="text-red-600 mt-1">{errors.email}</div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label htmlFor="password" className="block">
                                        Password
                                    </label>
                                    <input
                                        id="password"
                                        type="password"
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="mt-1 block w-full"
                                    />
                                    {errors.password && (
                                        <div className="text-red-600 mt-1">{errors.password}</div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label htmlFor="password_confirmation" className="block">
                                        Confirm Password
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={e =>
                                            setData('password_confirmation', e.target.value)
                                        }
                                        className="mt-1 block w-full"
                                    />
                                    {errors.password_confirmation && (
                                        <div className="text-red-600 mt-1">
                                            {errors.password_confirmation}
                                        </div>
                                    )}
                                </div>

                                <div className="mb-4">
                                    <label htmlFor="role" className="block">
                                        Role
                                    </label>
                                    <select
                                        id="role"
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
                                        Create
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
