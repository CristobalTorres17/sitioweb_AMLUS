import React, { useState } from 'react';
import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import { DndContext, closestCenter } from '@dnd-kit/core';
import {
    arrayMove,
    SortableContext,
    sortableKeyboardCoordinates,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import SortableItem from '@/Components/SortableItem';

export default function MenuEdit() {
    const { props } = usePage<any>();
    const menu = props.menu || {};

    const [name, setName] = useState(menu.name || '');
    const [slug, setSlug] = useState(menu.slug || '');
    const [items, setItems] = useState<any[]>(
        (menu.items || []).sort((a: any, b: any) => a.position - b.position),
    );

    const submit = async (e: any) => {
        e.preventDefault();
        const form = new FormData();
        form.append('name', name);
        form.append('slug', slug);
        form.append('_method', 'PUT');
        await window.axios.post(`/admin/menus/${menu.id}`, form);
        window.location.href = '/admin/menus';
    };

    const handleDragEnd = async (event: any) => {
        const { active, over } = event;
        if (!over || active.id === over.id) return;

        const oldIndex = items.findIndex(i => String(i.id) === String(active.id));
        const newIndex = items.findIndex(i => String(i.id) === String(over.id));
        const newArr = arrayMove(items, oldIndex, newIndex);
        setItems(newArr);

        // send new order to server
        const order = newArr.map(i => i.id);
        try {
            await window.axios.post(`/admin/menus/${menu.id}/items/reorder`, { order });
        } catch (e) {
            console.error('Reorder failed', e);
        }
    };

    return (
        <Authenticated header={<h2 className="text-xl font-semibold">Editar menú</h2>}>
            <Head title="Editar menú" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-sm sm:rounded-lg p-6">
                        <form onSubmit={submit}>
                            <div>
                                <label>Nombre</label>
                                <input
                                    value={name}
                                    onChange={e => setName(e.target.value)}
                                    required
                                    className="border rounded px-2 py-1 w-full"
                                />
                            </div>
                            <div className="mt-2">
                                <label>Slug</label>
                                <input
                                    value={slug}
                                    onChange={e => setSlug(e.target.value)}
                                    required
                                    className="border rounded px-2 py-1 w-full"
                                />
                            </div>

                            <div className="mt-4">
                                <button className="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                        <div className="mt-6">
                            <h3 className="font-semibold mb-2">Items</h3>
                            <DndContext
                                collisionDetection={closestCenter}
                                onDragEnd={handleDragEnd}
                            >
                                <SortableContext
                                    items={items.map(i => String(i.id))}
                                    strategy={verticalListSortingStrategy}
                                >
                                    <div className="space-y-2">
                                        {items.map((it: any) => (
                                            <SortableItem key={it.id} id={String(it.id)}>
                                                <div className="p-3 border rounded bg-white flex justify-between items-center">
                                                    <div>
                                                        <div className="font-medium">
                                                            {it.title}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {it.url}
                                                        </div>
                                                    </div>
                                                    <div className="text-sm text-gray-400">↕</div>
                                                </div>
                                            </SortableItem>
                                        ))}
                                    </div>
                                </SortableContext>
                            </DndContext>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
