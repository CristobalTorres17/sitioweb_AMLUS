import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function Toast() {
    const page: any = usePage();
    const flash = page.props?.flash || {};
    const [show, setShow] = useState<boolean>(false);
    const [message, setMessage] = useState<string | null>(null);
    const [type, setType] = useState<'success' | 'error' | null>(null);

    useEffect(() => {
        if (flash?.success) {
            setMessage(flash.success);
            setType('success');
            setShow(true);
        } else if (flash?.error) {
            setMessage(flash.error);
            setType('error');
            setShow(true);
        }
    }, [flash?.success, flash?.error]);

    useEffect(() => {
        if (!show) return;
        const t = setTimeout(() => setShow(false), 4000);
        return () => clearTimeout(t);
    }, [show]);

    if (!show || !message) return null;

    return (
        <div className="fixed right-4 bottom-4 z-50">
            <div
                className={`max-w-sm w-full rounded shadow-lg px-4 py-3 flex items-start gap-3 ${
                    type === 'success'
                        ? 'bg-green-50 border border-green-200 text-green-800'
                        : 'bg-red-50 border border-red-200 text-red-800'
                }`}
                role="status"
            >
                <div className="flex-1">
                    <div className="text-sm font-medium">
                        {type === 'success' ? 'Success' : 'Error'}
                    </div>
                    <div className="text-sm mt-1">{message}</div>
                </div>

                <button
                    aria-label="Close"
                    onClick={() => setShow(false)}
                    className="text-sm opacity-80"
                >
                    ×
                </button>
            </div>
        </div>
    );
}
