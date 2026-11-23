import React from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';

export default function SettingsIndex() {
    const { props } = usePage<any>();
    const settings = props.settings || {};

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Settings</h2>}>
            <Head title="Settings" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            {Object.keys(settings).map(group => (
                                <div key={group} className="mb-6">
                                    <h3 className="font-semibold mb-2">{group}</h3>
                                    <div>
                                        {settings[group].map((s: any) => (
                                            <div
                                                key={s.key}
                                                className="flex items-center justify-between border-b py-2"
                                            >
                                                <div>
                                                    <div className="font-medium">{s.key}</div>
                                                    <div className="text-sm text-gray-500">
                                                        {s.description}
                                                    </div>
                                                </div>
                                                <div>
                                                    <a
                                                        href={`/admin/settings/${s.key}/edit`}
                                                        className="text-indigo-600"
                                                    >
                                                        Editar
                                                    </a>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
