import BlockPreviewPanel from '@/Components/CmsBlocks/BlockPreviewPanel';
import TemplatePickerPanel from '@/Components/CmsBlocks/TemplatePickerPanel';
import MediaPicker from '@/Components/MediaPicker';
import RichTextEditor from '@/Components/RichTextEditor';
import {
    buildPreviewHtml,
    defaultTemplateData,
    formatJson,
    mediaToSelected,
    normalizeEditorialDescriptions,
    parseJson,
} from './CmsBlockTemplateFieldsHelpers';
import { useEffect, useMemo, useRef, useState } from 'react';

type TemplateDefinition = {
    label: string;
    description: string;
    zones: Record<string, string>;
};

type Props = {
    templates: Record<string, TemplateDefinition>;
    data: any;
    setData: (field: string, value: any) => void;
};

const HTML_SNIPPETS: { label: string; value: string }[] = [
    { label: '— Insertar snippet —', value: '' },
    {
        label: 'Google Maps (iframe)',
        value:
            '<div class="cms-map-embed" style="width:100%;overflow:hidden;border-radius:8px;">\n' +
            '  <iframe\n' +
            '    src="https://www.google.com/maps/embed?pb=PEGAR_ID_EMBED_AQUI"\n' +
            '    width="100%"\n' +
            '    height="400"\n' +
            '    style="border:0;display:block;"\n' +
            '    allowfullscreen\n' +
            '    loading="lazy"\n' +
            '    referrerpolicy="no-referrer-when-downgrade"\n' +
            '  ></iframe>\n' +
            '</div>',
    },
    {
        label: 'YouTube (iframe)',
        value:
            '<div class="cms-video-embed" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;">\n' +
            '  <iframe\n' +
            '    src="https://www.youtube.com/embed/VIDEO_ID_AQUI"\n' +
            '    style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"\n' +
            '    allowfullscreen\n' +
            '    loading="lazy"\n' +
            '  ></iframe>\n' +
            '</div>',
    },
    {
        label: 'iframe genérico',
        value:
            '<div class="cms-embed" style="width:100%;overflow:hidden;">\n' +
            '  <iframe\n' +
            '    src="https://PEGAR_URL_AQUI"\n' +
            '    width="100%"\n' +
            '    height="500"\n' +
            '    style="border:0;display:block;"\n' +
            '    loading="lazy"\n' +
            '  ></iframe>\n' +
            '</div>',
    },
    {
        label: 'Post de Facebook',
        value:
            '<div class="cms-fb-embed" style="display:flex;justify-content:center;">\n' +
            '  <div class="fb-post" data-href="URL_DEL_POST_AQUI" data-width="500"></div>\n' +
            '  <div id="fb-root"></div>\n' +
            '  <script async defer crossorigin="anonymous"\n' +
            '    src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v19.0">\n' +
            '  </script>\n' +
            '</div>',
    },
    {
        label: 'HTML libre',
        value: '<!-- Tu HTML personalizado -->\n<div class="cms-custom">\n  \n</div>',
    },
];

const ICON_OPTIONS = [
    { value: 'person', label: 'Persona' },
    { value: 'verified', label: 'Verificado' },
    { value: 'volunteer_activism', label: 'Compromiso social' },
    { value: 'groups', label: 'Grupos' },
    { value: 'public', label: 'Público' },
    { value: 'description', label: 'Documento' },
    { value: 'campaign', label: 'Campaña' },
    { value: 'shield', label: 'Seguridad' },
    { value: 'handshake', label: 'Acuerdo' },
    { value: 'lightbulb', label: 'Idea' },
    { value: 'fact_check', label: 'Validación' },
];

const COLOR_SWATCHES = [
    { label: 'Azul principal', value: '#1a2236' },
    { label: 'Secundario', value: '#f59e0b' },
    { label: 'Superficie', value: '#f5f6fa' },
    { label: 'Blanco', value: '#ffffff' },
    { label: 'Entidad 1', value: '#012340' },
    { label: 'Entidad 2', value: '#012840' },
    { label: 'Entidad 3', value: '#d9b777' },
    { label: 'Entidad 4', value: '#d9c39a' },
    { label: 'Entidad 5', value: '#8c603a' },
    { label: 'Sin color', value: 'transparent' },
];

function ColorPickerField({
    label,
    value,
    onChange,
    allowEmpty = false,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
    allowEmpty?: boolean;
}) {
    const colorInputRef = useRef<HTMLInputElement>(null);
    const [pickerOpen, setPickerOpen] = useState(false);

    const resolveHex = (v: string) => {
        if (v === 'primary') return '#1a2236';
        if (v === 'secondary') return '#f59e0b';
        if (v === 'surface') return '#f5f6fa';
        if (v === 'white') return '#ffffff';
        return v;
    };

    const displayHex = resolveHex(value || 'transparent');
    const isTransparent = displayHex === 'transparent';
    const openNativePicker = () => colorInputRef.current?.click();
    const applyColor = (nextValue: string, closePicker = true) => {
        onChange(nextValue);
        if (closePicker) setPickerOpen(false);
    };

    return (
        <div className="space-y-2">
            <label className="block text-sm font-medium">{label}</label>
            <button
                type="button"
                onClick={() => setPickerOpen(v => !v)}
                className="flex w-full items-center justify-between gap-3 rounded border border-border bg-white px-3 py-2 text-left transition hover:border-primary"
                aria-expanded={pickerOpen}
            >
                <div className="flex items-center gap-2 overflow-hidden">
                    <span
                        className="h-5 w-5 shrink-0 rounded border border-border"
                        style={{
                            background: isTransparent
                                ? 'linear-gradient(135deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%),linear-gradient(135deg,#ccc 25%,#fff 25%,#fff 75%,#ccc 75%)'
                                : displayHex,
                            backgroundSize: isTransparent ? '8px 8px' : undefined,
                            backgroundPosition: isTransparent ? '0 0,4px 4px' : undefined,
                        }}
                    />
                    <span className="truncate text-sm font-semibold text-primary">
                        {isTransparent ? 'Sin color' : displayHex}
                    </span>
                </div>
                <span className="text-xs text-text-secondary">
                    {pickerOpen ? 'Cerrar' : 'Elegir'}
                </span>
            </button>

            {pickerOpen && (
                <div className="space-y-3 rounded border border-border bg-white p-3 shadow-sm">
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={openNativePicker}
                            className="flex h-10 min-w-28 items-center gap-2 rounded border border-border bg-white px-2.5 py-1.5 text-left text-xs font-semibold text-primary transition hover:border-primary"
                            title="Abrir selector de color"
                        >
                            <span
                                className="h-5 w-5 rounded border border-border"
                                style={{
                                    background: isTransparent
                                        ? 'linear-gradient(135deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%),linear-gradient(135deg,#ccc 25%,#fff 25%,#fff 75%,#ccc 75%)'
                                        : displayHex,
                                    backgroundSize: isTransparent ? '8px 8px' : undefined,
                                    backgroundPosition: isTransparent ? '0 0,4px 4px' : undefined,
                                }}
                            />
                            <span className="truncate">
                                {isTransparent ? 'Sin color' : displayHex}
                            </span>
                        </button>
                        <input
                            ref={colorInputRef}
                            type="color"
                            value={isTransparent ? '#ffffff' : displayHex || '#ffffff'}
                            onChange={e => applyColor(e.target.value)}
                            className="sr-only"
                            aria-label={`Seleccionar color para ${label}`}
                        />
                        <input
                            type="text"
                            value={value}
                            onChange={e => onChange(e.target.value)}
                            onBlur={() => setPickerOpen(false)}
                            onKeyDown={e => {
                                if (e.key === 'Enter') {
                                    setPickerOpen(false);
                                    (e.currentTarget as HTMLInputElement).blur();
                                }
                                if (e.key === 'Escape') setPickerOpen(false);
                            }}
                            className="flex-1 rounded border border-border bg-white px-2 py-1.5 text-xs font-mono"
                            placeholder="transparent / #rrggbb"
                        />
                    </div>

                    <div className="flex flex-wrap gap-1.5">
                        {allowEmpty && (
                            <button
                                type="button"
                                title="Sin color"
                                onClick={() => applyColor('')}
                                className={`flex h-6 w-6 items-center justify-center rounded-full border-2 transition ${
                                    !value
                                        ? 'border-primary ring-2 ring-primary ring-offset-1'
                                        : 'border-border'
                                }`}
                                style={{ background: '#f9fafb' }}
                            >
                                <span style={{ fontSize: 10, color: '#9ca3af' }}>✕</span>
                            </button>
                        )}
                        {COLOR_SWATCHES.map(s => {
                            const swatchHex = resolveHex(s.value);
                            const selected = displayHex === swatchHex;

                            return (
                                <button
                                    key={s.value}
                                    type="button"
                                    title={s.label}
                                    onClick={() => applyColor(s.value)}
                                    className="h-6 w-6 rounded-full border-2 transition"
                                    style={{
                                        background:
                                            s.value === 'transparent'
                                                ? 'linear-gradient(135deg,#ccc 25%,transparent 25%,transparent 75%,#ccc 75%),linear-gradient(135deg,#ccc 25%,#fff 25%,#fff 75%,#ccc 75%)'
                                                : s.value,
                                        backgroundSize:
                                            s.value === 'transparent' ? '8px 8px' : undefined,
                                        backgroundPosition:
                                            s.value === 'transparent' ? '0 0,4px 4px' : undefined,
                                        borderColor: selected ? '#1a2236' : '#d1d5db',
                                        outline: selected ? '2px solid #1a2236' : 'none',
                                        outlineOffset: '2px',
                                    }}
                                />
                            );
                        })}
                    </div>
                </div>
            )}
        </div>
    );
}

function ImageStyleFields({
    label,
    prefix,
    data,
    setData,
}: {
    label: string;
    prefix: string;
    data: any;
    setData: (field: string, value: any) => void;
}) {
    const border = data[`${prefix}_border`] || 'none';

    return (
        <div className="space-y-4 rounded-lg border border-border bg-white p-4">
            <div>
                <h5 className="text-sm font-semibold text-primary">{label}</h5>
                <p className="text-xs text-text-secondary">Configura borde, sombra y redondeo.</p>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div>
                    <label className="mb-1 block text-sm font-medium">Borde</label>
                    <select
                        value={border}
                        onChange={e => setData(`${prefix}_border`, e.target.value)}
                        className="w-full rounded border border-border bg-white px-3 py-2"
                    >
                        <option value="none">Sin borde</option>
                        <option value="solid">Sólido</option>
                        <option value="dashed">Punteado</option>
                        <option value="dotted">Dotted</option>
                    </select>
                </div>
                <div>
                    <label className="mb-1 block text-sm font-medium">Sombra</label>
                    <select
                        value={data[`${prefix}_shadow`] || 'none'}
                        onChange={e => setData(`${prefix}_shadow`, e.target.value)}
                        className="w-full rounded border border-border bg-white px-3 py-2"
                    >
                        <option value="none">Sin sombra</option>
                        <option value="sm">Suave</option>
                        <option value="md">Mediana</option>
                        <option value="lg">Grande</option>
                        <option value="xl">Muy grande</option>
                        <option value="2xl">Extra grande</option>
                    </select>
                </div>
            </div>

            {border !== 'none' && (
                <div className="grid gap-4 md:grid-cols-2">
                    <div>
                        <label className="mb-1 block text-sm font-medium">Grosor</label>
                        <select
                            value={data[`${prefix}_border_width`] || '1'}
                            onChange={e => setData(`${prefix}_border_width`, e.target.value)}
                            className="w-full rounded border border-border bg-white px-3 py-2"
                        >
                            <option value="1">1px</option>
                            <option value="2">2px</option>
                            <option value="4">4px</option>
                            <option value="8">8px</option>
                        </select>
                    </div>
                    <ColorPickerField
                        label="Color del borde"
                        value={data[`${prefix}_border_color`] || '#e5e7eb'}
                        onChange={v => setData(`${prefix}_border_color`, v)}
                    />
                </div>
            )}

            <div>
                <label className="mb-1 block text-sm font-medium">Redondeo</label>
                <select
                    value={data[`${prefix}_radius`] || 'none'}
                    onChange={e => setData(`${prefix}_radius`, e.target.value)}
                    className="w-full rounded border border-border bg-white px-3 py-2"
                >
                    <option value="none">Sin redondeo</option>
                    <option value="sm">4px</option>
                    <option value="md">8px</option>
                    <option value="lg">12px</option>
                    <option value="xl">16px</option>
                    <option value="2xl">24px</option>
                    <option value="full">Completo</option>
                </select>
            </div>
        </div>
    );
}

export default function CmsBlockTemplateFields({ templates, data, setData }: Props) {
    const templateKey = data.template_key || 'rich_text';
    const currentTemplate = templates[templateKey] || templates.rich_text;
    const availableZones = Object.keys(currentTemplate?.zones || {});
    const templateData =
        data.template_data && typeof data.template_data === 'object' ? data.template_data : {};
    const [jsonDraft, setJsonDraft] = useState(() => formatJson(templateData));
    const [jsonError, setJsonError] = useState<string | null>(null);
    const [pickerOpen, setPickerOpen] = useState(false);
    const [jsonExpanded, setJsonExpanded] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [wrapperSectionOpen, setWrapperSectionOpen] = useState(false);
    const [expandedCardIndices, setExpandedCardIndices] = useState<Record<number, boolean>>({
        0: true,
    });
    const [expandedSectionIndices, setExpandedSectionIndices] = useState<Record<number, boolean>>({
        0: true,
    });
    const embedDialogRef = useRef<HTMLDialogElement>(null);
    const customHtml = templateData._custom_html || '';
    const htmlHasRiskyContent =
        customHtml.length > 0 && /(<script[\s>]|javascript\s*:|on\w+\s*=)/i.test(customHtml);
    const templateDataSignature = useMemo(() => formatJson(templateData), [templateData]);

    // Validar que la zone actual siempre sea válida para el template actual
    useEffect(() => {
        const currentZone = data.zone;
        const availableZones = Object.keys(currentTemplate?.zones || {});

        if (availableZones.length > 0 && !availableZones.includes(currentZone || '')) {
            setData('zone', availableZones[0]);
        }
    }, [templateKey, currentTemplate, data.zone, setData]);

    const previewHtml = useMemo(
        () => buildPreviewHtml(templateKey, templateData, data.content || ''),
        [templateKey, templateData, data.content],
    );

    useEffect(() => {
        if (templateKey === 'rich_text') return;
        if (data.content !== previewHtml) {
            setData('content', previewHtml);
        }
    }, [templateKey, previewHtml, data.content, setData]);

    useEffect(() => {
        if (templateKey === 'rich_text') {
            setJsonDraft('');
            setJsonError(null);
            return;
        }

        setJsonDraft(templateDataSignature);
        setJsonError(null);
    }, [templateKey, templateDataSignature]);

    useEffect(() => {
        if (templateKey === 'rich_text') return;

        if (Object.keys(templateData).length === 0) {
            const nextTemplateData = defaultTemplateData(templateKey);
            setData('template_data', nextTemplateData);
            setData('content', buildPreviewHtml(templateKey, nextTemplateData, data.content || ''));
            setJsonDraft(formatJson(nextTemplateData));
        }
    }, [templateKey, templateData, data.content, setData]);

    const handleTemplateChange = (nextTemplateKey: string) => {
        if (
            nextTemplateKey !== templateKey &&
            Object.keys(templateData).some(k => !k.startsWith('_custom_') && templateData[k])
        ) {
            if (!window.confirm('¿Cambiar de plantilla descartará los datos actuales. Continuar?'))
                return;
        }
        const nextTemplateData = defaultTemplateData(nextTemplateKey);
        const nextTemplateZones = templates[nextTemplateKey]?.zones || {};
        const nextZone = Object.keys(nextTemplateZones)[0] || 'content';

        setData('template_key', nextTemplateKey);
        setData('zone', nextZone);
        setData('template_data', nextTemplateData);
        setData('content', buildPreviewHtml(nextTemplateKey, nextTemplateData, data.content || ''));
        setJsonDraft(formatJson(nextTemplateData));
        setJsonError(null);
        setPickerOpen(false);
        setExpandedCardIndices({ 0: true });
    };

    const updateTemplateData = (key: string, value: any) => {
        const nextTemplateData = { ...templateData, [key]: value };
        setData('template_data', nextTemplateData);
        setData('content', buildPreviewHtml(templateKey, nextTemplateData, data.content || ''));
        setJsonDraft(formatJson(nextTemplateData));
        setJsonError(null);
    };

    const updateCard = (index: number, field: string, value: any) => {
        const cards = Array.isArray(templateData.cards) ? [...templateData.cards] : [];
        cards[index] = { ...(cards[index] || {}), [field]: value };
        updateTemplateData('cards', cards);
    };

    // ── Article Sections handlers ────────────────────────────────────────────
    const articleSections: any[] = Array.isArray(templateData.sections)
        ? templateData.sections
        : [];

    const updateArticleSection = (index: number, field: string, value: any) => {
        const next = [...articleSections];
        next[index] = { ...(next[index] || {}), [field]: value };
        updateTemplateData('sections', next);
    };

    const addArticleSection = (type: string) => {
        const defaults: Record<string, any> = {
            text: { type: 'text', content: '' },
            image: { type: 'image', image: null, caption: '', width: 'full', align: 'center' },
            video: { type: 'video', url: '', caption: '' },
            quote: { type: 'quote', text: '', author_name: '', author_title: '' },
            callout: { type: 'callout', title: '', body: '', callout_type: 'info' },
            separator: { type: 'separator', style: 'line' },
            gallery: { type: 'gallery', images: [], cols: '3', aspect: 'photo', width: 'content' },
            embed: { type: 'embed', url: '', html: '', caption: '' },
            card: {
                type: 'card',
                card_variant: 'content',
                title: '',
                subtitle: '',
                paragraphs: [
                    {
                        text: '',
                        color: '',
                        size: 'base',
                        weight: 'normal',
                        italic: false,
                        line_height: 'relaxed',
                    },
                ],
                card_bg: '',
                card_border: 'none',
                card_border_width: '1',
                card_border_color: '#e5e7eb',
                card_radius: 'lg',
                card_shadow: 'md',
                card_padding: 'lg',
                card_align: 'left',
                card_width: 'full',
            },
        };
        const newSection = defaults[type] ?? { type };
        const next = [...articleSections, newSection];
        updateTemplateData('sections', next);
        setExpandedSectionIndices(prev => ({ ...prev, [next.length - 1]: true }));
    };

    const removeArticleSection = (index: number) => {
        updateTemplateData(
            'sections',
            articleSections.filter((_, i) => i !== index),
        );
        setExpandedSectionIndices(prev => {
            const updated: Record<number, boolean> = {};
            Object.entries(prev).forEach(([k, v]) => {
                const ki = Number(k);
                if (ki < index) updated[ki] = v;
                else if (ki > index) updated[ki - 1] = v;
            });
            return updated;
        });
    };

    const moveArticleSection = (index: number, dir: 'up' | 'down') => {
        const next = [...articleSections];
        const swap = dir === 'up' ? index - 1 : index + 1;
        if (swap < 0 || swap >= next.length) return;
        [next[index], next[swap]] = [next[swap], next[index]];
        updateTemplateData('sections', next);
    };

    const updateListItem = (listKey: string, index: number, field: string, value: any) => {
        const list = Array.isArray(templateData[listKey]) ? [...templateData[listKey]] : [];
        list[index] = { ...(list[index] || {}), [field]: value };
        updateTemplateData(listKey, list);
    };

    const editorialDescriptions = normalizeEditorialDescriptions(templateData);

    const updateEditorialDescription = (index: number, field: string, value: any) => {
        const nextDescriptions = [...editorialDescriptions];
        nextDescriptions[index] = { ...nextDescriptions[index], [field]: value };
        updateTemplateData('descriptions', nextDescriptions);
    };

    const addEditorialDescription = () => {
        updateTemplateData('descriptions', [
            ...editorialDescriptions,
            {
                subtitle: '',
                description: '',
                icon: '',
                icon_position: 'left',
            },
        ]);
    };

    const removeEditorialDescription = (index: number) => {
        updateTemplateData(
            'descriptions',
            editorialDescriptions.filter((_, currentIndex) => currentIndex !== index),
        );
    };

    const applyJsonDraft = () => {
        const result = parseJson(jsonDraft);

        if (!result.ok) {
            setJsonError(result.error);
            return;
        }

        setJsonError(null);
        setData('template_data', result.data);
        setData('content', buildPreviewHtml(templateKey, result.data, data.content || ''));
    };

    return (
        <div
            className={`grid gap-6 ${sidebarOpen ? 'lg:grid-cols-[1.25fr_0.75fr]' : 'grid-cols-1'}`}
        >
            <div className="space-y-6">
                <div>
                    <div className="mb-2 flex items-center justify-between gap-2">
                        <div className="flex items-center gap-3">
                            <label className="block text-sm font-medium">Plantilla</label>
                            <span className="inline-flex items-center rounded-full bg-surface px-2 py-0.5 text-xs font-medium text-text-secondary">
                                {Object.keys(templates || {}).length} plantillas
                            </span>
                        </div>
                        <button
                            type="button"
                            onClick={() => setPickerOpen(v => !v)}
                            className="flex items-center gap-1.5 rounded-full border border-border bg-white px-3 py-1 text-xs font-semibold text-primary transition hover:border-primary"
                        >
                            {pickerOpen ? 'Cerrar' : 'Cambiar plantilla'}
                            <svg
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                className={`h-3 w-3 transition-transform ${pickerOpen ? 'rotate-180' : ''}`}
                            >
                                <path
                                    d="M4 6l4 4 4-4"
                                    stroke="currentColor"
                                    strokeWidth="1.5"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    fill="none"
                                />
                            </svg>
                        </button>
                    </div>

                    {pickerOpen ? (
                        <TemplatePickerPanel
                            templates={templates}
                            selectedKey={templateKey}
                            onSelect={handleTemplateChange}
                        />
                    ) : (
                        <div className="flex items-center gap-3 rounded-lg border border-border bg-white px-3 py-2.5">
                            <div className="h-8 w-8 shrink-0 text-primary">
                                {/* mini thumb indicator */}
                                <svg
                                    viewBox="0 0 16 16"
                                    fill="currentColor"
                                    className="h-full w-full opacity-60"
                                >
                                    <rect x="1" y="2" width="14" height="2" rx="1" />
                                    <rect
                                        x="1"
                                        y="6"
                                        width="12"
                                        height="1.5"
                                        rx="0.75"
                                        opacity="0.6"
                                    />
                                    <rect
                                        x="1"
                                        y="10"
                                        width="10"
                                        height="1.5"
                                        rx="0.75"
                                        opacity="0.45"
                                    />
                                    <rect
                                        x="1"
                                        y="13"
                                        width="7"
                                        height="1.5"
                                        rx="0.75"
                                        opacity="0.3"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p className="text-sm font-semibold text-primary">
                                    {currentTemplate.label}
                                </p>
                                <p className="text-xs text-text-secondary">
                                    {currentTemplate.description}
                                </p>
                            </div>
                        </div>
                    )}
                </div>

                <div>
                    <label className="mb-2 block text-sm font-medium">Zone</label>
                    <select
                        value={data.zone || 'content'}
                        onChange={e => setData('zone', e.target.value)}
                        className="w-full rounded border border-border bg-white px-3 py-2"
                    >
                        {availableZones && availableZones.length > 0 ? (
                            availableZones.map(value => (
                                <option key={value} value={value}>
                                    {currentTemplate?.zones?.[value] || value}
                                </option>
                            ))
                        ) : (
                            <option value="content">Contenido principal</option>
                        )}
                    </select>
                </div>

                {templateKey !== 'rich_text' && (
                    <div className="rounded-lg border border-border bg-surface shadow-sm">
                        <button
                            type="button"
                            onClick={() => setWrapperSectionOpen(v => !v)}
                            className="flex w-full items-center justify-between gap-3 px-4 py-3"
                        >
                            <div className="flex items-center gap-2">
                                <svg
                                    viewBox="0 0 16 16"
                                    fill="currentColor"
                                    className={`h-3.5 w-3.5 text-text-secondary transition-transform ${wrapperSectionOpen ? 'rotate-90' : ''}`}
                                >
                                    <path
                                        d="M6 4l4 4-4 4"
                                        stroke="currentColor"
                                        strokeWidth="1.5"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        fill="none"
                                    />
                                </svg>
                                <h4 className="text-sm font-semibold text-primary">
                                    Contenedor del bloque
                                </h4>
                            </div>
                            <span className="text-xs text-text-secondary">
                                Borde · Sombra · Fondo · Padding
                            </span>
                        </button>

                        {wrapperSectionOpen && (
                            <div className="space-y-5 border-t border-border p-4">
                                <ColorPickerField
                                    label="Fondo"
                                    value={templateData._wrap_bg || 'transparent'}
                                    onChange={v => updateTemplateData('_wrap_bg', v)}
                                />

                                <div className="grid gap-3 md:grid-cols-3">
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            Borde
                                        </label>
                                        <select
                                            value={templateData._wrap_border || 'none'}
                                            onChange={e =>
                                                updateTemplateData('_wrap_border', e.target.value)
                                            }
                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                        >
                                            <option value="none">Sin borde</option>
                                            <option value="solid">Sólido</option>
                                            <option value="dashed">Punteado</option>
                                            <option value="dotted">Dotted</option>
                                        </select>
                                    </div>
                                    {templateData._wrap_border &&
                                        templateData._wrap_border !== 'none' && (
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Grosor
                                                </label>
                                                <select
                                                    value={templateData._wrap_border_width || '1'}
                                                    onChange={e =>
                                                        updateTemplateData(
                                                            '_wrap_border_width',
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="1">1px</option>
                                                    <option value="2">2px</option>
                                                    <option value="4">4px</option>
                                                    <option value="8">8px</option>
                                                </select>
                                            </div>
                                        )}
                                    {templateData._wrap_border &&
                                        templateData._wrap_border !== 'none' && (
                                            <ColorPickerField
                                                label="Color del borde"
                                                value={templateData._wrap_border_color || '#e5e7eb'}
                                                onChange={v =>
                                                    updateTemplateData('_wrap_border_color', v)
                                                }
                                            />
                                        )}
                                </div>

                                <div className="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            Redondeo
                                        </label>
                                        <select
                                            value={templateData._wrap_radius || 'none'}
                                            onChange={e =>
                                                updateTemplateData('_wrap_radius', e.target.value)
                                            }
                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                        >
                                            <option value="none">Sin redondeo</option>
                                            <option value="sm">4px</option>
                                            <option value="md">8px</option>
                                            <option value="lg">12px</option>
                                            <option value="xl">16px</option>
                                            <option value="2xl">24px</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            Sombra
                                        </label>
                                        <select
                                            value={templateData._wrap_shadow || 'none'}
                                            onChange={e =>
                                                updateTemplateData('_wrap_shadow', e.target.value)
                                            }
                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                        >
                                            <option value="none">Sin sombra</option>
                                            <option value="sm">Suave</option>
                                            <option value="md">Mediana</option>
                                            <option value="lg">Grande</option>
                                            <option value="xl">Muy grande</option>
                                            <option value="2xl">Extra grande</option>
                                        </select>
                                    </div>
                                </div>

                                <div className="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            Padding horizontal
                                        </label>
                                        <select
                                            value={templateData._wrap_padding_x || 'none'}
                                            onChange={e =>
                                                updateTemplateData(
                                                    '_wrap_padding_x',
                                                    e.target.value,
                                                )
                                            }
                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                        >
                                            <option value="none">Sin padding</option>
                                            <option value="sm">Pequeño</option>
                                            <option value="md">Mediano</option>
                                            <option value="lg">Grande</option>
                                            <option value="xl">Extra grande</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            Padding vertical
                                        </label>
                                        <select
                                            value={templateData._wrap_padding_y || 'none'}
                                            onChange={e =>
                                                updateTemplateData(
                                                    '_wrap_padding_y',
                                                    e.target.value,
                                                )
                                            }
                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                        >
                                            <option value="none">Sin padding</option>
                                            <option value="sm">Pequeño</option>
                                            <option value="md">Mediano</option>
                                            <option value="lg">Grande</option>
                                            <option value="xl">Extra grande</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {templateKey === 'rich_text' && (
                    <div>
                        <label className="mb-2 block text-sm font-medium">Content *</label>
                        <RichTextEditor
                            value={data.content || ''}
                            onChange={(html: string) => setData('content', html)}
                        />
                    </div>
                )}

                {templateKey === 'article_sections' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between gap-3">
                            <div>
                                <h4 className="text-sm font-semibold text-primary">
                                    Secciones del artículo
                                </h4>
                                <p className="text-xs text-text-secondary">
                                    Agrega secciones de texto, imagen, video, cita o separador. Se
                                    renderizan en orden.
                                </p>
                            </div>
                        </div>

                        {/* Section list */}
                        <div className="space-y-3">
                            {articleSections.map((section: any, si: number) => {
                                const isOpen = !!expandedSectionIndices[si];
                                const typeLabels: Record<string, string> = {
                                    text: 'Texto',
                                    image: 'Imagen',
                                    video: 'Video',
                                    quote: 'Cita',
                                    callout: 'Llamada',
                                    separator: 'Separador',
                                    gallery: 'Galería',
                                    embed: 'Embed',
                                    card: 'Card',
                                };
                                const typeLabel = typeLabels[section.type] ?? section.type;
                                const summary =
                                    section.type === 'text'
                                        ? section.content
                                            ? section.content.replace(/<[^>]+>/g, '').slice(0, 60)
                                            : '(vacío)'
                                        : section.type === 'image'
                                          ? section.caption || 'Imagen'
                                          : section.type === 'video'
                                            ? section.url || 'URL de video'
                                            : section.type === 'quote'
                                              ? section.text?.slice(0, 60) || '(vacío)'
                                              : section.type === 'callout'
                                                ? section.title ||
                                                  section.body?.slice(0, 40) ||
                                                  '(vacío)'
                                                : section.type === 'card'
                                                  ? section.title ||
                                                    (Array.isArray(section.paragraphs) &&
                                                        section.paragraphs[0]?.text?.slice(
                                                            0,
                                                            50,
                                                        )) ||
                                                    section.card_variant ||
                                                    '(vacío)'
                                                  : '—';

                                return (
                                    <div
                                        key={si}
                                        className="rounded-lg border border-border bg-white shadow-sm"
                                    >
                                        {/* Header */}
                                        <div className="flex items-center gap-2 px-3 py-2.5">
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    setExpandedSectionIndices(prev => ({
                                                        ...prev,
                                                        [si]: !prev[si],
                                                    }))
                                                }
                                                className="flex flex-1 items-center gap-2 text-left"
                                            >
                                                <svg
                                                    viewBox="0 0 16 16"
                                                    fill="currentColor"
                                                    className={`h-3.5 w-3.5 shrink-0 text-text-secondary transition-transform ${isOpen ? 'rotate-90' : ''}`}
                                                >
                                                    <path
                                                        d="M6 4l4 4-4 4"
                                                        stroke="currentColor"
                                                        strokeWidth="1.5"
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        fill="none"
                                                    />
                                                </svg>
                                                <span className="rounded bg-primary/10 px-1.5 py-0.5 text-xs font-bold text-primary">
                                                    {typeLabel}
                                                </span>
                                                <span className="truncate text-xs text-text-secondary">
                                                    {summary}
                                                </span>
                                            </button>
                                            <div className="flex shrink-0 items-center gap-1">
                                                <button
                                                    type="button"
                                                    onClick={() => moveArticleSection(si, 'up')}
                                                    disabled={si === 0}
                                                    className="rounded border border-border px-1.5 py-0.5 text-xs text-text-secondary hover:border-primary disabled:opacity-30"
                                                    title="Subir"
                                                >
                                                    ↑
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => moveArticleSection(si, 'down')}
                                                    disabled={si === articleSections.length - 1}
                                                    className="rounded border border-border px-1.5 py-0.5 text-xs text-text-secondary hover:border-primary disabled:opacity-30"
                                                    title="Bajar"
                                                >
                                                    ↓
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => removeArticleSection(si)}
                                                    className="rounded border border-border px-1.5 py-0.5 text-xs text-red-500 hover:border-red-400 disabled:opacity-30"
                                                    disabled={articleSections.length === 1}
                                                    title="Eliminar"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        </div>

                                        {/* Fields */}
                                        {isOpen && (
                                            <div className="space-y-4 border-t border-border p-4">
                                                {/* Type selector */}
                                                <div>
                                                    <label className="mb-1 block text-xs font-semibold text-text-secondary uppercase tracking-wide">
                                                        Tipo de sección
                                                    </label>
                                                    <select
                                                        value={section.type}
                                                        onChange={e =>
                                                            updateArticleSection(
                                                                si,
                                                                'type',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                    >
                                                        <option value="text">
                                                            Texto enriquecido
                                                        </option>
                                                        <option value="image">Imagen</option>
                                                        <option value="video">
                                                            Video (YouTube)
                                                        </option>
                                                        <option value="quote">
                                                            Cita destacada
                                                        </option>
                                                        <option value="callout">
                                                            Llamada / Aviso
                                                        </option>
                                                        <option value="gallery">
                                                            Galería de imágenes
                                                        </option>
                                                        <option value="embed">
                                                            Embed / Iframe
                                                        </option>
                                                        <option value="separator">Separador</option>
                                                        <option value="card">
                                                            Card de contenido
                                                        </option>
                                                    </select>
                                                </div>

                                                {/* Text */}
                                                {section.type === 'text' && (
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Contenido
                                                        </label>
                                                        <RichTextEditor
                                                            value={section.content || ''}
                                                            onChange={(html: string) =>
                                                                updateArticleSection(
                                                                    si,
                                                                    'content',
                                                                    html,
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                )}

                                                {/* Image */}
                                                {section.type === 'image' && (
                                                    <div className="space-y-3">
                                                        <MediaPicker
                                                            label="Seleccionar imagen"
                                                            singleSelect
                                                            allowUpload={false}
                                                            value={mediaToSelected(section.image)}
                                                            onChange={value =>
                                                                updateArticleSection(
                                                                    si,
                                                                    'image',
                                                                    value?.[0] ?? null,
                                                                )
                                                            }
                                                        />
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Pie de foto
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={section.caption || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'caption',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="(opcional)"
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                        <div className="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Ancho
                                                                </label>
                                                                <select
                                                                    value={section.width || 'full'}
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'width',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                >
                                                                    <option value="full">
                                                                        Completo
                                                                    </option>
                                                                    <option value="wide">
                                                                        Ancho (900 px)
                                                                    </option>
                                                                    <option value="medium">
                                                                        Mediano (600 px)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Alineación
                                                                </label>
                                                                <select
                                                                    value={
                                                                        section.align || 'center'
                                                                    }
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'align',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                >
                                                                    <option value="center">
                                                                        Centro
                                                                    </option>
                                                                    <option value="left">
                                                                        Izquierda
                                                                    </option>
                                                                    <option value="right">
                                                                        Derecha
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Video */}
                                                {section.type === 'video' && (
                                                    <div className="space-y-3">
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                URL de YouTube / Vimeo
                                                            </label>
                                                            <input
                                                                type="url"
                                                                value={section.url || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'url',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="https://www.youtube.com/watch?v=..."
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Pie de video
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={section.caption || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'caption',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="(opcional)"
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Quote */}
                                                {section.type === 'quote' && (
                                                    <div className="space-y-3">
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Cita *
                                                            </label>
                                                            <textarea
                                                                value={section.text || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'text',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                rows={3}
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                placeholder="Texto de la cita…"
                                                            />
                                                        </div>
                                                        <div className="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Nombre del autor
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    value={
                                                                        section.author_name || ''
                                                                    }
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'author_name',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    placeholder="(opcional)"
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                />
                                                            </div>
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Cargo / título
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    value={
                                                                        section.author_title || ''
                                                                    }
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'author_title',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    placeholder="(opcional)"
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Callout */}
                                                {section.type === 'callout' && (
                                                    <div className="space-y-3">
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Tipo
                                                            </label>
                                                            <select
                                                                value={
                                                                    section.callout_type || 'info'
                                                                }
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'callout_type',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            >
                                                                <option value="info">
                                                                    ℹ️ Información
                                                                </option>
                                                                <option value="warning">
                                                                    ⚠️ Advertencia
                                                                </option>
                                                                <option value="success">
                                                                    ✅ Éxito / Tip
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Título
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={section.title || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'title',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="(opcional)"
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Cuerpo
                                                            </label>
                                                            <textarea
                                                                value={section.body || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'body',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                rows={3}
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Separator */}
                                                {section.type === 'separator' && (
                                                    <div>
                                                        <label className="mb-1 block text-sm font-medium">
                                                            Estilo
                                                        </label>
                                                        <select
                                                            value={section.style || 'line'}
                                                            onChange={e =>
                                                                updateArticleSection(
                                                                    si,
                                                                    'style',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                        >
                                                            <option value="line">
                                                                Línea horizontal
                                                            </option>
                                                            <option value="dots">
                                                                Puntos decorativos
                                                            </option>
                                                            <option value="space">
                                                                Espacio en blanco
                                                            </option>
                                                        </select>
                                                    </div>
                                                )}

                                                {/* Gallery */}
                                                {section.type === 'gallery' && (
                                                    <div className="space-y-3">
                                                        <MediaPicker
                                                            label="Seleccionar imágenes"
                                                            singleSelect={false}
                                                            allowUpload={false}
                                                            value={mediaToSelected(section.images)}
                                                            onChange={value =>
                                                                updateArticleSection(
                                                                    si,
                                                                    'images',
                                                                    value || [],
                                                                )
                                                            }
                                                        />

                                                        <div className="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Columnas
                                                                </label>
                                                                <select
                                                                    value={section.cols || '3'}
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'cols',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                >
                                                                    <option value="2">
                                                                        2 columnas
                                                                    </option>
                                                                    <option value="3">
                                                                        3 columnas
                                                                    </option>
                                                                    <option value="4">
                                                                        4 columnas
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Proporción
                                                                </label>
                                                                <select
                                                                    value={
                                                                        section.aspect || 'photo'
                                                                    }
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'aspect',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                >
                                                                    <option value="photo">
                                                                        Fotográfica (4/3)
                                                                    </option>
                                                                    <option value="square">
                                                                        Cuadrada (1/1)
                                                                    </option>
                                                                    <option value="video">
                                                                        Video (16/9)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Anchura
                                                            </label>
                                                            <select
                                                                value={section.width || 'content'}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'width',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            >
                                                                <option value="content">
                                                                    Contenido (central)
                                                                </option>
                                                                <option value="full">
                                                                    Pantalla completa (bleed)
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Embed */}
                                                {section.type === 'embed' && (
                                                    <div className="space-y-3">
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                URL o iframe
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={section.url || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'url',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="https://www.youtube.com/watch?v=..."
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                HTML (opcional)
                                                                {/* Block styles */}
                                                                <div className="pt-3 border-t border-border">
                                                                    <h5 className="mb-2 text-sm font-semibold">
                                                                        Estilos del bloque
                                                                    </h5>
                                                                    <div className="space-y-3">
                                                                        <ColorPickerField
                                                                            label="Fondo"
                                                                            value={
                                                                                section.block_bg ||
                                                                                ''
                                                                            }
                                                                            onChange={v =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'block_bg',
                                                                                    v,
                                                                                )
                                                                            }
                                                                        />

                                                                        <div className="grid gap-3 md:grid-cols-3">
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Borde
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.block_border ||
                                                                                        'none'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'block_border',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="none">
                                                                                        Sin borde
                                                                                    </option>
                                                                                    <option value="solid">
                                                                                        Sólido
                                                                                    </option>
                                                                                    <option value="dashed">
                                                                                        Punteado
                                                                                    </option>
                                                                                    <option value="dotted">
                                                                                        Dotted
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            {section.block_border &&
                                                                                section.block_border !==
                                                                                    'none' && (
                                                                                    <div>
                                                                                        <label className="mb-1 block text-sm font-medium">
                                                                                            Grosor
                                                                                        </label>
                                                                                        <select
                                                                                            value={
                                                                                                section.block_border_width ||
                                                                                                '1'
                                                                                            }
                                                                                            onChange={e =>
                                                                                                updateArticleSection(
                                                                                                    si,
                                                                                                    'block_border_width',
                                                                                                    e
                                                                                                        .target
                                                                                                        .value,
                                                                                                )
                                                                                            }
                                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                        >
                                                                                            <option value="1">
                                                                                                1px
                                                                                            </option>
                                                                                            <option value="2">
                                                                                                2px
                                                                                            </option>
                                                                                            <option value="4">
                                                                                                4px
                                                                                            </option>
                                                                                            <option value="8">
                                                                                                8px
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                )}
                                                                            {section.block_border &&
                                                                                section.block_border !==
                                                                                    'none' && (
                                                                                    <ColorPickerField
                                                                                        label="Color del borde"
                                                                                        value={
                                                                                            section.block_border_color ||
                                                                                            '#e5e7eb'
                                                                                        }
                                                                                        onChange={v =>
                                                                                            updateArticleSection(
                                                                                                si,
                                                                                                'block_border_color',
                                                                                                v,
                                                                                            )
                                                                                        }
                                                                                    />
                                                                                )}
                                                                        </div>

                                                                        <div className="grid gap-3 md:grid-cols-2">
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Redondeo
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.block_radius ||
                                                                                        'none'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'block_radius',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="none">
                                                                                        Sin redondeo
                                                                                    </option>
                                                                                    <option value="sm">
                                                                                        4px
                                                                                    </option>
                                                                                    <option value="md">
                                                                                        8px
                                                                                    </option>
                                                                                    <option value="lg">
                                                                                        12px
                                                                                    </option>
                                                                                    <option value="xl">
                                                                                        16px
                                                                                    </option>
                                                                                    <option value="2xl">
                                                                                        24px
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Sombra
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.block_shadow ||
                                                                                        'none'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'block_shadow',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="none">
                                                                                        Sin sombra
                                                                                    </option>
                                                                                    <option value="sm">
                                                                                        Suave
                                                                                    </option>
                                                                                    <option value="md">
                                                                                        Mediana
                                                                                    </option>
                                                                                    <option value="lg">
                                                                                        Grande
                                                                                    </option>
                                                                                    <option value="xl">
                                                                                        Muy grande
                                                                                    </option>
                                                                                    <option value="2xl">
                                                                                        Extra grande
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div>
                                                                            <label className="mb-1 block text-sm font-medium">
                                                                                Padding
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.block_padding ||
                                                                                    'none'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'block_padding',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                            >
                                                                                <option value="none">
                                                                                    Sin padding
                                                                                </option>
                                                                                <option value="sm">
                                                                                    Pequeño
                                                                                </option>
                                                                                <option value="md">
                                                                                    Mediano
                                                                                </option>
                                                                                <option value="lg">
                                                                                    Grande
                                                                                </option>
                                                                                <option value="xl">
                                                                                    Extra grande
                                                                                </option>
                                                                            </select>
                                                                        </div>

                                                                        <div className="grid gap-3 md:grid-cols-2">
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Alineación de
                                                                                    texto
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.text_align ||
                                                                                        'left'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'text_align',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="left">
                                                                                        Izquierda
                                                                                    </option>
                                                                                    <option value="center">
                                                                                        Centrada
                                                                                    </option>
                                                                                    <option value="right">
                                                                                        Derecha
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Tamaño de texto
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.text_size ||
                                                                                        'normal'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'text_size',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="normal">
                                                                                        Normal
                                                                                    </option>
                                                                                    <option value="lg">
                                                                                        Grande
                                                                                    </option>
                                                                                    <option value="xl">
                                                                                        Muy grande
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <textarea
                                                                value={section.html || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'html',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                rows={3}
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                placeholder="Pega un iframe o HTML embebido"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label className="mb-1 block text-sm font-medium">
                                                                Pie
                                                            </label>
                                                            <input
                                                                type="text"
                                                                value={section.caption || ''}
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'caption',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                placeholder="(opcional)"
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            />
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Card */}
                                                {section.type === 'card' && (
                                                    <div className="space-y-4">
                                                        {/* Variant */}
                                                        <div>
                                                            <label className="mb-1 block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                Variante de card
                                                            </label>
                                                            <select
                                                                value={
                                                                    section.card_variant ||
                                                                    'content'
                                                                }
                                                                onChange={e =>
                                                                    updateArticleSection(
                                                                        si,
                                                                        'card_variant',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                            >
                                                                <option value="content">
                                                                    Contenido (título + párrafos)
                                                                </option>
                                                                <option value="quote">
                                                                    Cita destacada
                                                                </option>
                                                                <option value="image_text">
                                                                    Imagen + texto
                                                                </option>
                                                                <option value="gallery_card">
                                                                    Galería de imágenes
                                                                </option>
                                                                <option value="text_only">
                                                                    Solo texto
                                                                </option>
                                                                <option value="bg_card">
                                                                    Fondo con imagen
                                                                </option>
                                                            </select>
                                                        </div>

                                                        {/* Quote fields */}
                                                        {section.card_variant === 'quote' && (
                                                            <div className="space-y-3">
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Texto de cita *
                                                                    </label>
                                                                    <textarea
                                                                        value={
                                                                            section.quote_text || ''
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'quote_text',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        rows={3}
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        placeholder="El texto de la cita..."
                                                                    />
                                                                </div>
                                                                <div className="grid gap-3 md:grid-cols-2">
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Autor
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            value={
                                                                                section.quote_author ||
                                                                                ''
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'quote_author',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            placeholder="(opcional)"
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        />
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Cargo / Organización
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            value={
                                                                                section.quote_org ||
                                                                                ''
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'quote_org',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            placeholder="(opcional)"
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div className="grid gap-3 md:grid-cols-2">
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Estilo de acento
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.quote_style ||
                                                                                'border_left'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'quote_style',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="border_left">
                                                                                Línea izquierda
                                                                            </option>
                                                                            <option value="quotes">
                                                                                Comillas decorativas
                                                                            </option>
                                                                            <option value="minimal">
                                                                                Minimal
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Tamaño de texto
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.quote_size ||
                                                                                'xl'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'quote_size',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="base">
                                                                                Base
                                                                            </option>
                                                                            <option value="lg">
                                                                                Grande
                                                                            </option>
                                                                            <option value="xl">
                                                                                XL
                                                                            </option>
                                                                            <option value="2xl">
                                                                                2XL
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div className="grid gap-3 md:grid-cols-3">
                                                                    <ColorPickerField
                                                                        label="Color del texto"
                                                                        value={
                                                                            section.quote_text_color ||
                                                                            ''
                                                                        }
                                                                        onChange={v =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'quote_text_color',
                                                                                v,
                                                                            )
                                                                        }
                                                                    />
                                                                    <ColorPickerField
                                                                        label="Color del autor"
                                                                        value={
                                                                            section.quote_author_color ||
                                                                            ''
                                                                        }
                                                                        onChange={v =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'quote_author_color',
                                                                                v,
                                                                            )
                                                                        }
                                                                    />
                                                                    <ColorPickerField
                                                                        label="Color de acento"
                                                                        value={
                                                                            section.quote_accent_color ||
                                                                            '#f59e0b'
                                                                        }
                                                                        onChange={v =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'quote_accent_color',
                                                                                v,
                                                                            )
                                                                        }
                                                                    />
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Title (non-quote) */}
                                                        {(section.card_variant || 'content') !==
                                                            'quote' && (
                                                            <div className="rounded-lg border border-border bg-gray-50 p-3">
                                                                <label className="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                    Título (opcional)
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    value={section.title || ''}
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'title',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    placeholder="Título de la card"
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                />
                                                                {section.title && (
                                                                    <div className="mt-2 grid gap-2 md:grid-cols-3">
                                                                        <ColorPickerField
                                                                            label="Color"
                                                                            value={
                                                                                section.title_color ||
                                                                                ''
                                                                            }
                                                                            onChange={v =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'title_color',
                                                                                    v,
                                                                                )
                                                                            }
                                                                        />
                                                                        <div>
                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                Tamaño
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.title_size ||
                                                                                    'xl'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'title_size',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-2 py-1.5 text-xs"
                                                                            >
                                                                                <option value="sm">
                                                                                    Pequeño
                                                                                </option>
                                                                                <option value="base">
                                                                                    Base
                                                                                </option>
                                                                                <option value="lg">
                                                                                    Grande
                                                                                </option>
                                                                                <option value="xl">
                                                                                    XL
                                                                                </option>
                                                                                <option value="2xl">
                                                                                    2XL
                                                                                </option>
                                                                                <option value="3xl">
                                                                                    3XL
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div>
                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                Peso
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.title_weight ||
                                                                                    'bold'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'title_weight',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-2 py-1.5 text-xs"
                                                                            >
                                                                                <option value="normal">
                                                                                    Normal
                                                                                </option>
                                                                                <option value="medium">
                                                                                    Medium
                                                                                </option>
                                                                                <option value="semibold">
                                                                                    Semibold
                                                                                </option>
                                                                                <option value="bold">
                                                                                    Bold
                                                                                </option>
                                                                                <option value="extrabold">
                                                                                    Extrabold
                                                                                </option>
                                                                                <option value="black">
                                                                                    Black (900)
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div>
                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                Interlineado
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.title_line_height ||
                                                                                    'tight'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'title_line_height',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-2 py-1.5 text-xs"
                                                                            >
                                                                                <option value="tight">
                                                                                    Compacto
                                                                                </option>
                                                                                <option value="snug">
                                                                                    Ajustado
                                                                                </option>
                                                                                <option value="normal">
                                                                                    Normal
                                                                                </option>
                                                                                <option value="relaxed">
                                                                                    Relajado
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div className="flex items-center gap-2 pt-1">
                                                                            <input
                                                                                type="checkbox"
                                                                                id={`title-italic-${si}`}
                                                                                checked={
                                                                                    !!section.title_italic
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'title_italic',
                                                                                        e.target
                                                                                            .checked,
                                                                                    )
                                                                                }
                                                                                className="rounded"
                                                                            />
                                                                            <label
                                                                                htmlFor={`title-italic-${si}`}
                                                                                className="text-xs"
                                                                            >
                                                                                Cursiva
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        )}

                                                        {/* Subtitle */}
                                                        {['content', 'image_text'].includes(
                                                            section.card_variant || 'content',
                                                        ) && (
                                                            <div className="rounded-lg border border-border bg-gray-50 p-3">
                                                                <label className="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                    Subtítulo (opcional)
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    value={section.subtitle || ''}
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'subtitle',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    placeholder="Subtítulo de la card"
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                />
                                                                {section.subtitle && (
                                                                    <div className="mt-2 grid gap-2 md:grid-cols-3">
                                                                        <ColorPickerField
                                                                            label="Color"
                                                                            value={
                                                                                section.subtitle_color ||
                                                                                ''
                                                                            }
                                                                            onChange={v =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'subtitle_color',
                                                                                    v,
                                                                                )
                                                                            }
                                                                        />
                                                                        <div>
                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                Tamaño
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.subtitle_size ||
                                                                                    'sm'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'subtitle_size',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-2 py-1.5 text-xs"
                                                                            >
                                                                                <option value="sm">
                                                                                    Pequeño
                                                                                </option>
                                                                                <option value="base">
                                                                                    Base
                                                                                </option>
                                                                                <option value="lg">
                                                                                    Grande
                                                                                </option>
                                                                                <option value="xl">
                                                                                    XL
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div>
                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                Peso
                                                                            </label>
                                                                            <select
                                                                                value={
                                                                                    section.subtitle_weight ||
                                                                                    'medium'
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'subtitle_weight',
                                                                                        e.target
                                                                                            .value,
                                                                                    )
                                                                                }
                                                                                className="w-full rounded border border-border bg-white px-2 py-1.5 text-xs"
                                                                            >
                                                                                <option value="normal">
                                                                                    Normal
                                                                                </option>
                                                                                <option value="medium">
                                                                                    Medium
                                                                                </option>
                                                                                <option value="semibold">
                                                                                    Semibold
                                                                                </option>
                                                                                <option value="bold">
                                                                                    Bold
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div className="flex items-center gap-2 pt-1">
                                                                            <input
                                                                                type="checkbox"
                                                                                id={`subtitle-italic-${si}`}
                                                                                checked={
                                                                                    !!section.subtitle_italic
                                                                                }
                                                                                onChange={e =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'subtitle_italic',
                                                                                        e.target
                                                                                            .checked,
                                                                                    )
                                                                                }
                                                                                className="rounded"
                                                                            />
                                                                            <label
                                                                                htmlFor={`subtitle-italic-${si}`}
                                                                                className="text-xs"
                                                                            >
                                                                                Cursiva
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        )}

                                                        {/* Paragraphs */}
                                                        {(section.card_variant || 'content') !==
                                                            'quote' &&
                                                            (section.card_variant || 'content') !==
                                                                'gallery_card' && (
                                                                <div className="rounded-lg border border-border bg-gray-50 p-3">
                                                                    <div className="mb-2 flex items-center justify-between">
                                                                        <label className="block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                            Párrafos
                                                                        </label>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() => {
                                                                                const paras =
                                                                                    Array.isArray(
                                                                                        section.paragraphs,
                                                                                    )
                                                                                        ? [
                                                                                              ...section.paragraphs,
                                                                                          ]
                                                                                        : [];
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'paragraphs',
                                                                                    [
                                                                                        ...paras,
                                                                                        {
                                                                                            text: '',
                                                                                            color: '',
                                                                                            size: 'base',
                                                                                            weight: 'normal',
                                                                                            italic: false,
                                                                                            line_height:
                                                                                                'relaxed',
                                                                                        },
                                                                                    ],
                                                                                );
                                                                            }}
                                                                            className="rounded border border-border bg-white px-2 py-1 text-xs font-semibold text-primary hover:border-primary"
                                                                        >
                                                                            + Párrafo
                                                                        </button>
                                                                    </div>
                                                                    <div className="space-y-3">
                                                                        {(Array.isArray(
                                                                            section.paragraphs,
                                                                        )
                                                                            ? section.paragraphs
                                                                            : []
                                                                        ).map(
                                                                            (
                                                                                para: any,
                                                                                pi: number,
                                                                            ) => (
                                                                                <div
                                                                                    key={pi}
                                                                                    className="space-y-2 rounded border border-border bg-white p-2"
                                                                                >
                                                                                    <div className="flex items-center justify-between">
                                                                                        <span className="text-xs font-medium text-text-secondary">
                                                                                            Párrafo{' '}
                                                                                            {pi + 1}
                                                                                        </span>
                                                                                        <button
                                                                                            type="button"
                                                                                            onClick={() => {
                                                                                                const paras =
                                                                                                    [
                                                                                                        ...(section.paragraphs ||
                                                                                                            []),
                                                                                                    ];
                                                                                                paras.splice(
                                                                                                    pi,
                                                                                                    1,
                                                                                                );
                                                                                                updateArticleSection(
                                                                                                    si,
                                                                                                    'paragraphs',
                                                                                                    paras,
                                                                                                );
                                                                                            }}
                                                                                            className="text-xs text-red-400 hover:text-red-600"
                                                                                        >
                                                                                            ✕
                                                                                        </button>
                                                                                    </div>
                                                                                    <textarea
                                                                                        value={
                                                                                            para.text ||
                                                                                            ''
                                                                                        }
                                                                                        onChange={e => {
                                                                                            const paras =
                                                                                                [
                                                                                                    ...(section.paragraphs ||
                                                                                                        []),
                                                                                                ];
                                                                                            paras[
                                                                                                pi
                                                                                            ] = {
                                                                                                ...paras[
                                                                                                    pi
                                                                                                ],
                                                                                                text: e
                                                                                                    .target
                                                                                                    .value,
                                                                                            };
                                                                                            updateArticleSection(
                                                                                                si,
                                                                                                'paragraphs',
                                                                                                paras,
                                                                                            );
                                                                                        }}
                                                                                        rows={2}
                                                                                        placeholder="Texto del párrafo..."
                                                                                        className="w-full rounded border border-border px-2 py-1.5 text-sm"
                                                                                    />
                                                                                    <div className="grid gap-2 md:grid-cols-4">
                                                                                        <ColorPickerField
                                                                                            label="Color"
                                                                                            value={
                                                                                                para.color ||
                                                                                                ''
                                                                                            }
                                                                                            onChange={v => {
                                                                                                const paras =
                                                                                                    [
                                                                                                        ...(section.paragraphs ||
                                                                                                            []),
                                                                                                    ];
                                                                                                paras[
                                                                                                    pi
                                                                                                ] =
                                                                                                    {
                                                                                                        ...paras[
                                                                                                            pi
                                                                                                        ],
                                                                                                        color: v,
                                                                                                    };
                                                                                                updateArticleSection(
                                                                                                    si,
                                                                                                    'paragraphs',
                                                                                                    paras,
                                                                                                );
                                                                                            }}
                                                                                        />
                                                                                        <div>
                                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                                Tamaño
                                                                                            </label>
                                                                                            <select
                                                                                                value={
                                                                                                    para.size ||
                                                                                                    'base'
                                                                                                }
                                                                                                onChange={e => {
                                                                                                    const paras =
                                                                                                        [
                                                                                                            ...(section.paragraphs ||
                                                                                                                []),
                                                                                                        ];
                                                                                                    paras[
                                                                                                        pi
                                                                                                    ] =
                                                                                                        {
                                                                                                            ...paras[
                                                                                                                pi
                                                                                                            ],
                                                                                                            size: e
                                                                                                                .target
                                                                                                                .value,
                                                                                                        };
                                                                                                    updateArticleSection(
                                                                                                        si,
                                                                                                        'paragraphs',
                                                                                                        paras,
                                                                                                    );
                                                                                                }}
                                                                                                className="w-full rounded border border-border bg-white px-2 py-1 text-xs"
                                                                                            >
                                                                                                <option value="sm">
                                                                                                    Pequeño
                                                                                                </option>
                                                                                                <option value="base">
                                                                                                    Base
                                                                                                </option>
                                                                                                <option value="lg">
                                                                                                    Grande
                                                                                                </option>
                                                                                                <option value="xl">
                                                                                                    XL
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                                Peso
                                                                                            </label>
                                                                                            <select
                                                                                                value={
                                                                                                    para.weight ||
                                                                                                    'normal'
                                                                                                }
                                                                                                onChange={e => {
                                                                                                    const paras =
                                                                                                        [
                                                                                                            ...(section.paragraphs ||
                                                                                                                []),
                                                                                                        ];
                                                                                                    paras[
                                                                                                        pi
                                                                                                    ] =
                                                                                                        {
                                                                                                            ...paras[
                                                                                                                pi
                                                                                                            ],
                                                                                                            weight: e
                                                                                                                .target
                                                                                                                .value,
                                                                                                        };
                                                                                                    updateArticleSection(
                                                                                                        si,
                                                                                                        'paragraphs',
                                                                                                        paras,
                                                                                                    );
                                                                                                }}
                                                                                                className="w-full rounded border border-border bg-white px-2 py-1 text-xs"
                                                                                            >
                                                                                                <option value="normal">
                                                                                                    Normal
                                                                                                </option>
                                                                                                <option value="medium">
                                                                                                    Medium
                                                                                                </option>
                                                                                                <option value="semibold">
                                                                                                    Semibold
                                                                                                </option>
                                                                                                <option value="bold">
                                                                                                    Bold
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label className="mb-1 block text-xs font-medium">
                                                                                                Interlineado
                                                                                            </label>
                                                                                            <select
                                                                                                value={
                                                                                                    para.line_height ||
                                                                                                    'relaxed'
                                                                                                }
                                                                                                onChange={e => {
                                                                                                    const paras =
                                                                                                        [
                                                                                                            ...(section.paragraphs ||
                                                                                                                []),
                                                                                                        ];
                                                                                                    paras[
                                                                                                        pi
                                                                                                    ] =
                                                                                                        {
                                                                                                            ...paras[
                                                                                                                pi
                                                                                                            ],
                                                                                                            line_height:
                                                                                                                e
                                                                                                                    .target
                                                                                                                    .value,
                                                                                                        };
                                                                                                    updateArticleSection(
                                                                                                        si,
                                                                                                        'paragraphs',
                                                                                                        paras,
                                                                                                    );
                                                                                                }}
                                                                                                className="w-full rounded border border-border bg-white px-2 py-1 text-xs"
                                                                                            >
                                                                                                <option value="tight">
                                                                                                    Compacto
                                                                                                </option>
                                                                                                <option value="normal">
                                                                                                    Normal
                                                                                                </option>
                                                                                                <option value="relaxed">
                                                                                                    Relajado
                                                                                                </option>
                                                                                                <option value="loose">
                                                                                                    Suelto
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div className="flex items-center gap-2">
                                                                                        <input
                                                                                            type="checkbox"
                                                                                            id={`para-italic-${si}-${pi}`}
                                                                                            checked={
                                                                                                !!para.italic
                                                                                            }
                                                                                            onChange={e => {
                                                                                                const paras =
                                                                                                    [
                                                                                                        ...(section.paragraphs ||
                                                                                                            []),
                                                                                                    ];
                                                                                                paras[
                                                                                                    pi
                                                                                                ] =
                                                                                                    {
                                                                                                        ...paras[
                                                                                                            pi
                                                                                                        ],
                                                                                                        italic: e
                                                                                                            .target
                                                                                                            .checked,
                                                                                                    };
                                                                                                updateArticleSection(
                                                                                                    si,
                                                                                                    'paragraphs',
                                                                                                    paras,
                                                                                                );
                                                                                            }}
                                                                                            className="rounded"
                                                                                        />
                                                                                        <label
                                                                                            htmlFor={`para-italic-${si}-${pi}`}
                                                                                            className="text-xs"
                                                                                        >
                                                                                            Cursiva
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            ),
                                                                        )}
                                                                        {(!Array.isArray(
                                                                            section.paragraphs,
                                                                        ) ||
                                                                            section.paragraphs
                                                                                .length === 0) && (
                                                                            <p className="text-xs italic text-text-secondary">
                                                                                Sin párrafos. Usa el
                                                                                botón "+ Párrafo"
                                                                                para agregar.
                                                                            </p>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            )}

                                                        {/* Image (image_text) */}
                                                        {section.card_variant === 'image_text' && (
                                                            <div className="space-y-3 rounded-lg border border-border bg-gray-50 p-3">
                                                                <label className="block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                    Imagen
                                                                </label>
                                                                <MediaPicker
                                                                    label="Seleccionar imagen"
                                                                    singleSelect
                                                                    allowUpload={false}
                                                                    value={mediaToSelected(
                                                                        section.image,
                                                                    )}
                                                                    onChange={value =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'image',
                                                                            value?.[0] ?? null,
                                                                        )
                                                                    }
                                                                />
                                                                <div className="grid gap-3 md:grid-cols-2">
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Posición de imagen
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.image_position ||
                                                                                'right'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'image_position',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="left">
                                                                                Izquierda
                                                                            </option>
                                                                            <option value="right">
                                                                                Derecha
                                                                            </option>
                                                                            <option value="top">
                                                                                Arriba
                                                                            </option>
                                                                            <option value="bottom">
                                                                                Abajo
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Ancho de imagen
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.image_width ||
                                                                                '40%'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'image_width',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="30%">
                                                                                30%
                                                                            </option>
                                                                            <option value="40%">
                                                                                40%
                                                                            </option>
                                                                            <option value="50%">
                                                                                50%
                                                                            </option>
                                                                            <option value="60%">
                                                                                60%
                                                                            </option>
                                                                            <option value="100%">
                                                                                100%
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Proporción
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.image_aspect ||
                                                                                'photo'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'image_aspect',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="photo">
                                                                                Fotográfica (4/3)
                                                                            </option>
                                                                            <option value="square">
                                                                                Cuadrada (1/1)
                                                                            </option>
                                                                            <option value="video">
                                                                                Video (16/9)
                                                                            </option>
                                                                            <option value="auto">
                                                                                Auto
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Redondeo de imagen
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.image_radius ||
                                                                                'md'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'image_radius',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="none">
                                                                                Sin redondeo
                                                                            </option>
                                                                            <option value="sm">
                                                                                Suave (4px)
                                                                            </option>
                                                                            <option value="md">
                                                                                Mediano (8px)
                                                                            </option>
                                                                            <option value="lg">
                                                                                Grande (12px)
                                                                            </option>
                                                                            <option value="xl">
                                                                                Extra grande (16px)
                                                                            </option>
                                                                            <option value="full">
                                                                                Circular
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Gallery images (gallery_card) */}
                                                        {section.card_variant ===
                                                            'gallery_card' && (
                                                            <div className="space-y-3 rounded-lg border border-border bg-gray-50 p-3">
                                                                <label className="block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                    Imágenes
                                                                </label>
                                                                <MediaPicker
                                                                    label="Seleccionar imágenes"
                                                                    singleSelect={false}
                                                                    allowUpload={false}
                                                                    value={mediaToSelected(
                                                                        section.gallery_images,
                                                                    )}
                                                                    onChange={value =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'gallery_images',
                                                                            value || [],
                                                                        )
                                                                    }
                                                                />
                                                                <div className="grid gap-3 md:grid-cols-2">
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Columnas
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.gallery_cols ||
                                                                                '2'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'gallery_cols',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="2">
                                                                                2 columnas
                                                                            </option>
                                                                            <option value="3">
                                                                                3 columnas
                                                                            </option>
                                                                            <option value="4">
                                                                                4 columnas
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div>
                                                                        <label className="mb-1 block text-sm font-medium">
                                                                            Proporción
                                                                        </label>
                                                                        <select
                                                                            value={
                                                                                section.gallery_aspect ||
                                                                                'photo'
                                                                            }
                                                                            onChange={e =>
                                                                                updateArticleSection(
                                                                                    si,
                                                                                    'gallery_aspect',
                                                                                    e.target.value,
                                                                                )
                                                                            }
                                                                            className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                        >
                                                                            <option value="photo">
                                                                                Fotográfica (4/3)
                                                                            </option>
                                                                            <option value="square">
                                                                                Cuadrada (1/1)
                                                                            </option>
                                                                            <option value="video">
                                                                                Video (16/9)
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* BG image (bg_card) */}
                                                        {section.card_variant === 'bg_card' && (
                                                            <div className="space-y-3 rounded-lg border border-border bg-gray-50 p-3">
                                                                <label className="block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                    Imagen de fondo
                                                                </label>
                                                                <MediaPicker
                                                                    label="Seleccionar imagen de fondo"
                                                                    singleSelect
                                                                    allowUpload={false}
                                                                    value={mediaToSelected(
                                                                        section.bg_image,
                                                                    )}
                                                                    onChange={value =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'bg_image',
                                                                            value?.[0] ?? null,
                                                                        )
                                                                    }
                                                                />
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Opacidad del overlay (
                                                                        {section.bg_overlay ?? 50}%)
                                                                    </label>
                                                                    <input
                                                                        type="range"
                                                                        min="0"
                                                                        max="100"
                                                                        step="5"
                                                                        value={
                                                                            section.bg_overlay ?? 50
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'bg_overlay',
                                                                                Number(
                                                                                    e.target.value,
                                                                                ),
                                                                            )
                                                                        }
                                                                        className="w-full"
                                                                    />
                                                                </div>
                                                                <ColorPickerField
                                                                    label="Color del overlay"
                                                                    value={
                                                                        section.bg_overlay_color ||
                                                                        '#000000'
                                                                    }
                                                                    onChange={v =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'bg_overlay_color',
                                                                            v,
                                                                        )
                                                                    }
                                                                />
                                                            </div>
                                                        )}

                                                        {/* Card wrapper styling */}
                                                        <div className="space-y-3 rounded-lg border border-border bg-gray-50 p-3">
                                                            <label className="block text-xs font-semibold uppercase tracking-wide text-text-secondary">
                                                                Estilos de la card
                                                            </label>
                                                            <div className="grid gap-3 md:grid-cols-2">
                                                                <ColorPickerField
                                                                    label="Fondo"
                                                                    value={section.card_bg || ''}
                                                                    onChange={v =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'card_bg',
                                                                            v,
                                                                        )
                                                                    }
                                                                />
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Alineación de texto
                                                                    </label>
                                                                    <select
                                                                        value={
                                                                            section.card_align ||
                                                                            'left'
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'card_align',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                    >
                                                                        <option value="left">
                                                                            Izquierda
                                                                        </option>
                                                                        <option value="center">
                                                                            Centro
                                                                        </option>
                                                                        <option value="right">
                                                                            Derecha
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div className="grid gap-3 md:grid-cols-3">
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Borde
                                                                    </label>
                                                                    <select
                                                                        value={
                                                                            section.card_border ||
                                                                            'none'
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'card_border',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                    >
                                                                        <option value="none">
                                                                            Sin borde
                                                                        </option>
                                                                        <option value="solid">
                                                                            Sólido
                                                                        </option>
                                                                        <option value="dashed">
                                                                            Guiones
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                {section.card_border &&
                                                                    section.card_border !==
                                                                        'none' && (
                                                                        <>
                                                                            <div>
                                                                                <label className="mb-1 block text-sm font-medium">
                                                                                    Grosor
                                                                                </label>
                                                                                <select
                                                                                    value={
                                                                                        section.card_border_width ||
                                                                                        '1'
                                                                                    }
                                                                                    onChange={e =>
                                                                                        updateArticleSection(
                                                                                            si,
                                                                                            'card_border_width',
                                                                                            e.target
                                                                                                .value,
                                                                                        )
                                                                                    }
                                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                                >
                                                                                    <option value="1">
                                                                                        1px
                                                                                    </option>
                                                                                    <option value="2">
                                                                                        2px
                                                                                    </option>
                                                                                    <option value="4">
                                                                                        4px
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                            <ColorPickerField
                                                                                label="Color borde"
                                                                                value={
                                                                                    section.card_border_color ||
                                                                                    '#e5e7eb'
                                                                                }
                                                                                onChange={v =>
                                                                                    updateArticleSection(
                                                                                        si,
                                                                                        'card_border_color',
                                                                                        v,
                                                                                    )
                                                                                }
                                                                            />
                                                                        </>
                                                                    )}
                                                            </div>
                                                            <div className="grid gap-3 md:grid-cols-3">
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Redondeo
                                                                    </label>
                                                                    <select
                                                                        value={
                                                                            section.card_radius ||
                                                                            'lg'
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'card_radius',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                    >
                                                                        <option value="none">
                                                                            Sin redondeo
                                                                        </option>
                                                                        <option value="sm">
                                                                            4px
                                                                        </option>
                                                                        <option value="md">
                                                                            8px
                                                                        </option>
                                                                        <option value="lg">
                                                                            12px
                                                                        </option>
                                                                        <option value="xl">
                                                                            16px
                                                                        </option>
                                                                        <option value="2xl">
                                                                            24px
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Sombra
                                                                    </label>
                                                                    <select
                                                                        value={
                                                                            section.card_shadow ||
                                                                            'md'
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'card_shadow',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                    >
                                                                        <option value="none">
                                                                            Sin sombra
                                                                        </option>
                                                                        <option value="sm">
                                                                            Suave
                                                                        </option>
                                                                        <option value="md">
                                                                            Mediana
                                                                        </option>
                                                                        <option value="lg">
                                                                            Grande
                                                                        </option>
                                                                        <option value="xl">
                                                                            Muy grande
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label className="mb-1 block text-sm font-medium">
                                                                        Padding
                                                                    </label>
                                                                    <select
                                                                        value={
                                                                            section.card_padding ||
                                                                            'lg'
                                                                        }
                                                                        onChange={e =>
                                                                            updateArticleSection(
                                                                                si,
                                                                                'card_padding',
                                                                                e.target.value,
                                                                            )
                                                                        }
                                                                        className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                    >
                                                                        <option value="none">
                                                                            Sin padding
                                                                        </option>
                                                                        <option value="sm">
                                                                            Pequeño
                                                                        </option>
                                                                        <option value="md">
                                                                            Mediano
                                                                        </option>
                                                                        <option value="lg">
                                                                            Grande
                                                                        </option>
                                                                        <option value="xl">
                                                                            Extra grande
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label className="mb-1 block text-sm font-medium">
                                                                    Ancho de la card
                                                                </label>
                                                                <select
                                                                    value={
                                                                        section.card_width || 'full'
                                                                    }
                                                                    onChange={e =>
                                                                        updateArticleSection(
                                                                            si,
                                                                            'card_width',
                                                                            e.target.value,
                                                                        )
                                                                    }
                                                                    className="w-full rounded border border-border bg-white px-3 py-2 text-sm"
                                                                >
                                                                    <option value="full">
                                                                        Completo
                                                                    </option>
                                                                    <option value="wide">
                                                                        Ancho (900px)
                                                                    </option>
                                                                    <option value="medium">
                                                                        Mediano (640px)
                                                                    </option>
                                                                    <option value="narrow">
                                                                        Estrecho (480px)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>

                        {/* Add section */}
                        <div className="rounded-lg border border-dashed border-border bg-gray-50 p-4">
                            <p className="mb-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">
                                Agregar sección
                            </p>
                            <div className="flex flex-wrap gap-2">
                                {[
                                    { type: 'text', label: 'Texto', icon: 'text_fields' },
                                    { type: 'image', label: 'Imagen', icon: 'image' },
                                    { type: 'video', label: 'Video', icon: 'play_circle' },
                                    { type: 'quote', label: 'Cita', icon: 'format_quote' },
                                    { type: 'callout', label: 'Llamada', icon: 'info' },
                                    { type: 'gallery', label: 'Galería', icon: 'collections' },
                                    { type: 'embed', label: 'Embed', icon: 'launch' },
                                    { type: 'card', label: 'Card', icon: 'credit_card' },
                                    {
                                        type: 'separator',
                                        label: 'Separador',
                                        icon: 'horizontal_rule',
                                    },
                                ].map(({ type, label, icon }) => (
                                    <button
                                        key={type}
                                        type="button"
                                        onClick={() => addArticleSection(type)}
                                        className="inline-flex items-center gap-1.5 rounded-full border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-primary hover:bg-primary/5"
                                    >
                                        <span className="material-symbols-outlined text-[15px] leading-none">
                                            {icon}
                                        </span>
                                        + {label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {templateKey === 'editorial_split_showcase' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Estos campos construyen el JSON de la plantilla.
                            </p>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Fondo</label>
                                <select
                                    value={templateData.bg || 'white'}
                                    onChange={e => updateTemplateData('bg', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="white">Blanco</option>
                                    <option value="surface">Surface (gris claro)</option>
                                    <option value="primary">Primario (oscuro)</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Posición de imagen
                                </label>
                                <select
                                    value={templateData.image_side || 'left'}
                                    onChange={e => updateTemplateData('image_side', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="left">Izquierda</option>
                                    <option value="right">Derecha</option>
                                </select>
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Eyebrow</label>
                                <input
                                    type="text"
                                    value={templateData.eyebrow || ''}
                                    onChange={e => updateTemplateData('eyebrow', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">Badge text</label>
                                <input
                                    type="text"
                                    value={templateData.badge_text || ''}
                                    onChange={e => updateTemplateData('badge_text', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">Title</label>
                            <input
                                type="text"
                                value={templateData.title || ''}
                                onChange={e => updateTemplateData('title', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>

                        <div className="space-y-4 rounded-lg border border-border bg-white p-4">
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <h4 className="text-sm font-semibold text-primary">
                                        Descripciones
                                    </h4>
                                    <p className="text-xs text-text-secondary">
                                        Agrega subtítulo, texto e icono por cada bloque descriptivo.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={addEditorialDescription}
                                    className="rounded-full border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-primary"
                                >
                                    Agregar descripción
                                </button>
                            </div>

                            <div className="space-y-3">
                                {editorialDescriptions.map((item, index) => {
                                    const summaryText = item.subtitle || `Descripción ${index + 1}`;

                                    return (
                                        <details
                                            key={`${summaryText}-${index}`}
                                            className="rounded-lg border border-border bg-surface-container-lowest p-3"
                                            open={index === 0}
                                        >
                                            <summary className="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-primary">
                                                <span>{summaryText}</span>
                                                <span className="text-xs font-medium text-text-secondary">
                                                    {item.icon_position === 'right'
                                                        ? 'Icono a la derecha'
                                                        : 'Icono a la izquierda'}
                                                </span>
                                            </summary>

                                            <div className="mt-4 space-y-4">
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Subtítulo
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={item.subtitle || ''}
                                                            onChange={e =>
                                                                updateEditorialDescription(
                                                                    index,
                                                                    'subtitle',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Icono
                                                        </label>
                                                        <select
                                                            value={item.icon || ''}
                                                            onChange={e =>
                                                                updateEditorialDescription(
                                                                    index,
                                                                    'icon',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="">Sin icono</option>
                                                            {ICON_OPTIONS.map(option => (
                                                                <option
                                                                    key={option.value}
                                                                    value={option.value}
                                                                >
                                                                    {option.label}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Posición del icono
                                                    </label>
                                                    <select
                                                        value={item.icon_position || 'left'}
                                                        onChange={e =>
                                                            updateEditorialDescription(
                                                                index,
                                                                'icon_position',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="left">Izquierda</option>
                                                        <option value="right">Derecha</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Descripción
                                                    </label>
                                                    <textarea
                                                        value={item.description || ''}
                                                        onChange={e =>
                                                            updateEditorialDescription(
                                                                index,
                                                                'description',
                                                                e.target.value,
                                                            )
                                                        }
                                                        rows={4}
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    />
                                                </div>

                                                <div className="flex justify-end">
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            removeEditorialDescription(index)
                                                        }
                                                        className="rounded-full border border-border px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-primary"
                                                        disabled={
                                                            editorialDescriptions.length === 1
                                                        }
                                                    >
                                                        Quitar descripción
                                                    </button>
                                                </div>
                                            </div>
                                        </details>
                                    );
                                })}
                            </div>
                        </div>

                        <div>
                            <h4 className="text-sm font-semibold text-primary">Imágenes</h4>
                            <p className="text-xs text-text-secondary">
                                Selecciona cada imagen y ajusta borde, sombra y redondeo por
                                separado.
                            </p>
                        </div>

                        <div className="space-y-3">
                            <details className="rounded-lg border border-border bg-white p-4">
                                <summary className="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-primary">
                                    <span>Imagen principal</span>
                                    <span className="text-xs font-medium text-text-secondary">
                                        Configuración plegable
                                    </span>
                                </summary>
                                <div className="mt-4 space-y-4">
                                    <MediaPicker
                                        label="Seleccionar imagen principal"
                                        singleSelect
                                        allowUpload={false}
                                        value={mediaToSelected(templateData.main_image)}
                                        onChange={value =>
                                            updateTemplateData('main_image', value?.[0] || null)
                                        }
                                    />
                                    <ImageStyleFields
                                        label="Estilo de imagen principal"
                                        prefix="main_image"
                                        data={templateData}
                                        setData={updateTemplateData}
                                    />
                                </div>
                            </details>

                            <details className="rounded-lg border border-border bg-white p-4">
                                <summary className="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-primary">
                                    <span>Imagen secundaria</span>
                                    <span className="text-xs font-medium text-text-secondary">
                                        Configuración plegable
                                    </span>
                                </summary>
                                <div className="mt-4 space-y-4">
                                    <MediaPicker
                                        label="Seleccionar imagen secundaria"
                                        singleSelect
                                        allowUpload={false}
                                        value={mediaToSelected(templateData.secondary_image)}
                                        onChange={value =>
                                            updateTemplateData(
                                                'secondary_image',
                                                value?.[0] || null,
                                            )
                                        }
                                    />
                                    <ImageStyleFields
                                        label="Estilo de imagen secundaria"
                                        prefix="secondary_image"
                                        data={templateData}
                                        setData={updateTemplateData}
                                    />
                                </div>
                            </details>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <ColorPickerField
                                label="Color del título"
                                value={templateData.title_color || ''}
                                onChange={v => updateTemplateData('title_color', v)}
                                allowEmpty
                            />
                            <ColorPickerField
                                label="Color de la descripción"
                                value={templateData.description_color || ''}
                                onChange={v => updateTemplateData('description_color', v)}
                                allowEmpty
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">CTA label</label>
                                <input
                                    type="text"
                                    value={templateData.cta_label || ''}
                                    onChange={e => updateTemplateData('cta_label', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">CTA URL</label>
                                <input
                                    type="text"
                                    value={templateData.cta_url || ''}
                                    onChange={e => updateTemplateData('cta_url', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>
                    </div>
                )}

                {templateKey === 'value_cards_row' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Cada tarjeta genera su bloque dentro del JSON.
                            </p>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">Fondo</label>
                            <select
                                value={templateData.bg || 'white'}
                                onChange={e => updateTemplateData('bg', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            >
                                <option value="white">Blanco</option>
                                <option value="surface">Surface (gris claro)</option>
                                <option value="primary">Primario (oscuro)</option>
                            </select>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        {templateData.bg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 0}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 0}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                        )}

                        <div className="space-y-2">
                            {(templateData.cards || []).map((card: any, index: number) => {
                                const isExpanded = expandedCardIndices[index] ?? false;
                                return (
                                    <div
                                        key={index}
                                        className="overflow-hidden rounded border border-border bg-white"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setExpandedCardIndices(prev => ({
                                                    ...prev,
                                                    [index]: !prev[index],
                                                }))
                                            }
                                            className="flex w-full items-center justify-between px-4 py-3 text-left"
                                        >
                                            <span className="text-sm font-semibold text-primary">
                                                {card.title
                                                    ? `Card ${index + 1} — ${card.title}`
                                                    : `Card ${index + 1}`}
                                            </span>
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={e => {
                                                        e.stopPropagation();
                                                        const cards = [
                                                            ...(templateData.cards || []),
                                                        ];
                                                        cards.splice(index, 1);
                                                        updateTemplateData('cards', cards);
                                                    }}
                                                    className="text-xs text-red-400 hover:text-red-600"
                                                >
                                                    ✕
                                                </button>
                                                <span className="text-xs text-text-secondary">
                                                    {isExpanded ? '▲' : '▼'}
                                                </span>
                                            </div>
                                        </button>
                                        {isExpanded && (
                                            <div className="space-y-4 border-t border-border p-4">
                                                {/* Content */}
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Contenido
                                                </p>
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Ícono
                                                        </label>
                                                        <select
                                                            value={card.icon || 'verified'}
                                                            onChange={e =>
                                                                updateCard(
                                                                    index,
                                                                    'icon',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            {ICON_OPTIONS.map(o => (
                                                                <option
                                                                    key={o.value}
                                                                    value={o.value}
                                                                >
                                                                    {o.label}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                    <ColorPickerField
                                                        label="Color del ícono"
                                                        value={card.icon_color || '#1a2236'}
                                                        onChange={v =>
                                                            updateCard(index, 'icon_color', v)
                                                        }
                                                    />
                                                </div>
                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Título
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={card.title || ''}
                                                        onChange={e =>
                                                            updateCard(
                                                                index,
                                                                'title',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    />
                                                </div>
                                                <ColorPickerField
                                                    label="Color del título"
                                                    value={card.title_color || ''}
                                                    onChange={v =>
                                                        updateCard(index, 'title_color', v)
                                                    }
                                                    allowEmpty
                                                />
                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Descripción
                                                    </label>
                                                    <textarea
                                                        value={card.description || ''}
                                                        onChange={e =>
                                                            updateCard(
                                                                index,
                                                                'description',
                                                                e.target.value,
                                                            )
                                                        }
                                                        rows={3}
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    />
                                                </div>
                                                <ColorPickerField
                                                    label="Color de la descripción"
                                                    value={card.description_color || ''}
                                                    onChange={v =>
                                                        updateCard(index, 'description_color', v)
                                                    }
                                                    allowEmpty
                                                />
                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Alineación del texto
                                                    </label>
                                                    <select
                                                        value={card.text_align || 'center'}
                                                        onChange={e =>
                                                            updateCard(
                                                                index,
                                                                'text_align',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="left">Izquierda</option>
                                                        <option value="center">Centro</option>
                                                        <option value="right">Derecha</option>
                                                    </select>
                                                </div>
                                                {/* Card styling */}
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Estilo de tarjeta
                                                </p>
                                                <ColorPickerField
                                                    label="Fondo de la tarjeta"
                                                    value={card.card_bg || '#ffffff'}
                                                    onChange={v => updateCard(index, 'card_bg', v)}
                                                />
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Borde
                                                        </label>
                                                        <select
                                                            value={card.card_border || 'none'}
                                                            onChange={e =>
                                                                updateCard(
                                                                    index,
                                                                    'card_border',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Sin borde</option>
                                                            <option value="solid">Sólido</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Radio
                                                        </label>
                                                        <select
                                                            value={card.card_radius || 'none'}
                                                            onChange={e =>
                                                                updateCard(
                                                                    index,
                                                                    'card_radius',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguno</option>
                                                            <option value="sm">Pequeño</option>
                                                            <option value="md">Mediano</option>
                                                            <option value="lg">Grande</option>
                                                            <option value="full">Completo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {card.card_border === 'solid' && (
                                                    <div className="grid gap-4 md:grid-cols-2">
                                                        <div>
                                                            <label className="mb-2 block text-sm font-medium">
                                                                Grosor
                                                            </label>
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                max="8"
                                                                value={card.card_border_width || 1}
                                                                onChange={e =>
                                                                    updateCard(
                                                                        index,
                                                                        'card_border_width',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                                            />
                                                        </div>
                                                        <ColorPickerField
                                                            label="Color del borde"
                                                            value={
                                                                card.card_border_color || '#e5e7eb'
                                                            }
                                                            onChange={v =>
                                                                updateCard(
                                                                    index,
                                                                    'card_border_color',
                                                                    v,
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                )}
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Sombra
                                                        </label>
                                                        <select
                                                            value={card.card_shadow || 'none'}
                                                            onChange={e =>
                                                                updateCard(
                                                                    index,
                                                                    'card_shadow',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguna</option>
                                                            <option value="sm">Pequeña</option>
                                                            <option value="md">Mediana</option>
                                                            <option value="lg">Grande</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Padding
                                                        </label>
                                                        <select
                                                            value={card.card_padding || 'md'}
                                                            onChange={e =>
                                                                updateCard(
                                                                    index,
                                                                    'card_padding',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguno</option>
                                                            <option value="sm">Pequeño</option>
                                                            <option value="md">Mediano</option>
                                                            <option value="lg">Grande</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    const newIndex = (templateData.cards || []).length;
                                    updateTemplateData('cards', [
                                        ...(templateData.cards || []),
                                        {
                                            icon: 'verified',
                                            title: 'Nueva tarjeta',
                                            description: '',
                                            icon_color: '#1a2236',
                                            title_color: '',
                                            description_color: '',
                                            text_align: 'center',
                                            card_bg: '#ffffff',
                                            card_border: 'none',
                                            card_border_width: '1',
                                            card_border_color: '#e5e7eb',
                                            card_radius: 'none',
                                            card_shadow: 'none',
                                            card_padding: 'md',
                                        },
                                    ]);
                                    setExpandedCardIndices(prev => ({ ...prev, [newIndex]: true }));
                                }}
                                className="rounded-md border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary hover:border-primary"
                            >
                                + Añadir tarjeta
                            </button>
                        </div>
                    </div>
                )}

                {templateKey === 'stat_row' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Números de impacto de la gestión.
                            </p>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Eyebrow (opcional)
                                </label>
                                <input
                                    type="text"
                                    value={templateData.eyebrow || ''}
                                    onChange={e => updateTemplateData('eyebrow', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">Fondo</label>
                                <select
                                    value={templateData.bg || 'white'}
                                    onChange={e => updateTemplateData('bg', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="white">Blanco</option>
                                    <option value="primary">Primario (oscuro)</option>
                                    <option value="surface">Surface (gris claro)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        {templateData.bg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 0}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 0}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                        )}

                        <div className="space-y-2">
                            {(templateData.stats || []).map((stat: any, i: number) => {
                                const isExpanded = expandedCardIndices[i] ?? false;
                                return (
                                    <div
                                        key={i}
                                        className="overflow-hidden rounded border border-border bg-white"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setExpandedCardIndices(prev => ({
                                                    ...prev,
                                                    [i]: !prev[i],
                                                }))
                                            }
                                            className="flex w-full items-center justify-between px-4 py-3 text-left"
                                        >
                                            <span className="text-sm font-semibold text-primary">
                                                {stat.number
                                                    ? `Estadística ${i + 1} — ${stat.number}${stat.suffix || ''} ${stat.label || ''}`
                                                    : `Estadística ${i + 1}`}
                                            </span>
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={e => {
                                                        e.stopPropagation();
                                                        const s = [...(templateData.stats || [])];
                                                        s.splice(i, 1);
                                                        updateTemplateData('stats', s);
                                                    }}
                                                    className="text-xs text-red-400 hover:text-red-600"
                                                >
                                                    ✕
                                                </button>
                                                <span className="text-xs text-text-secondary">
                                                    {isExpanded ? '▲' : '▼'}
                                                </span>
                                            </div>
                                        </button>
                                        {isExpanded && (
                                            <div className="space-y-4 border-t border-border p-4">
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Datos
                                                </p>
                                                <div className="grid gap-3 md:grid-cols-3">
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium">
                                                            Número
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={stat.number || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'number',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium">
                                                            Sufijo
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={stat.suffix || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'suffix',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                            placeholder="% + ..."
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium">
                                                            Etiqueta
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={stat.label || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'label',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                        />
                                                    </div>
                                                </div>
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Ícono
                                                        </label>
                                                        <select
                                                            value={stat.icon || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'icon',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="">Sin ícono</option>
                                                            {ICON_OPTIONS.map(o => (
                                                                <option
                                                                    key={o.value}
                                                                    value={o.value}
                                                                >
                                                                    {o.label}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                    <ColorPickerField
                                                        label="Color del ícono"
                                                        value={stat.icon_color || ''}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'stats',
                                                                i,
                                                                'icon_color',
                                                                v,
                                                            )
                                                        }
                                                        allowEmpty
                                                    />
                                                </div>
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <ColorPickerField
                                                        label="Color del número"
                                                        value={stat.number_color || ''}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'stats',
                                                                i,
                                                                'number_color',
                                                                v,
                                                            )
                                                        }
                                                        allowEmpty
                                                    />
                                                    <ColorPickerField
                                                        label="Color de la etiqueta"
                                                        value={stat.label_color || ''}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'stats',
                                                                i,
                                                                'label_color',
                                                                v,
                                                            )
                                                        }
                                                        allowEmpty
                                                    />
                                                </div>
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Estilo de celda
                                                </p>
                                                <ColorPickerField
                                                    label="Fondo de la celda"
                                                    value={stat.card_bg || 'transparent'}
                                                    onChange={v =>
                                                        updateListItem('stats', i, 'card_bg', v)
                                                    }
                                                    allowEmpty
                                                />
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Borde
                                                        </label>
                                                        <select
                                                            value={stat.card_border || 'none'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'card_border',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Sin borde</option>
                                                            <option value="solid">Sólido</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Radio
                                                        </label>
                                                        <select
                                                            value={stat.card_radius || 'none'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'card_radius',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguno</option>
                                                            <option value="sm">Pequeño</option>
                                                            <option value="md">Mediano</option>
                                                            <option value="lg">Grande</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {stat.card_border === 'solid' && (
                                                    <div className="grid gap-4 md:grid-cols-2">
                                                        <div>
                                                            <label className="mb-2 block text-sm font-medium">
                                                                Grosor
                                                            </label>
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                max="8"
                                                                value={stat.card_border_width || 1}
                                                                onChange={e =>
                                                                    updateListItem(
                                                                        'stats',
                                                                        i,
                                                                        'card_border_width',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                                            />
                                                        </div>
                                                        <ColorPickerField
                                                            label="Color del borde"
                                                            value={
                                                                stat.card_border_color || '#e5e7eb'
                                                            }
                                                            onChange={v =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'card_border_color',
                                                                    v,
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                )}
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Sombra
                                                        </label>
                                                        <select
                                                            value={stat.card_shadow || 'none'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'card_shadow',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguna</option>
                                                            <option value="sm">Pequeña</option>
                                                            <option value="md">Mediana</option>
                                                            <option value="lg">Grande</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Padding
                                                        </label>
                                                        <select
                                                            value={stat.card_padding || 'none'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'stats',
                                                                    i,
                                                                    'card_padding',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguno</option>
                                                            <option value="sm">Pequeño</option>
                                                            <option value="md">Mediano</option>
                                                            <option value="lg">Grande</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    const newIndex = (templateData.stats || []).length;
                                    updateTemplateData('stats', [
                                        ...(templateData.stats || []),
                                        {
                                            number: '0',
                                            suffix: '',
                                            label: 'Nueva estadística',
                                            icon: '',
                                            icon_color: '',
                                            number_color: '',
                                            label_color: '',
                                            card_bg: 'transparent',
                                            card_border: 'none',
                                            card_border_width: '1',
                                            card_border_color: '#e5e7eb',
                                            card_radius: 'none',
                                            card_shadow: 'none',
                                            card_padding: 'none',
                                        },
                                    ]);
                                    setExpandedCardIndices(prev => ({ ...prev, [newIndex]: true }));
                                }}
                                className="rounded-md border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary hover:border-primary"
                            >
                                + Añadir estadística
                            </button>
                        </div>
                    </div>
                )}

                {templateKey === 'cta_banner' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Franja de llamada a la acción.
                            </p>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Fondo</label>
                                <select
                                    value={templateData.bg || 'primary'}
                                    onChange={e => updateTemplateData('bg', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="primary">Primario</option>
                                    <option value="secondary">Secundario</option>
                                    <option value="surface">Surface</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">Alineación</label>
                                <select
                                    value={templateData.alignment || 'split'}
                                    onChange={e => updateTemplateData('alignment', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="split">Dividido (texto izq, botón der)</option>
                                    <option value="center">Centrado</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        {templateData.bg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 60}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 60}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                        )}

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen decorativa fg (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.fg_image)}
                                onChange={value =>
                                    updateTemplateData('fg_image', value?.[0] || null)
                                }
                            />
                            <div className="mt-3">
                                <ImageStyleFields
                                    label="Estilo de imagen decorativa"
                                    prefix="fg_image"
                                    data={templateData}
                                    setData={updateTemplateData}
                                />
                            </div>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">Título</label>
                            <input
                                type="text"
                                value={templateData.title || ''}
                                onChange={e => updateTemplateData('title', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Subtítulo (opcional)
                            </label>
                            <input
                                type="text"
                                value={templateData.subtitle || ''}
                                onChange={e => updateTemplateData('subtitle', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <ColorPickerField
                                label="Color del título"
                                value={templateData.title_color || ''}
                                onChange={v => updateTemplateData('title_color', v)}
                                allowEmpty
                            />
                            <ColorPickerField
                                label="Color del subtítulo"
                                value={templateData.subtitle_color || ''}
                                onChange={v => updateTemplateData('subtitle_color', v)}
                                allowEmpty
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Botón principal — texto
                                </label>
                                <input
                                    type="text"
                                    value={templateData.cta_primary_label || ''}
                                    onChange={e =>
                                        updateTemplateData('cta_primary_label', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Botón principal — URL
                                </label>
                                <input
                                    type="text"
                                    value={templateData.cta_primary_url || ''}
                                    onChange={e =>
                                        updateTemplateData('cta_primary_url', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Botón secundario — texto (opcional)
                                </label>
                                <input
                                    type="text"
                                    value={templateData.cta_secondary_label || ''}
                                    onChange={e =>
                                        updateTemplateData('cta_secondary_label', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Botón secundario — URL
                                </label>
                                <input
                                    type="text"
                                    value={templateData.cta_secondary_url || ''}
                                    onChange={e =>
                                        updateTemplateData('cta_secondary_url', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>
                    </div>
                )}

                {templateKey === 'hero_section' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Hero para páginas interiores.
                            </p>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Eyebrow</label>
                                <input
                                    type="text"
                                    value={templateData.eyebrow || ''}
                                    onChange={e => updateTemplateData('eyebrow', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Badge (opcional)
                                </label>
                                <input
                                    type="text"
                                    value={templateData.badge_text || ''}
                                    onChange={e => updateTemplateData('badge_text', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">Título</label>
                            <input
                                type="text"
                                value={templateData.title || ''}
                                onChange={e => updateTemplateData('title', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">Subtítulo</label>
                            <textarea
                                value={templateData.subtitle || ''}
                                onChange={e => updateTemplateData('subtitle', e.target.value)}
                                rows={3}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-3">
                            <ColorPickerField
                                label="Color eyebrow"
                                value={templateData.eyebrow_color || ''}
                                onChange={v => updateTemplateData('eyebrow_color', v)}
                                allowEmpty
                            />
                            <ColorPickerField
                                label="Color del título"
                                value={templateData.title_color || ''}
                                onChange={v => updateTemplateData('title_color', v)}
                                allowEmpty
                            />
                            <ColorPickerField
                                label="Color del subtítulo"
                                value={templateData.subtitle_color || ''}
                                onChange={v => updateTemplateData('subtitle_color', v)}
                                allowEmpty
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-3">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Altura</label>
                                <select
                                    value={templateData.height || 'md'}
                                    onChange={e => updateTemplateData('height', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="sm">Pequeño (~280px)</option>
                                    <option value="md">Mediano (~420px)</option>
                                    <option value="lg">Grande (~560px)</option>
                                    <option value="full">Pantalla completa</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 50}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 50}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Color overlay
                                </label>
                                <select
                                    value={templateData.overlay_color || 'black'}
                                    onChange={e =>
                                        updateTemplateData('overlay_color', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="black">Negro</option>
                                    <option value="primary">Primario</option>
                                    <option value="secondary">Secundario</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Alineación de texto
                            </label>
                            <select
                                value={templateData.text_align || 'left'}
                                onChange={e => updateTemplateData('text_align', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            >
                                <option value="left">Izquierda</option>
                                <option value="center">Centrado</option>
                            </select>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Texto CTA (opcional)
                                </label>
                                <input
                                    type="text"
                                    value={templateData.cta_label || ''}
                                    onChange={e => updateTemplateData('cta_label', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">URL CTA</label>
                                <input
                                    type="text"
                                    value={templateData.cta_url || ''}
                                    onChange={e => updateTemplateData('cta_url', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>
                        <ColorPickerField
                            label="Color de fondo (cuando no hay imagen)"
                            value={templateData.bg || 'primary'}
                            onChange={v => updateTemplateData('bg', v)}
                        />
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen de fondo"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen frontal (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen frontal"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.fg_image)}
                                onChange={value =>
                                    updateTemplateData('fg_image', value?.[0] || null)
                                }
                            />
                            <div className="mt-3">
                                <ImageStyleFields
                                    label="Estilo de imagen frontal"
                                    prefix="fg_image"
                                    data={templateData}
                                    setData={updateTemplateData}
                                />
                            </div>
                        </div>

                        {templateData.fg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Posición de imagen frontal
                                </label>
                                <select
                                    value={templateData.fg_position || 'right'}
                                    onChange={e =>
                                        updateTemplateData('fg_position', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="left">Izquierda (texto a la derecha)</option>
                                    <option value="right">Derecha (texto a la izquierda)</option>
                                </select>
                            </div>
                        )}
                    </div>
                )}

                {templateKey === 'image_gallery' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Grid de imágenes con caption opcional.
                            </p>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Título de sección (opcional)
                            </label>
                            <input
                                type="text"
                                value={templateData.title || ''}
                                onChange={e => updateTemplateData('title', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Columnas</label>
                                <select
                                    value={templateData.cols || '3'}
                                    onChange={e => updateTemplateData('cols', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="2">2 columnas</option>
                                    <option value="3">3 columnas</option>
                                    <option value="4">4 columnas</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">Proporción</label>
                                <select
                                    value={templateData.aspect || 'photo'}
                                    onChange={e => updateTemplateData('aspect', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="photo">Fotográfica (4/3)</option>
                                    <option value="square">Cuadrada (1/1)</option>
                                    <option value="video">Video (16/9)</option>
                                </select>
                            </div>
                        </div>
                        <div className="space-y-2">
                            {(templateData.items || []).map((item: any, i: number) => {
                                const isExpanded = expandedCardIndices[i] ?? false;
                                return (
                                    <div
                                        key={i}
                                        className="overflow-hidden rounded border border-border bg-white"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setExpandedCardIndices(prev => ({
                                                    ...prev,
                                                    [i]: !prev[i],
                                                }))
                                            }
                                            className="flex w-full items-center justify-between px-4 py-3 text-left"
                                        >
                                            <span className="text-sm font-semibold text-primary">
                                                {item.caption
                                                    ? `Imagen ${i + 1} — ${item.caption}`
                                                    : `Imagen ${i + 1}`}
                                            </span>
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={e => {
                                                        e.stopPropagation();
                                                        const items = [
                                                            ...(templateData.items || []),
                                                        ];
                                                        items.splice(i, 1);
                                                        updateTemplateData('items', items);
                                                    }}
                                                    className="text-xs text-red-400 hover:text-red-600"
                                                >
                                                    ✕
                                                </button>
                                                <span className="text-xs text-text-secondary">
                                                    {isExpanded ? '▲' : '▼'}
                                                </span>
                                            </div>
                                        </button>
                                        {isExpanded && (
                                            <div className="space-y-4 border-t border-border p-4">
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Contenido
                                                </p>
                                                <MediaPicker
                                                    label="Seleccionar imagen"
                                                    singleSelect
                                                    allowUpload={false}
                                                    value={mediaToSelected(item.image)}
                                                    onChange={value =>
                                                        updateListItem(
                                                            'items',
                                                            i,
                                                            'image',
                                                            value?.[0] || null,
                                                        )
                                                    }
                                                />
                                                <div>
                                                    <label className="mb-1 block text-xs font-medium">
                                                        Caption (opcional)
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={item.caption || ''}
                                                        onChange={e =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'caption',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                    />
                                                </div>
                                                <ColorPickerField
                                                    label="Color del caption"
                                                    value={item.caption_color || ''}
                                                    onChange={v =>
                                                        updateListItem(
                                                            'items',
                                                            i,
                                                            'caption_color',
                                                            v,
                                                        )
                                                    }
                                                    allowEmpty
                                                />
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Estilo de la imagen
                                                </p>
                                                <ColorPickerField
                                                    label="Fondo del contenedor"
                                                    value={item.card_bg || '#e5e7eb'}
                                                    onChange={v =>
                                                        updateListItem('items', i, 'card_bg', v)
                                                    }
                                                />
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Borde
                                                        </label>
                                                        <select
                                                            value={item.card_border || 'none'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'items',
                                                                    i,
                                                                    'card_border',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Sin borde</option>
                                                            <option value="solid">Sólido</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label className="mb-2 block text-sm font-medium">
                                                            Radio
                                                        </label>
                                                        <select
                                                            value={item.card_radius || 'md'}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'items',
                                                                    i,
                                                                    'card_radius',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-3 py-2"
                                                        >
                                                            <option value="none">Ninguno</option>
                                                            <option value="sm">Pequeño</option>
                                                            <option value="md">Mediano</option>
                                                            <option value="lg">Grande</option>
                                                            <option value="full">Completo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {item.card_border === 'solid' && (
                                                    <div className="grid gap-4 md:grid-cols-2">
                                                        <div>
                                                            <label className="mb-2 block text-sm font-medium">
                                                                Grosor
                                                            </label>
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                max="8"
                                                                value={item.card_border_width || 1}
                                                                onChange={e =>
                                                                    updateListItem(
                                                                        'items',
                                                                        i,
                                                                        'card_border_width',
                                                                        e.target.value,
                                                                    )
                                                                }
                                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                                            />
                                                        </div>
                                                        <ColorPickerField
                                                            label="Color del borde"
                                                            value={
                                                                item.card_border_color || '#e5e7eb'
                                                            }
                                                            onChange={v =>
                                                                updateListItem(
                                                                    'items',
                                                                    i,
                                                                    'card_border_color',
                                                                    v,
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                )}
                                                <div>
                                                    <label className="mb-2 block text-sm font-medium">
                                                        Sombra
                                                    </label>
                                                    <select
                                                        value={item.card_shadow || 'none'}
                                                        onChange={e =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'card_shadow',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="none">Ninguna</option>
                                                        <option value="sm">Pequeña</option>
                                                        <option value="md">Mediana</option>
                                                        <option value="lg">Grande</option>
                                                    </select>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    const newIndex = (templateData.items || []).length;
                                    updateTemplateData('items', [
                                        ...(templateData.items || []),
                                        {
                                            image: null,
                                            caption: '',
                                            caption_color: '',
                                            card_bg: '#e5e7eb',
                                            card_border: 'none',
                                            card_border_width: '1',
                                            card_border_color: '#e5e7eb',
                                            card_radius: 'md',
                                            card_shadow: 'none',
                                        },
                                    ]);
                                    setExpandedCardIndices(prev => ({ ...prev, [newIndex]: true }));
                                }}
                                className="rounded-md border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary hover:border-primary"
                            >
                                + Añadir imagen
                            </button>
                        </div>
                    </div>
                )}

                {templateKey === 'timeline' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Lista cronológica de hitos.
                            </p>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">Fondo</label>
                            <select
                                value={templateData.bg || 'white'}
                                onChange={e => updateTemplateData('bg', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            >
                                <option value="white">Blanco</option>
                                <option value="surface">Surface (gris claro)</option>
                                <option value="primary">Primario (oscuro)</option>
                            </select>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        {templateData.bg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 0}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 0}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                        )}

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Título de sección
                            </label>
                            <input
                                type="text"
                                value={templateData.title || ''}
                                onChange={e => updateTemplateData('title', e.target.value)}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>
                        <div className="space-y-2">
                            {(templateData.items || []).map((item: any, i: number) => {
                                const isExpanded = expandedCardIndices[i] ?? false;
                                return (
                                    <div
                                        key={i}
                                        className="overflow-hidden rounded border border-border bg-white"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setExpandedCardIndices(prev => ({
                                                    ...prev,
                                                    [i]: !prev[i],
                                                }))
                                            }
                                            className="flex w-full items-center justify-between px-4 py-3 text-left"
                                        >
                                            <span className="text-sm font-semibold text-primary">
                                                {item.title
                                                    ? `Hito ${i + 1} — ${item.date ? item.date + ' · ' : ''}${item.title}`
                                                    : `Hito ${i + 1}`}
                                            </span>
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={e => {
                                                        e.stopPropagation();
                                                        const items = [
                                                            ...(templateData.items || []),
                                                        ];
                                                        items.splice(i, 1);
                                                        updateTemplateData('items', items);
                                                    }}
                                                    className="text-xs text-red-400 hover:text-red-600"
                                                >
                                                    ✕
                                                </button>
                                                <span className="text-xs text-text-secondary">
                                                    {isExpanded ? '▲' : '▼'}
                                                </span>
                                            </div>
                                        </button>
                                        {isExpanded && (
                                            <div className="space-y-4 border-t border-border p-4">
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Contenido
                                                </p>
                                                <div className="grid gap-3 md:grid-cols-2">
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium">
                                                            Fecha / año
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={item.date || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'items',
                                                                    i,
                                                                    'date',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium">
                                                            Ícono
                                                        </label>
                                                        <select
                                                            value={item.icon || ''}
                                                            onChange={e =>
                                                                updateListItem(
                                                                    'items',
                                                                    i,
                                                                    'icon',
                                                                    e.target.value,
                                                                )
                                                            }
                                                            className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                        >
                                                            <option value="">Sin ícono</option>
                                                            {ICON_OPTIONS.map(o => (
                                                                <option
                                                                    key={o.value}
                                                                    value={o.value}
                                                                >
                                                                    {o.label}
                                                                </option>
                                                            ))}
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label className="mb-1 block text-xs font-medium">
                                                        Título del hito
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={item.title || ''}
                                                        onChange={e =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'title',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                    />
                                                </div>
                                                <div>
                                                    <label className="mb-1 block text-xs font-medium">
                                                        Descripción
                                                    </label>
                                                    <textarea
                                                        value={item.description || ''}
                                                        onChange={e =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'description',
                                                                e.target.value,
                                                            )
                                                        }
                                                        rows={2}
                                                        className="w-full rounded border border-border bg-white px-2 py-1.5 text-sm"
                                                    />
                                                </div>
                                                <p className="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                                                    Colores
                                                </p>
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <ColorPickerField
                                                        label="Color del punto"
                                                        value={item.accent_color || '#1a2236'}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'accent_color',
                                                                v,
                                                            )
                                                        }
                                                    />
                                                    <ColorPickerField
                                                        label="Color del ícono"
                                                        value={item.icon_color || '#ffffff'}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'icon_color',
                                                                v,
                                                            )
                                                        }
                                                    />
                                                </div>
                                                <div className="grid gap-4 md:grid-cols-2">
                                                    <ColorPickerField
                                                        label="Color de la fecha"
                                                        value={item.date_color || ''}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'date_color',
                                                                v,
                                                            )
                                                        }
                                                        allowEmpty
                                                    />
                                                    <ColorPickerField
                                                        label="Color del título"
                                                        value={item.title_color || ''}
                                                        onChange={v =>
                                                            updateListItem(
                                                                'items',
                                                                i,
                                                                'title_color',
                                                                v,
                                                            )
                                                        }
                                                        allowEmpty
                                                    />
                                                </div>
                                                <ColorPickerField
                                                    label="Color de la descripción"
                                                    value={item.description_color || ''}
                                                    onChange={v =>
                                                        updateListItem(
                                                            'items',
                                                            i,
                                                            'description_color',
                                                            v,
                                                        )
                                                    }
                                                    allowEmpty
                                                />
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    const newIndex = (templateData.items || []).length;
                                    updateTemplateData('items', [
                                        ...(templateData.items || []),
                                        {
                                            date: '',
                                            title: 'Nuevo hito',
                                            description: '',
                                            icon: '',
                                            icon_color: '#ffffff',
                                            title_color: '',
                                            date_color: '',
                                            description_color: '',
                                            accent_color: '#1a2236',
                                        },
                                    ]);
                                    setExpandedCardIndices(prev => ({ ...prev, [newIndex]: true }));
                                }}
                                className="rounded-md border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary hover:border-primary"
                            >
                                + Añadir hito
                            </button>
                        </div>
                    </div>
                )}

                {templateKey === 'quote_highlight' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <div>
                            <h4 className="text-sm font-semibold text-primary">Campos guiados</h4>
                            <p className="text-xs text-text-secondary">
                                Cita grande centrada con autor.
                            </p>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-medium">Cita</label>
                            <textarea
                                value={templateData.quote || ''}
                                onChange={e => updateTemplateData('quote', e.target.value)}
                                rows={4}
                                className="w-full rounded border border-border bg-white px-3 py-2"
                            />
                        </div>
                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Nombre del autor
                                </label>
                                <input
                                    type="text"
                                    value={templateData.author_name || ''}
                                    onChange={e =>
                                        updateTemplateData('author_name', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Cargo / título (opcional)
                                </label>
                                <input
                                    type="text"
                                    value={templateData.author_title || ''}
                                    onChange={e =>
                                        updateTemplateData('author_title', e.target.value)
                                    }
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                />
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <ColorPickerField
                                label="Color de la cita"
                                value={templateData.quote_color || ''}
                                onChange={v => updateTemplateData('quote_color', v)}
                                allowEmpty
                            />
                            <ColorPickerField
                                label="Color del autor"
                                value={templateData.author_color || ''}
                                onChange={v => updateTemplateData('author_color', v)}
                                allowEmpty
                            />
                        </div>

                        <div className="grid gap-4 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-medium">Fondo</label>
                                <select
                                    value={templateData.bg || 'surface'}
                                    onChange={e => updateTemplateData('bg', e.target.value)}
                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                >
                                    <option value="surface">Surface (gris claro)</option>
                                    <option value="white">Blanco</option>
                                    <option value="primary">Primario (oscuro)</option>
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Foto del autor (opcional)
                                </label>
                                <MediaPicker
                                    label="Seleccionar foto"
                                    singleSelect
                                    allowUpload={false}
                                    value={mediaToSelected(templateData.author_image)}
                                    onChange={value =>
                                        updateTemplateData('author_image', value?.[0] || null)
                                    }
                                />
                                <div className="mt-3">
                                    <ImageStyleFields
                                        label="Estilo de foto del autor"
                                        prefix="author_image"
                                        data={templateData}
                                        setData={updateTemplateData}
                                    />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Imagen de fondo (opcional)
                            </label>
                            <MediaPicker
                                label="Seleccionar imagen"
                                singleSelect
                                allowUpload={false}
                                value={mediaToSelected(templateData.bg_image)}
                                onChange={value =>
                                    updateTemplateData('bg_image', value?.[0] || null)
                                }
                            />
                        </div>

                        {templateData.bg_image && (
                            <div>
                                <label className="mb-2 block text-sm font-medium">
                                    Opacidad overlay ({templateData.overlay_opacity ?? 0}%)
                                </label>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={templateData.overlay_opacity ?? 0}
                                    onChange={e =>
                                        updateTemplateData(
                                            'overlay_opacity',
                                            Number(e.target.value),
                                        )
                                    }
                                    className="w-full"
                                />
                            </div>
                        )}
                    </div>
                )}

                {templateKey === 'configurable_cards' && (
                    <div className="space-y-4 rounded-lg border border-border bg-gray-50 p-4">
                        <h4 className="text-sm font-semibold text-primary">
                            Tarjetas configurables
                        </h4>

                        {/* Sección */}
                        <div className="space-y-4 rounded border border-border bg-white p-4">
                            <p className="text-xs font-bold uppercase tracking-wider text-primary/50">
                                Sección
                            </p>
                            <div className="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Título de sección
                                    </label>
                                    <input
                                        type="text"
                                        value={templateData.section_title || ''}
                                        onChange={e =>
                                            updateTemplateData('section_title', e.target.value)
                                        }
                                        placeholder="(opcional)"
                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                    />
                                </div>
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Subtítulo
                                    </label>
                                    <input
                                        type="text"
                                        value={templateData.section_subtitle || ''}
                                        onChange={e =>
                                            updateTemplateData('section_subtitle', e.target.value)
                                        }
                                        placeholder="(opcional)"
                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                    />
                                </div>
                            </div>
                            <div className="grid gap-3 md:grid-cols-3">
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Fondo sección
                                    </label>
                                    <select
                                        value={templateData.bg || 'white'}
                                        onChange={e => updateTemplateData('bg', e.target.value)}
                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                    >
                                        <option value="white">Blanco</option>
                                        <option value="surface">Surface</option>
                                        <option value="primary">Primario</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Columnas
                                    </label>
                                    <select
                                        value={String(templateData.cols || 3)}
                                        onChange={e =>
                                            updateTemplateData('cols', parseInt(e.target.value))
                                        }
                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                    >
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Espacio entre tarjetas
                                    </label>
                                    <select
                                        value={templateData.gap || 'md'}
                                        onChange={e => updateTemplateData('gap', e.target.value)}
                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                    >
                                        <option value="sm">Pequeño</option>
                                        <option value="md">Mediano</option>
                                        <option value="lg">Grande</option>
                                        <option value="xl">Extra grande</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label className="mb-1 block text-sm font-medium">
                                    Imagen de fondo (opcional)
                                </label>
                                <MediaPicker
                                    label="Seleccionar imagen"
                                    singleSelect
                                    allowUpload={false}
                                    value={mediaToSelected(templateData.bg_image)}
                                    onChange={value =>
                                        updateTemplateData('bg_image', value?.[0] || null)
                                    }
                                />
                            </div>
                            {templateData.bg_image && (
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        Opacidad overlay ({templateData.overlay_opacity ?? 0}%)
                                    </label>
                                    <input
                                        type="range"
                                        min="0"
                                        max="100"
                                        value={templateData.overlay_opacity ?? 0}
                                        onChange={e =>
                                            updateTemplateData(
                                                'overlay_opacity',
                                                Number(e.target.value),
                                            )
                                        }
                                        className="w-full"
                                    />
                                </div>
                            )}
                        </div>

                        {/* Tarjetas */}
                        {(templateData.cards || []).map((card: any, ci: number) => (
                            <div key={ci} className="rounded border border-border bg-white">
                                <div className="flex items-center justify-between gap-2 px-4 py-3">
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setExpandedCardIndices(prev => ({
                                                ...prev,
                                                [ci]: !prev[ci],
                                            }))
                                        }
                                        className="flex flex-1 items-center gap-2 text-left"
                                    >
                                        <svg
                                            viewBox="0 0 16 16"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="1.5"
                                            className={`h-3.5 w-3.5 shrink-0 transition-transform ${expandedCardIndices[ci] ? 'rotate-90' : ''}`}
                                        >
                                            <path
                                                d="M6 4l4 4-4 4"
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                            />
                                        </svg>
                                        <span className="truncate text-sm font-semibold text-primary">
                                            {card.title || `Tarjeta ${ci + 1}`}
                                        </span>
                                        {card.image && (
                                            <span className="shrink-0 rounded-full bg-blue-100 px-2 py-0.5 text-[11px] text-blue-700">
                                                imagen
                                            </span>
                                        )}
                                    </button>
                                    {(templateData.cards || []).length > 1 && (
                                        <button
                                            type="button"
                                            title="Eliminar tarjeta"
                                            onClick={() => {
                                                const cards = (templateData.cards || []).filter(
                                                    (_: any, i: number) => i !== ci,
                                                );
                                                updateTemplateData('cards', cards);
                                            }}
                                            className="rounded p-1 text-red-400 hover:bg-red-50 hover:text-red-600"
                                        >
                                            <svg
                                                viewBox="0 0 16 16"
                                                fill="none"
                                                stroke="currentColor"
                                                strokeWidth="1.5"
                                                className="h-4 w-4"
                                            >
                                                <path
                                                    d="M3 4h10M6 4V3h4v1M5 4v8a1 1 0 001 1h4a1 1 0 001-1V4"
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                />
                                            </svg>
                                        </button>
                                    )}
                                </div>

                                {expandedCardIndices[ci] && (
                                    <div className="space-y-4 border-t border-border p-4">
                                        <p className="text-xs font-bold uppercase tracking-wider text-primary/50">
                                            Contenido
                                        </p>

                                        <div>
                                            <label className="mb-1 block text-sm font-medium">
                                                Imagen (opcional)
                                            </label>
                                            <MediaPicker
                                                label="Seleccionar imagen"
                                                singleSelect
                                                allowUpload={false}
                                                value={mediaToSelected(card.image)}
                                                onChange={value =>
                                                    updateCard(ci, 'image', value?.[0] || null)
                                                }
                                            />
                                        </div>

                                        {card.image && (
                                            <div className="grid gap-3 md:grid-cols-2">
                                                <div>
                                                    <label className="mb-1 block text-sm font-medium">
                                                        Posición imagen
                                                    </label>
                                                    <select
                                                        value={card.image_position || 'top'}
                                                        onChange={e =>
                                                            updateCard(
                                                                ci,
                                                                'image_position',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="top">Arriba</option>
                                                        <option value="bottom">Abajo</option>
                                                        <option value="left">Izquierda</option>
                                                        <option value="right">Derecha</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label className="mb-1 block text-sm font-medium">
                                                        Proporción
                                                    </label>
                                                    <select
                                                        value={card.image_aspect || 'photo'}
                                                        onChange={e =>
                                                            updateCard(
                                                                ci,
                                                                'image_aspect',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="photo">
                                                            Fotográfica (4:3)
                                                        </option>
                                                        <option value="square">Cuadrada</option>
                                                        <option value="video">Video (16:9)</option>
                                                        <option value="auto">Auto</option>
                                                    </select>
                                                </div>
                                            </div>
                                        )}

                                        <div className="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Ícono (opcional)
                                                </label>
                                                <select
                                                    value={card.icon || ''}
                                                    onChange={e =>
                                                        updateCard(ci, 'icon', e.target.value)
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="">Sin ícono</option>
                                                    {ICON_OPTIONS.map(o => (
                                                        <option key={o.value} value={o.value}>
                                                            {o.label}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>
                                            {card.icon && (
                                                <ColorPickerField
                                                    label="Color ícono"
                                                    value={card.icon_color || '#1a2236'}
                                                    onChange={v => updateCard(ci, 'icon_color', v)}
                                                />
                                            )}
                                        </div>

                                        <div>
                                            <label className="mb-1 block text-sm font-medium">
                                                Título
                                            </label>
                                            <input
                                                type="text"
                                                value={card.title || ''}
                                                onChange={e =>
                                                    updateCard(ci, 'title', e.target.value)
                                                }
                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                            />
                                        </div>

                                        <div className="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Tamaño título
                                                </label>
                                                <select
                                                    value={card.title_size || 'lg'}
                                                    onChange={e =>
                                                        updateCard(ci, 'title_size', e.target.value)
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="sm">Pequeño</option>
                                                    <option value="md">Mediano</option>
                                                    <option value="lg">Grande</option>
                                                    <option value="xl">Extra grande</option>
                                                    <option value="2xl">Muy grande</option>
                                                </select>
                                            </div>
                                            <ColorPickerField
                                                label="Color título"
                                                value={card.title_color || ''}
                                                onChange={v => updateCard(ci, 'title_color', v)}
                                                allowEmpty
                                            />
                                        </div>

                                        <div>
                                            <label className="mb-1 block text-sm font-medium">
                                                Subtítulo (opcional)
                                            </label>
                                            <input
                                                type="text"
                                                value={card.subtitle || ''}
                                                onChange={e =>
                                                    updateCard(ci, 'subtitle', e.target.value)
                                                }
                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                            />
                                        </div>

                                        <div>
                                            <label className="mb-1 block text-sm font-medium">
                                                Descripción (opcional)
                                            </label>
                                            <textarea
                                                value={card.body || ''}
                                                onChange={e =>
                                                    updateCard(ci, 'body', e.target.value)
                                                }
                                                rows={3}
                                                className="w-full rounded border border-border bg-white px-3 py-2"
                                            />
                                        </div>

                                        <div className="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Botón texto
                                                </label>
                                                <input
                                                    type="text"
                                                    value={card.cta_label || ''}
                                                    onChange={e =>
                                                        updateCard(ci, 'cta_label', e.target.value)
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                />
                                            </div>
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Botón URL
                                                </label>
                                                <input
                                                    type="text"
                                                    value={card.cta_url || ''}
                                                    onChange={e =>
                                                        updateCard(ci, 'cta_url', e.target.value)
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <label className="mb-1 block text-sm font-medium">
                                                Alineación texto
                                            </label>
                                            <div className="flex gap-2">
                                                {(
                                                    [
                                                        ['left', 'Izquierda'],
                                                        ['center', 'Centro'],
                                                        ['right', 'Derecha'],
                                                    ] as [string, string][]
                                                ).map(([v, l]) => (
                                                    <button
                                                        key={v}
                                                        type="button"
                                                        onClick={() =>
                                                            updateCard(ci, 'text_align', v)
                                                        }
                                                        className={`flex-1 rounded border px-2 py-1.5 text-xs font-semibold transition ${
                                                            (card.text_align || 'left') === v
                                                                ? 'border-primary bg-primary text-white'
                                                                : 'border-border bg-white text-text-secondary hover:border-primary'
                                                        }`}
                                                    >
                                                        {l}
                                                    </button>
                                                ))}
                                            </div>
                                        </div>

                                        <p className="text-xs font-bold uppercase tracking-wider text-primary/50">
                                            Estilos de la tarjeta
                                        </p>

                                        <ColorPickerField
                                            label="Fondo tarjeta"
                                            value={card.card_bg || '#ffffff'}
                                            onChange={v => updateCard(ci, 'card_bg', v)}
                                        />

                                        <div className="grid gap-3 md:grid-cols-3">
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Borde
                                                </label>
                                                <select
                                                    value={card.card_border || 'none'}
                                                    onChange={e =>
                                                        updateCard(
                                                            ci,
                                                            'card_border',
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="none">Sin borde</option>
                                                    <option value="solid">Sólido</option>
                                                    <option value="dashed">Punteado</option>
                                                    <option value="dotted">Dotted</option>
                                                </select>
                                            </div>
                                            {card.card_border && card.card_border !== 'none' && (
                                                <div>
                                                    <label className="mb-1 block text-sm font-medium">
                                                        Grosor
                                                    </label>
                                                    <select
                                                        value={card.card_border_width || '1'}
                                                        onChange={e =>
                                                            updateCard(
                                                                ci,
                                                                'card_border_width',
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="w-full rounded border border-border bg-white px-3 py-2"
                                                    >
                                                        <option value="1">1px</option>
                                                        <option value="2">2px</option>
                                                        <option value="4">4px</option>
                                                        <option value="8">8px</option>
                                                    </select>
                                                </div>
                                            )}
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Sombra
                                                </label>
                                                <select
                                                    value={card.card_shadow || 'none'}
                                                    onChange={e =>
                                                        updateCard(
                                                            ci,
                                                            'card_shadow',
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="none">Sin sombra</option>
                                                    <option value="sm">Suave</option>
                                                    <option value="md">Mediana</option>
                                                    <option value="lg">Grande</option>
                                                    <option value="xl">Muy grande</option>
                                                </select>
                                            </div>
                                        </div>

                                        {card.card_border && card.card_border !== 'none' && (
                                            <ColorPickerField
                                                label="Color borde"
                                                value={card.card_border_color || '#e5e7eb'}
                                                onChange={v =>
                                                    updateCard(ci, 'card_border_color', v)
                                                }
                                            />
                                        )}

                                        <div className="grid gap-3 md:grid-cols-2">
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Redondeo
                                                </label>
                                                <select
                                                    value={card.card_radius || 'lg'}
                                                    onChange={e =>
                                                        updateCard(
                                                            ci,
                                                            'card_radius',
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="none">Sin redondeo</option>
                                                    <option value="sm">4px</option>
                                                    <option value="md">8px</option>
                                                    <option value="lg">12px</option>
                                                    <option value="xl">16px</option>
                                                    <option value="2xl">24px</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label className="mb-1 block text-sm font-medium">
                                                    Padding interior
                                                </label>
                                                <select
                                                    value={card.card_padding || 'md'}
                                                    onChange={e =>
                                                        updateCard(
                                                            ci,
                                                            'card_padding',
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded border border-border bg-white px-3 py-2"
                                                >
                                                    <option value="none">Sin padding</option>
                                                    <option value="sm">Pequeño</option>
                                                    <option value="md">Mediano</option>
                                                    <option value="lg">Grande</option>
                                                    <option value="xl">Extra grande</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}

                        <button
                            type="button"
                            onClick={() => {
                                const newCard = {
                                    type: 'text',
                                    image: null,
                                    image_position: 'top',
                                    image_aspect: 'photo',
                                    icon: '',
                                    icon_color: '#1a2236',
                                    title: 'Nueva tarjeta',
                                    title_size: 'lg',
                                    title_color: '',
                                    subtitle: '',
                                    subtitle_color: '',
                                    body: '',
                                    body_color: '',
                                    cta_label: '',
                                    cta_url: '',
                                    text_align: 'left',
                                    card_bg: '#ffffff',
                                    card_border: 'solid',
                                    card_border_width: '1',
                                    card_border_color: '#e5e7eb',
                                    card_radius: 'lg',
                                    card_shadow: 'md',
                                    card_padding: 'md',
                                };
                                const newIdx = (templateData.cards || []).length;
                                updateTemplateData('cards', [
                                    ...(templateData.cards || []),
                                    newCard,
                                ]);
                                setExpandedCardIndices(prev => ({ ...prev, [newIdx]: true }));
                            }}
                            className="w-full rounded-md border border-dashed border-border bg-white px-3 py-2.5 text-xs font-semibold text-primary hover:border-primary hover:bg-primary/5"
                        >
                            + Añadir tarjeta
                        </button>
                    </div>
                )}

                {templateKey !== 'rich_text' && (
                    <div className="rounded-lg border border-border bg-surface shadow-sm">
                        <button
                            type="button"
                            onClick={() => setJsonExpanded(v => !v)}
                            className="flex w-full items-center justify-between gap-3 px-4 py-3"
                        >
                            <div className="flex items-center gap-2">
                                <svg
                                    viewBox="0 0 16 16"
                                    fill="currentColor"
                                    className={`h-3.5 w-3.5 text-text-secondary transition-transform ${jsonExpanded ? 'rotate-90' : ''}`}
                                >
                                    <path
                                        d="M6 4l4 4-4 4"
                                        stroke="currentColor"
                                        strokeWidth="1.5"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        fill="none"
                                    />
                                </svg>
                                <h4 className="text-sm font-semibold text-primary">
                                    JSON de la plantilla
                                </h4>
                                <span className="rounded-full bg-surface px-2 py-0.5 text-[10px] font-semibold text-text-secondary ring-1 ring-border">
                                    avanzado
                                </span>
                            </div>
                            {!jsonExpanded && (
                                <span className="truncate text-xs text-text-secondary">
                                    {Object.keys(templateData)
                                        .filter(k => !k.startsWith('_custom_'))
                                        .slice(0, 3)
                                        .join(', ')}
                                </span>
                            )}
                        </button>

                        {jsonExpanded && (
                            <div className="space-y-3 border-t border-border p-4">
                                <div className="flex justify-end">
                                    <button
                                        type="button"
                                        onClick={applyJsonDraft}
                                        className="inline-flex items-center rounded-md bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:opacity-90"
                                    >
                                        Aplicar JSON
                                    </button>
                                </div>

                                <textarea
                                    value={jsonDraft}
                                    onChange={e => setJsonDraft(e.target.value)}
                                    rows={20}
                                    spellCheck={false}
                                    className="w-full rounded-lg border border-border bg-white px-3 py-3 font-mono text-sm leading-6 text-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                />

                                {jsonError && (
                                    <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                        {jsonError}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                )}

                {templateKey !== 'rich_text' && (
                    <div className="space-y-4 rounded-lg border border-amber-200 bg-amber-50 p-4 shadow-sm">
                        <div>
                            <h4 className="text-sm font-semibold text-amber-800">
                                HTML / CSS personalizado
                            </h4>
                            <p className="mt-0.5 text-xs text-amber-700">
                                El <strong>HTML</strong> reemplaza la salida de la plantilla (deja
                                vacío para usar la plantilla). El <strong>CSS</strong> se inyecta en
                                un <code className="font-mono">&lt;style&gt;</code> antes del bloque
                                y aplica siempre.
                            </p>
                        </div>

                        {/* ── Snippets de inicio rápido ── */}
                        <div className="flex items-center gap-3">
                            <label className="shrink-0 text-xs font-semibold text-amber-800">
                                Insertar snippet:
                            </label>
                            <select
                                defaultValue=""
                                onChange={e => {
                                    if (!e.target.value) return;
                                    const snippet = e.target.value;
                                    updateTemplateData(
                                        '_custom_html',
                                        customHtml ? customHtml + '\n\n' + snippet : snippet,
                                    );
                                    e.target.value = '';
                                }}
                                className="flex-1 rounded border border-amber-300 bg-white px-2 py-1.5 text-xs text-primary focus:outline-none focus:ring-2 focus:ring-amber-400/40"
                            >
                                {HTML_SNIPPETS.map(s => (
                                    <option key={s.label} value={s.value}>
                                        {s.label}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <div className="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <label className="text-sm font-medium text-amber-900">
                                    HTML personalizado
                                </label>
                                <div className="flex flex-wrap items-center gap-3">
                                    {templateData._custom_html && (
                                        <span className="rounded-full bg-amber-200 px-2 py-0.5 text-xs font-semibold text-amber-800">
                                            override activo
                                        </span>
                                    )}
                                    <button
                                        type="button"
                                        onClick={() => embedDialogRef.current?.showModal()}
                                        className="text-xs font-medium text-amber-700 underline underline-offset-2 hover:text-amber-900"
                                    >
                                        Vista previa
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            const cleanData = Object.fromEntries(
                                                Object.entries(templateData).filter(
                                                    ([k]) => !k.startsWith('_custom_'),
                                                ),
                                            );
                                            const generated = buildPreviewHtml(
                                                templateKey,
                                                cleanData,
                                                data.content || '',
                                            );
                                            updateTemplateData('_custom_html', generated);
                                        }}
                                        className="text-xs font-medium text-amber-700 underline underline-offset-2 hover:text-amber-900"
                                    >
                                        Cargar desde plantilla
                                    </button>
                                    {templateData._custom_html && (
                                        <button
                                            type="button"
                                            onClick={() => updateTemplateData('_custom_html', '')}
                                            className="text-xs font-medium text-red-500 underline underline-offset-2 hover:text-red-700"
                                        >
                                            Limpiar
                                        </button>
                                    )}
                                </div>
                            </div>
                            <textarea
                                value={templateData._custom_html || ''}
                                onChange={e => updateTemplateData('_custom_html', e.target.value)}
                                rows={14}
                                spellCheck={false}
                                placeholder="Deja vacío para usar la plantilla generada…"
                                className="w-full rounded-lg border border-amber-300 bg-white px-3 py-3 font-mono text-sm leading-6 text-primary focus:outline-none focus:ring-2 focus:ring-amber-400/40"
                            />

                            {/* ── Advertencia XSS ── */}
                            {htmlHasRiskyContent && (
                                <div className="mt-2 flex items-start gap-2 rounded-md border border-yellow-300 bg-yellow-50 px-3 py-2 text-xs text-yellow-800">
                                    <span className="material-symbols-outlined text-base leading-none text-yellow-600">
                                        warning
                                    </span>
                                    <span>
                                        El HTML contiene{' '}
                                        <strong>scripts o manejadores de eventos</strong>. Asegúrate
                                        de que el código proviene de una fuente de confianza.
                                    </span>
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium text-amber-900">
                                CSS personalizado
                            </label>
                            <textarea
                                value={templateData._custom_css || ''}
                                onChange={e => updateTemplateData('_custom_css', e.target.value)}
                                rows={6}
                                spellCheck={false}
                                placeholder=".cms-block { /* tus estilos */ }"
                                className="w-full rounded-lg border border-amber-300 bg-white px-3 py-3 font-mono text-sm leading-6 text-primary focus:outline-none focus:ring-2 focus:ring-amber-400/40"
                            />
                        </div>

                        {/* ── Modal de vista previa ── */}
                        <dialog
                            ref={embedDialogRef}
                            onClick={e => {
                                if (e.target === embedDialogRef.current)
                                    embedDialogRef.current?.close();
                            }}
                            className="m-auto max-h-[90vh] w-full max-w-3xl rounded-xl border border-border bg-white p-0 shadow-2xl backdrop:bg-black/50"
                        >
                            <div className="flex items-center justify-between border-b border-border px-4 py-3">
                                <span className="text-sm font-semibold text-primary">
                                    Vista previa — HTML personalizado
                                </span>
                                <button
                                    type="button"
                                    onClick={() => embedDialogRef.current?.close()}
                                    className="rounded p-1 text-text-secondary hover:bg-surface hover:text-primary"
                                >
                                    ✕
                                </button>
                            </div>
                            <div className="overflow-auto p-4">
                                <iframe
                                    srcDoc={`<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{margin:0;font-family:sans-serif;}${templateData._custom_css || ''}</style></head><body>${templateData._custom_html || '<p style="color:#888;padding:1rem;">No hay HTML personalizado aún.</p>'}</body></html>`}
                                    sandbox="allow-scripts allow-same-origin"
                                    className="h-[60vh] w-full rounded border border-border bg-white"
                                    title="Preview HTML personalizado"
                                />
                            </div>
                        </dialog>
                    </div>
                )}
            </div>

            {sidebarOpen && (
                <aside className="space-y-4 rounded-lg border border-border bg-surface p-5 shadow-sm lg:sticky lg:top-20 lg:self-start">
                    <div className="flex items-center justify-between gap-2 border-b border-border pb-3">
                        <h4 className="text-sm font-semibold uppercase tracking-wider text-text-secondary">
                            Metadatos
                        </h4>
                        <button
                            type="button"
                            onClick={() => setSidebarOpen(false)}
                            className="rounded p-1 text-text-secondary hover:bg-gray-100 hover:text-primary"
                            title="Cerrar metadatos"
                        >
                            <svg viewBox="0 0 16 16" fill="currentColor" className="h-4 w-4">
                                <path
                                    d="M12 4L4 12M4 4l8 8"
                                    stroke="currentColor"
                                    strokeWidth="1.5"
                                    strokeLinecap="round"
                                />
                            </svg>
                        </button>
                    </div>

                    <div>
                        <h4 className="mb-3 text-sm font-semibold uppercase tracking-wider text-text-secondary">
                            Preview
                        </h4>
                        <BlockPreviewPanel html={previewHtml} blockName={data.name} />
                    </div>

                    <details className="group">
                        <summary className="flex cursor-pointer select-none items-center gap-2 text-xs font-semibold uppercase tracking-wider text-text-secondary hover:text-primary">
                            <svg
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                className="h-3 w-3 transition-transform group-open:rotate-90"
                            >
                                <path
                                    d="M6 4l4 4-4 4"
                                    stroke="currentColor"
                                    strokeWidth="1.5"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    fill="none"
                                />
                            </svg>
                            Template data
                        </summary>
                        <pre className="mt-3 max-h-72 overflow-auto rounded-lg border border-border bg-white p-4 text-xs text-text-secondary">
                            {JSON.stringify(
                                {
                                    template_key: templateKey,
                                    zone: data.zone,
                                    template_data: templateData,
                                },
                                null,
                                2,
                            )}
                        </pre>
                    </details>
                </aside>
            )}

            {!sidebarOpen && (
                <div className="fixed bottom-6 left-6 z-40">
                    <button
                        type="button"
                        onClick={() => setSidebarOpen(true)}
                        className="inline-flex items-center gap-2 rounded-lg border border-primary bg-primary px-3 py-2 text-xs font-semibold text-white shadow-lg transition hover:opacity-90"
                    >
                        <svg viewBox="0 0 16 16" fill="currentColor" className="h-3.5 w-3.5">
                            <path d="M2 4h12v2H2V4zm0 4h12v2H2V8zm0 4h12v2H2v-2z" />
                        </svg>
                        Ver metadatos
                    </button>
                </div>
            )}
        </div>
    );
}
