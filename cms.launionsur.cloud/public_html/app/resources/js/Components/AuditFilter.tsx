import React, { useEffect, useState } from 'react';
import axios from 'axios';

type Props = {
    modelType: string;
    setModelType: (v: string) => void;
    action: string;
    setAction: (v: string) => void;
    isSensitive: string;
    setIsSensitive: (v: string) => void;
    userId: string;
    setUserId: (v: string) => void;
    startDate: string;
    setStartDate: (v: string) => void;
    endDate: string;
    setEndDate: (v: string) => void;
    onFilter: () => void;
    onReset: () => void;
};

const AuditFilter = ({
    modelType,
    setModelType,
    action,
    setAction,
    isSensitive,
    setIsSensitive,
    userId,
    setUserId,
    startDate,
    setStartDate,
    endDate,
    setEndDate,
    onFilter,
    onReset,
}: Props) => {
    const [users, setUsers] = useState<Array<{ id: number; name: string; email: string }>>([]);

    useEffect(() => {
        let mounted = true;
        axios
            .get('/api/users?per_page=100')
            .then(res => {
                if (!mounted) return;
                setUsers(res.data.data || []);
            })
            .catch(() => {
                // ignore
            });
        return () => {
            mounted = false;
        };
    }, []);

    return (
        <div className="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div>
                <label htmlFor="model-type" className="block text-sm font-medium text-gray-700">
                    Model Type
                </label>
                <input
                    id="model-type"
                    value={modelType}
                    onChange={e => setModelType(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                    placeholder="App\\Models\\User"
                />
            </div>

            <div>
                <label htmlFor="action" className="block text-sm font-medium text-gray-700">
                    Action
                </label>
                <select
                    id="action"
                    value={action}
                    onChange={e => setAction(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                >
                    <option value="">Any</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                    <option value="restore">Restore</option>
                </select>
            </div>

            <div>
                <label htmlFor="user" className="block text-sm font-medium text-gray-700">
                    User
                </label>
                <select
                    id="user"
                    value={userId}
                    onChange={e => setUserId(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                >
                    <option value="">Any</option>
                    {users.map(u => (
                        <option key={u.id} value={String(u.id)}>
                            {u.name} ({u.email})
                        </option>
                    ))}
                </select>
            </div>

            <div>
                <label htmlFor="sensitive" className="block text-sm font-medium text-gray-700">
                    Sensitive
                </label>
                <select
                    id="sensitive"
                    value={isSensitive}
                    onChange={e => setIsSensitive(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                >
                    <option value="">Any</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div>
                <label htmlFor="start-date" className="block text-sm font-medium text-gray-700">
                    Start Date
                </label>
                <input
                    id="start-date"
                    type="date"
                    value={startDate}
                    onChange={e => setStartDate(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                />
            </div>

            <div>
                <label htmlFor="end-date" className="block text-sm font-medium text-gray-700">
                    End Date
                </label>
                <input
                    id="end-date"
                    type="date"
                    value={endDate}
                    onChange={e => setEndDate(e.target.value)}
                    className="mt-1 block w-full rounded border-gray-300"
                />
            </div>

            <div className="md:col-span-6 flex gap-2 mt-2">
                <button onClick={onFilter} className="px-4 py-2 bg-indigo-600 text-white rounded">
                    Filtrar
                </button>
                <button onClick={onReset} className="px-3 py-2 border rounded">
                    Reset
                </button>
            </div>
        </div>
    );
};

export default AuditFilter;
