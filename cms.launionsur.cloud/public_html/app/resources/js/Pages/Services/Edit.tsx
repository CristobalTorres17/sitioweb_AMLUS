import Authenticated from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useEffect, useMemo, useRef } from 'react';
import Sortable from 'sortablejs';
import MediaPicker from '@/Components/MediaPicker';
import type { SelectedMedia } from '@/types/media';

type ServiceItem = { title: string; description: string };
type RequirementItem = { title: string; description: string; is_basic: boolean };
type ServiceKind = 'service' | 'procedure';
type ServiceForm = {
    title: string;
    kind: ServiceKind;
    category_id: string;
    description: string;
    estimated_time: string;
    cost: string;
    unit: string;
    schedule: string;
    documentation: string[];
    icon: string;
    is_active: boolean;
    is_featured: boolean;
    featured_order: number;
    steps: ServiceItem[];
    requirements: RequirementItem[];
    media: SelectedMedia[];
};

export default function Edit({ service, categories }: { service: any; categories: any[] }) {
    const form = useForm<ServiceForm>({
        title: service.title || '',
        kind: (service.kind as ServiceKind) || 'service',
        category_id: service.category_id ? String(service.category_id) : '',
        description: service.description || '',
        estimated_time: service.estimated_time || '',
        cost: service.cost || '',
        unit: service.unit || '',
        schedule: service.schedule || '',
        documentation: Array.isArray(service.documentation)
            ? service.documentation
            : service.documentation
              ? [service.documentation]
              : [],
        icon: service.icon || '',
        is_active: !!service.is_active,
        is_featured: !!service.is_featured,
        featured_order: service.featured_order || 0,
        steps: (service.steps || []).map((step: any) => ({
            title: step.title,
            description: step.description || '',
        })),
        requirements: (service.requirements || []).map((requirement: any) => ({
            title: requirement.title,
            description: requirement.description || '',
            is_basic: requirement.is_basic !== undefined ? !!requirement.is_basic : true,
        })),
        media: (service.media || []).map((m: any) => ({
            media_id: m.id,
            is_primary: m.pivot?.is_primary ?? false,
            position: m.pivot?.position ?? 0,
            caption: m.pivot?.caption ?? null,
            alt_text: m.pivot?.alt_text ?? null,
            filename: m.filename,
            path: m.path,
            url: m.urls?.thumbnail ?? m.url ?? '',
        })) as SelectedMedia[],
    });

    const isProcedure = form.data.kind === 'procedure';
    const categoryType = isProcedure ? 'tramite' : 'service';
    const availableCategories = useMemo(
        () => categories.filter(c => c.type === categoryType),
        [categories, categoryType],
    );
    const selectedCategory = useMemo(
        () => availableCategories.find(c => String(c.id) === String(form.data.category_id)),
        [availableCategories, form.data.category_id],
    );

    useEffect(() => {
        if (
            form.data.category_id &&
            !availableCategories.some(
                category => String(category.id) === String(form.data.category_id),
            )
        ) {
            form.setData('category_id', '');
        }
    }, [availableCategories, form.data.category_id]);

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(route('services.update', service.id));
    }

    function updateStep(index: number, field: keyof ServiceItem, value: string) {
        const steps = [...form.data.steps];
        steps[index] = { ...steps[index], [field]: value };
        form.setData('steps', steps);
    }

    function addStep() {
        form.setData('steps', [...form.data.steps, { title: '', description: '' }]);
    }

    function removeStep(index: number) {
        form.setData(
            'steps',
            form.data.steps.filter((_, i) => i !== index),
        );
    }

    function updateRequirement(
        index: number,
        field: keyof RequirementItem,
        value: string | boolean,
    ) {
        const requirements = [...form.data.requirements];
        requirements[index] = { ...requirements[index], [field]: value };
        form.setData('requirements', requirements);
    }

    function addRequirement() {
        form.setData('requirements', [
            ...form.data.requirements,
            { title: '', description: '', is_basic: true },
        ]);
    }

    function addDocItem() {
        form.setData('documentation', [...form.data.documentation, '']);
    }

    function updateDocItem(index: number, value: string) {
        const docs = [...form.data.documentation];
        docs[index] = value;
        form.setData('documentation', docs);
    }

    function removeDocItem(index: number) {
        form.setData(
            'documentation',
            form.data.documentation.filter((_, i) => i !== index),
        );
    }

    function removeRequirement(index: number) {
        form.setData(
            'requirements',
            form.data.requirements.filter((_, i) => i !== index),
        );
    }

    const stepsListRef = useRef<HTMLDivElement>(null);
    const requirementsListRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!stepsListRef.current) return;
        Sortable.create(stepsListRef.current, {
            animation: 150,
            ghostClass: 'bg-gray-100',
            onEnd: (evt: any) => {
                const steps = [...form.data.steps];
                if (evt.oldIndex !== undefined && evt.newIndex !== undefined) {
                    const moved = steps.splice(evt.oldIndex, 1);
                    steps.splice(evt.newIndex, 0, ...moved);
                    form.setData('steps', steps);
                }
            },
        });
    }, [form.data.steps.length]);

    useEffect(() => {
        if (!requirementsListRef.current) return;
        Sortable.create(requirementsListRef.current, {
            animation: 150,
            ghostClass: 'bg-gray-100',
            onEnd: (evt: any) => {
                const requirements = [...form.data.requirements];
                if (evt.oldIndex !== undefined && evt.newIndex !== undefined) {
                    const moved = requirements.splice(evt.oldIndex, 1);
                    requirements.splice(evt.newIndex, 0, ...moved);
                    form.setData('requirements', requirements);
                }
            },
        });
    }, [form.data.requirements.length]);

    return (
        <Authenticated>
            <Head title={`Editar: ${service.title}`} />

            <div className="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                    <h2 className="mb-4 text-xl font-semibold">Editar servicio o trámite</h2>
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <label className="block text-sm font-medium">Titulo</label>
                            <input
                                className="mt-1 block w-full"
                                value={form.data.title}
                                onChange={e => form.setData('title', e.target.value)}
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Categoría</label>
                                <select
                                    className="mt-1 block w-full"
                                    value={form.data.category_id}
                                    onChange={e => form.setData('category_id', e.target.value)}
                                >
                                    <option value="">Seleccione una categoría</option>
                                    {availableCategories.map(category => (
                                        <option key={category.id} value={category.id}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Tipo</label>
                                <select
                                    className="mt-1 block w-full"
                                    value={form.data.kind}
                                    onChange={e =>
                                        form.setData('kind', e.target.value as ServiceKind)
                                    }
                                >
                                    <option value="service">Servicio</option>
                                    <option value="procedure">Trámite</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium">Descripción</label>
                            <textarea
                                className="mt-1 block w-full"
                                rows={4}
                                value={form.data.description}
                                onChange={e => form.setData('description', e.target.value)}
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Costo</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.cost}
                                    onChange={e => form.setData('cost', e.target.value)}
                                    placeholder={isProcedure ? 'Ej. $10.50' : 'Opcional'}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Tiempo estimado</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.estimated_time}
                                    onChange={e => form.setData('estimated_time', e.target.value)}
                                    placeholder={isProcedure ? 'Ej. 2 días hábiles' : 'Opcional'}
                                />
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium">Unidad</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.unit}
                                    onChange={e => form.setData('unit', e.target.value)}
                                    placeholder="Unidad responsable"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium">Horario</label>
                                <input
                                    className="mt-1 block w-full"
                                    value={form.data.schedule}
                                    onChange={e => form.setData('schedule', e.target.value)}
                                    placeholder="Lunes a Viernes 8 AM - 4 PM"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium">
                                Icono (Font Awesome)
                            </label>
                            <input
                                className="mt-1 block w-full"
                                value={form.data.icon}
                                onChange={e => form.setData('icon', e.target.value)}
                                placeholder="fa-solid fa-file"
                            />
                        </div>

                        <section className="rounded-lg border p-4">
                            <div className="mb-3 flex items-center justify-between">
                                <label className="font-medium text-sm">
                                    Documentación requerida
                                </label>
                                <button
                                    type="button"
                                    className="rounded border px-3 py-1 text-sm"
                                    onClick={addDocItem}
                                >
                                    + Agregar
                                </button>
                            </div>
                            {form.data.documentation.length === 0 && (
                                <p className="text-sm text-gray-500">Sin documentos requeridos.</p>
                            )}
                            <div className="space-y-2">
                                {form.data.documentation.map((doc: string, index: number) => (
                                    <div key={index} className="flex gap-2 items-center">
                                        <input
                                            className="flex-1"
                                            value={doc}
                                            onChange={e => updateDocItem(index, e.target.value)}
                                            placeholder={`Documento ${index + 1}`}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeDocItem(index)}
                                            className="text-red-600 text-sm"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <div className="flex items-center gap-4 text-sm">
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={form.data.is_active}
                                    onChange={e => form.setData('is_active', e.target.checked)}
                                />
                                Activo
                            </label>
                        </div>

                        <section className="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <div className="mb-3 flex items-center justify-between">
                                <h3 className="font-semibold text-blue-900">
                                    Destacado en el Home
                                </h3>
                            </div>
                            <label className="mb-3 flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={form.data.is_featured}
                                    onChange={e => form.setData('is_featured', e.target.checked)}
                                />
                                <span className="text-sm">
                                    Destacar este servicio en la página de inicio
                                </span>
                            </label>
                            {form.data.is_featured && (
                                <div>
                                    <label className="block text-sm font-medium text-blue-900">
                                        Orden de visualización (1-4)
                                    </label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="4"
                                        value={form.data.featured_order || 1}
                                        onChange={e =>
                                            form.setData(
                                                'featured_order',
                                                parseInt(e.target.value) || 1,
                                            )
                                        }
                                        className="mt-1 block w-full"
                                    />
                                    <p className="mt-1 text-xs text-blue-700">
                                        Posición 1 = Grande (a la izquierda), 2-4 = Pequeños (a la
                                        derecha)
                                    </p>
                                </div>
                            )}
                        </section>

                        <section className="rounded-lg border p-4">
                            <div className="mb-3 flex items-center justify-between">
                                <h3 className="font-semibold">
                                    Steps{' '}
                                    <span className="text-xs text-gray-500">
                                        (drag to reorder,{' '}
                                        {isProcedure
                                            ? 'required for trámites'
                                            : 'optional for services'}
                                        )
                                    </span>
                                </h3>
                                <button
                                    type="button"
                                    className="rounded border px-3 py-1 text-sm"
                                    onClick={addStep}
                                >
                                    + Add step
                                </button>
                            </div>
                            <div ref={stepsListRef} className="space-y-3">
                                {form.data.steps.length === 0 && (
                                    <p className="text-sm text-gray-500">
                                        Add steps if this entry is a trámite.
                                    </p>
                                )}
                                {form.data.steps.map((step: ServiceItem, index: number) => (
                                    <div
                                        key={index}
                                        className="cursor-move rounded border p-3 hover:bg-gray-50"
                                        draggable
                                    >
                                        <div className="mb-2 flex items-center justify-between text-xs text-gray-500">
                                            <span>Step {index + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeStep(index)}
                                                className="text-red-600"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <input
                                            className="w-full"
                                            placeholder="Step title"
                                            value={step.title}
                                            onChange={e =>
                                                updateStep(index, 'title', e.target.value)
                                            }
                                        />
                                        <textarea
                                            className="mt-2 w-full"
                                            placeholder="Description"
                                            value={step.description}
                                            onChange={e =>
                                                updateStep(index, 'description', e.target.value)
                                            }
                                        />
                                    </div>
                                ))}
                            </div>
                        </section>

                        <section className="rounded-lg border p-4">
                            <div className="mb-3 flex items-center justify-between">
                                <h3 className="font-semibold">
                                    Requirements{' '}
                                    <span className="text-xs text-gray-500">
                                        (drag to reorder,{' '}
                                        {isProcedure
                                            ? 'required for trámites'
                                            : 'optional for services'}
                                        )
                                    </span>
                                </h3>
                                <button
                                    type="button"
                                    className="rounded border px-3 py-1 text-sm"
                                    onClick={addRequirement}
                                >
                                    + Add requirement
                                </button>
                            </div>
                            <div ref={requirementsListRef} className="space-y-3">
                                {form.data.requirements.length === 0 && (
                                    <p className="text-sm text-gray-500">
                                        Requirements are optional for informational services.
                                    </p>
                                )}
                                {form.data.requirements.map(
                                    (requirement: RequirementItem, index: number) => (
                                        <div
                                            key={index}
                                            className="cursor-move rounded border p-3 hover:bg-gray-50"
                                            draggable
                                        >
                                            <div className="mb-2 flex items-center justify-between text-xs text-gray-500">
                                                <span>Requirement {index + 1}</span>
                                                <button
                                                    type="button"
                                                    onClick={() => removeRequirement(index)}
                                                    className="text-red-600"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                            <input
                                                className="w-full"
                                                placeholder="Requirement title"
                                                value={requirement.title}
                                                onChange={e =>
                                                    updateRequirement(
                                                        index,
                                                        'title',
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            <textarea
                                                className="mt-2 w-full"
                                                placeholder="Description"
                                                value={requirement.description}
                                                onChange={e =>
                                                    updateRequirement(
                                                        index,
                                                        'description',
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            <label className="mt-2 flex items-center gap-2 text-xs text-gray-600">
                                                <input
                                                    type="checkbox"
                                                    checked={requirement.is_basic}
                                                    onChange={e =>
                                                        updateRequirement(
                                                            index,
                                                            'is_basic',
                                                            e.target.checked,
                                                        )
                                                    }
                                                />
                                                Requisito básico
                                            </label>
                                        </div>
                                    ),
                                )}
                            </div>
                        </section>

                        <button type="submit" className="rounded bg-primary px-4 py-2 text-white">
                            Save Service
                        </button>
                    </form>
                </div>

                <div className="rounded-md bg-white p-6 shadow sm:rounded-lg">
                    <h3 className="mb-4 text-lg font-medium">Imágenes del servicio</h3>
                    <MediaPicker
                        label="Galería de imágenes"
                        value={form.data.media}
                        onChange={media => form.setData('media', media ?? [])}
                    />

                    <h3 className="mb-4 mt-6 text-lg font-medium">Preview</h3>
                    <div className="mt-4 rounded-xl border p-4">
                        <div className="text-xs uppercase text-gray-500">
                            {form.data.kind === 'procedure' ? 'Trámite' : 'Servicio'}
                        </div>
                        <div className="mt-2 text-xl font-semibold">
                            {form.data.title || 'Service title'}
                        </div>
                        <p className="mt-2 text-sm text-gray-600">
                            {form.data.description || 'Service description'}
                        </p>
                        <div className="mt-3 grid gap-2 text-sm text-gray-500 md:grid-cols-2">
                            <div>Estimated time: {form.data.estimated_time || '-'}</div>
                            <div>Cost: {form.data.cost || '-'}</div>
                            <div>Unit: {form.data.unit || '-'}</div>
                            <div>Schedule: {form.data.schedule || '-'}</div>
                        </div>
                        <div className="mt-3 rounded bg-gray-50 p-3 text-sm text-gray-600">
                            {form.data.documentation.length > 0 ? (
                                <ul className="list-disc pl-4 space-y-1">
                                    {form.data.documentation.map((doc: string, i: number) => (
                                        <li key={i}>{doc}</li>
                                    ))}
                                </ul>
                            ) : (
                                'No documentation provided'
                            )}
                        </div>
                        <div className="mt-4 text-xs uppercase text-gray-500">
                            {selectedCategory?.name || 'No category selected'}
                        </div>

                        <div className="mt-4">
                            <div className="font-medium">Steps</div>
                            <ol className="mt-2 space-y-2 text-sm text-gray-600">
                                {form.data.steps.map((step: ServiceItem, index: number) => (
                                    <li key={index} className="rounded bg-gray-50 p-2">
                                        <div className="font-medium">
                                            {index + 1}. {step.title || 'Untitled step'}
                                        </div>
                                        <div>{step.description || 'No description'}</div>
                                    </li>
                                ))}
                            </ol>
                        </div>

                        <div className="mt-4">
                            <div className="font-medium">Requirements</div>
                            <ul className="mt-2 space-y-2 text-sm text-gray-600">
                                {form.data.requirements.map(
                                    (requirement: ServiceItem, index: number) => (
                                        <li key={index} className="rounded bg-gray-50 p-2">
                                            <div className="font-medium">
                                                {requirement.title || 'Untitled requirement'}
                                            </div>
                                            <div>{requirement.description || 'No description'}</div>
                                        </li>
                                    ),
                                )}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
