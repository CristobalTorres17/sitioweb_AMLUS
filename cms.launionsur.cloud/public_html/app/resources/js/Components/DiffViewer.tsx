import React from 'react';

type DiffItem = {
    field: string;
    old: any;
    new: any;
};

export default function DiffViewer({ diff }: { diff: DiffItem[] }) {
    if (!diff || diff.length === 0) return <div>No hay cambios.</div>;

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                    <tr>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                            Campo
                        </th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                            Anterior
                        </th>
                        <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">
                            Nuevo
                        </th>
                    </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                    {diff.map((d, idx) => (
                        <tr key={idx}>
                            <td className="px-4 py-2 text-sm text-gray-700">{d.field}</td>
                            <td className="px-4 py-2 text-sm text-red-600 whitespace-pre-wrap">
                                {String(d.old ?? '')}
                            </td>
                            <td className="px-4 py-2 text-sm text-green-600 whitespace-pre-wrap">
                                {String(d.new ?? '')}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
