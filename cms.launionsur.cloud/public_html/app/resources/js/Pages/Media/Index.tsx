import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import MediaUploader from '@/Components/MediaUploader';

export default function Index() {
    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Media Library</h2>}>
            <Head title="Media Library" />

            <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                <h3 className="text-lg font-medium mb-4">Media</h3>

                <MediaUploader />
            </div>
        </Authenticated>
    );
}
