import type { SelectedMedia } from '@/types/media';

export function buildMediaFrameStyle(data: any, prefix: string): string {
    const radiusMap: Record<string, string> = {
        none: '0',
        sm: '8px',
        md: '12px',
        lg: '20px',
        xl: '16px',
        '2xl': '24px',
        full: '9999px',
    };
    const shadowMap: Record<string, string> = {
        none: 'none',
        sm: '0 1px 4px rgba(0,0,0,0.08)',
        md: '0 4px 12px rgba(0,0,0,0.12)',
        lg: '0 8px 24px rgba(0,0,0,0.15)',
        xl: '0 16px 32px rgba(0,0,0,0.18)',
        '2xl': '0 25px 50px rgba(0,0,0,0.2)',
    };

    const border = data[`${prefix}_border`] || 'none';
    const borderWidth = data[`${prefix}_border_width`] || '1';
    const borderColor = data[`${prefix}_border_color`] || '#e5e7eb';
    const radius = radiusMap[data[`${prefix}_radius`] || 'none'] ?? '0';
    const shadow = shadowMap[data[`${prefix}_shadow`] || 'none'] ?? 'none';

    const style: string[] = [];
    if (border !== 'none') {
        style.push(`border:${borderWidth}px ${border} ${borderColor}`);
    }
    if (radius !== '0') {
        style.push(`border-radius:${radius}`);
        style.push('overflow:hidden');
    }
    if (shadow !== 'none') {
        style.push(`box-shadow:${shadow}`);
    }

    return style.join(';');
}

export function defaultTemplateData(templateKey: string) {
    if (templateKey === 'editorial_split_showcase') {
        return {
            eyebrow: 'CONOCE NUESTRA GESTIÓN',
            title: 'Alcaldía de La Unión Sur: Gestión Transformadora',
            descriptions: [
                {
                    subtitle: 'Innovación con visión',
                    description:
                        'Victoria Gutiérrez lidera con visión e innovación, impulsando proyectos que transforman la ciudad.',
                    icon: 'rocket_launch',
                    icon_position: 'left',
                },
                {
                    subtitle: 'Gestión para todos',
                    description:
                        'Su gestión moderniza infraestructuras y fortalece la participación ciudadana para un desarrollo real.',
                    icon: 'groups',
                    icon_position: 'right',
                },
            ],
            cta_label: 'MÁS INFORMACIÓN',
            cta_url: '/about',
            badge_text: 'PRIMERA ALCALDESA DE LA UNIÓN SUR',
            main_image: null,
            secondary_image: null,
            main_image_border: 'none',
            main_image_border_width: '1',
            main_image_border_color: '#e5e7eb',
            main_image_radius: 'xl',
            main_image_shadow: 'xl',
            secondary_image_border: 'none',
            secondary_image_border_width: '1',
            secondary_image_border_color: '#e5e7eb',
            secondary_image_radius: 'lg',
            secondary_image_shadow: 'lg',
            bg: 'white',
            image_side: 'left',
            title_color: '',
            description_color: '',
        };
    }

    if (templateKey === 'value_cards_row') {
        const defaultCard = (icon: string, title: string, description: string) => ({
            icon,
            title,
            description,
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
        });
        return {
            cards: [
                defaultCard(
                    'person',
                    'Liderazgo Inspirador',
                    'La alcaldesa actúa con compromiso y lidera con inspiración.',
                ),
                defaultCard(
                    'verified',
                    'Gestión Eficiente',
                    'Optimiza recursos y moderniza la ciudad para el progreso.',
                ),
                defaultCard(
                    'volunteer_activism',
                    'Compromiso Social',
                    'Impulsa proyectos que fortalecen el tejido social.',
                ),
            ],
            bg: 'white',
            bg_image: null,
            overlay_opacity: 0,
        };
    }

    if (templateKey === 'stat_row') {
        const defaultStat = (number: string, suffix: string, label: string, icon: string) => ({
            number,
            suffix,
            label,
            icon,
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
        });
        return {
            eyebrow: '',
            bg: 'white',
            bg_image: null,
            overlay_opacity: 0,
            stats: [
                defaultStat('3,200', '+', 'Ciudadanos atendidos', 'groups'),
                defaultStat('12', '', 'Proyectos terminados', 'fact_check'),
                defaultStat('98', '%', 'Satisfacción ciudadana', 'volunteer_activism'),
            ],
        };
    }

    if (templateKey === 'cta_banner') {
        return {
            title: '¿Necesitas ayuda?',
            subtitle: 'Contáctanos y te orientamos con los trámites disponibles.',
            cta_primary_label: 'Contactar ahora',
            cta_primary_url: '/contact',
            cta_secondary_label: '',
            cta_secondary_url: '',
            bg: 'primary',
            alignment: 'split',
            bg_image: null,
            fg_image: null,
            fg_image_border: 'none',
            fg_image_border_width: '1',
            fg_image_border_color: '#e5e7eb',
            fg_image_radius: 'xl',
            fg_image_shadow: 'lg',
            overlay_opacity: 60,
            title_color: '',
            subtitle_color: '',
        };
    }

    if (templateKey === 'hero_section') {
        return {
            eyebrow: 'ALCALDÍA DE LA UNIÓN SUR',
            title: 'Transformando nuestra ciudad',
            subtitle: 'Una gestión comprometida con el bienestar de cada ciudadano.',
            badge_text: '',
            cta_label: 'Conoce más',
            cta_url: '/about',
            height: 'md',
            overlay_opacity: 50,
            bg_image: null,
            bg: 'primary',
            text_align: 'left',
            overlay_color: 'black',
            fg_image: null,
            fg_position: 'right',
            fg_image_border: 'none',
            fg_image_border_width: '1',
            fg_image_border_color: '#e5e7eb',
            fg_image_radius: 'xl',
            fg_image_shadow: '2xl',
            eyebrow_color: '',
            title_color: '',
            subtitle_color: '',
        };
    }

    if (templateKey === 'image_gallery') {
        const defaultItem = () => ({
            image: null,
            caption: '',
            caption_color: '',
            card_bg: '#e5e7eb',
            card_border: 'none',
            card_border_width: '1',
            card_border_color: '#e5e7eb',
            card_radius: 'md',
            card_shadow: 'none',
        });
        return {
            title: '',
            cols: '3',
            aspect: 'photo',
            items: [defaultItem(), defaultItem(), defaultItem()],
        };
    }

    if (templateKey === 'timeline') {
        return {
            title: 'Hitos de la gestión',
            items: [
                {
                    date: '2021',
                    title: 'Inicio de gestión',
                    description:
                        'Victoria Gutiérrez asumió como primera alcaldesa de La Unión Sur.',
                    icon: 'flag',
                    icon_color: '#1a2236',
                    title_color: '',
                    date_color: '',
                    description_color: '',
                    accent_color: '#1a2236',
                },
                {
                    date: '2022',
                    title: 'Renovación del parque central',
                    description:
                        'Inauguración del parque renovado con nuevas instalaciones deportivas.',
                    icon_color: '#1a2236',
                    title_color: '',
                    date_color: '',
                    description_color: '',
                    accent_color: '#1a2236',
                    icon: 'park',
                },
                {
                    date: '2023',
                    title: 'Modernización digital',
                    description: 'Lanzamiento del portal de trámites en línea.',
                    icon: 'computer',
                    icon_color: '#1a2236',
                    title_color: '',
                    date_color: '',
                    description_color: '',
                    accent_color: '#1a2236',
                },
            ],
            bg: 'white',
            bg_image: null,
            overlay_opacity: 0,
        };
    }

    if (templateKey === 'quote_highlight') {
        return {
            quote: 'Trabajamos cada día para que La Unión Sur sea una ciudad más justa, segura y próspera para todos.',
            author_name: 'Victoria Gutiérrez',
            author_title: 'Alcaldesa de La Unión Sur',
            author_image: null,
            bg: 'surface',
            bg_image: null,
            overlay_opacity: 0,
            quote_color: '',
            author_color: '',
            author_image_border: 'none',
            author_image_border_width: '1',
            author_image_border_color: '#e5e7eb',
            author_image_radius: 'full',
            author_image_shadow: 'none',
        };
    }

    if (templateKey === 'configurable_cards') {
        const defaultCard = {
            type: 'text',
            image: null,
            image_position: 'top',
            image_aspect: 'photo',
            icon: '',
            icon_color: '#1a2236',
            title: '',
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
        return {
            section_title: '',
            section_subtitle: '',
            bg: 'white',
            bg_image: null,
            overlay_opacity: 0,
            cols: 3,
            gap: 'md',
            cards: [
                { ...defaultCard, title: 'Primera tarjeta', icon: 'verified' },
                { ...defaultCard, title: 'Segunda tarjeta', icon: 'lightbulb' },
                { ...defaultCard, title: 'Tercera tarjeta', icon: 'handshake' },
            ],
        };
    }

    if (templateKey === 'article_sections') {
        return {
            sections: [
                { type: 'text', content: '<p>Escribe el contenido de tu artículo aquí.</p>' },
            ],
        };
    }

    return {};
}

export function escapeHtml(value: string) {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

export function mediaToSelected(value: any): SelectedMedia[] | null {
    if (!value) return null;
    if (Array.isArray(value)) return value;

    const url = value.url || value.path || '';
    if (!url) return null;

    return [
        {
            media_id: value.media_id || value.id || 0,
            filename: value.filename || String(url).split('/').pop() || 'image',
            path: value.path || url,
            url,
            caption: value.caption || null,
            alt_text: value.alt_text || value.alt || null,
            is_primary: true,
            position: 0,
        },
    ];
}

export function mediaUrl(value: any): string {
    if (!value) return '';
    if (typeof value === 'string') return value;

    const candidate = value.url || value.path || '';
    if (!candidate) return '';

    if (
        candidate.startsWith('http://') ||
        candidate.startsWith('https://') ||
        candidate.startsWith('/')
    ) {
        return candidate;
    }

    return `/storage/${candidate}`;
}

export function formatJson(value: any): string {
    return JSON.stringify(value ?? {}, null, 2);
}

export function parseJson(
    value: string,
): { ok: true; data: Record<string, any> } | { ok: false; error: string } {
    try {
        const parsed = JSON.parse(value);

        if (!parsed || Array.isArray(parsed) || typeof parsed !== 'object') {
            return {
                ok: false,
                error: 'El JSON debe ser un objeto con claves de plantilla.',
            };
        }

        return { ok: true, data: parsed };
    } catch (error) {
        return {
            ok: false,
            error: error instanceof Error ? error.message : 'JSON inválido',
        };
    }
}

export function normalizeEditorialDescriptions(data: any): Array<{
    subtitle: string;
    description: string;
    icon: string;
    icon_position: 'left' | 'right';
}> {
    const sourceItems = Array.isArray(data?.descriptions) ? data.descriptions : [];

    const fallbackItems = sourceItems.length
        ? sourceItems
        : [
            {
                subtitle: 'Descripción 1',
                description: data?.description_1 || '',
                icon: '',
                icon_position: 'left',
            },
            {
                subtitle: 'Descripción 2',
                description: data?.description_2 || '',
                icon: '',
                icon_position: 'left',
            },
        ].filter(item => item.description);

    return fallbackItems.map((item: any, index: number) => ({
        subtitle: String(item?.subtitle ?? item?.title ?? `Descripción ${index + 1}`),
        description: String(item?.description ?? item?.body ?? ''),
        icon: String(item?.icon ?? ''),
        icon_position: item?.icon_position === 'right' ? 'right' : 'left',
    }));
}

export function buildPreviewHtml(templateKey: string, data: any, content: string) {
    const customCss = data._custom_css ? `<style>\n${data._custom_css}\n</style>\n` : '';

    if (data._custom_html) {
        return customCss + data._custom_html;
    }

    const wrap = (html: string) => applyCardWrapperPreview(html, data);

    if (templateKey === 'editorial_split_showcase') {
        const mainImage = mediaUrl(data.main_image);
        const secondaryImage = mediaUrl(data.secondary_image);
        const imageSide = (data.image_side || 'left') === 'right' ? 'right' : 'left';
        const mainImageStyle = buildMediaFrameStyle(data, 'main_image');
        const secondaryImageStyle = buildMediaFrameStyle(data, 'secondary_image');
        const descriptions = normalizeEditorialDescriptions(data);
        const resolveColor = (v: string) => {
            if (v === 'primary') return '#1a2236';
            if (v === 'secondary') return '#f59e0b';
            if (v === 'surface') return '#f5f6fa';
            if (v === 'white') return '#ffffff';
            return v;
        };
        const titleColor = resolveColor(data.title_color || '') || '#111827';
        const descColor = resolveColor(data.description_color || '') || '#6b7280';
        const bgStyle =
            data.bg === 'primary'
                ? 'background:#1a2236;color:white'
                : data.bg === 'surface'
                    ? 'background:#f5f6fa'
                    : 'background:white';
        const gridCols = imageSide === 'right' ? '0.95fr 1.05fr' : '1.05fr 0.95fr';

        const imageCol = `<div style="position:relative;display:flex;min-height:360px;align-items:flex-start;gap:1rem">
            ${mainImage ? `<div style="display:inline-block;height:360px;width:280px;${mainImageStyle}"><img src="${escapeHtml(mainImage)}" alt="${escapeHtml(data.main_image?.alt_text || data.main_image?.filename || '')}" style="height:100%;width:100%;object-fit:cover" /></div>` : ''}
            ${secondaryImage ? `<div style="display:inline-block;margin-top:2.5rem;height:220px;width:200px;${secondaryImageStyle}"><img src="${escapeHtml(secondaryImage)}" alt="${escapeHtml(data.secondary_image?.alt_text || data.secondary_image?.filename || '')}" style="height:100%;width:100%;object-fit:cover" /></div>` : ''}
            ${data.badge_text ? `<div style="position:absolute;right:0;top:1rem;border-radius:1.5rem;background:#f59e0b;padding:1rem 1.25rem;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em">${escapeHtml(data.badge_text)}</div>` : ''}
        </div>`;

        const contentCol = `<div style="display:flex;flex-direction:column;gap:1rem">
            ${data.eyebrow ? `<p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.22em;color:#f59e0b;margin:0">${escapeHtml(data.eyebrow)}</p>` : ''}
            ${data.title ? `<h2 style="font-size:2.25rem;font-weight:900;line-height:1.15;margin:0;color:${titleColor}">${escapeHtml(data.title)}</h2>` : ''}
            ${descriptions
                .map((item: any) => {
                    const isRight = item.icon_position === 'right';
                    const iconHtml = item.icon
                        ? `<div style="display:flex;height:40px;width:40px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;background:rgba(245,158,11,0.12);color:#f59e0b"><span class="material-symbols-outlined" style="font-size:20px;line-height:1">${escapeHtml(item.icon)}</span></div>`
                        : '';
                    const textHtml = `<div style="flex:1;display:flex;flex-direction:column;gap:0.35rem;align-items:flex-start">
                        ${item.subtitle ? `<p style="margin:0;font-size:0.85rem;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:${titleColor}">${escapeHtml(item.subtitle)}</p>` : ''}
                        ${item.description ? `<p style="margin:0;font-size:0.95rem;line-height:1.8;color:${descColor}">${escapeHtml(item.description)}</p>` : ''}
                    </div>`;

                    return `<div style="display:flex;gap:1rem;align-items:flex-start;flex-direction:${isRight ? 'row-reverse' : 'row'};padding-top:0.5rem;padding-bottom:0.5rem">${iconHtml}${textHtml}</div>`;
                })
                .join('')}
            ${data.cta_label ? `<a href="${escapeHtml(data.cta_url || '#')}" style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.18em;text-decoration:none;color:inherit">${escapeHtml(data.cta_label)}</a>` : ''}
        </div>`;

        return wrap(
            customCss +
            `<section style="padding:4rem 2rem;${bgStyle}">
                <div style="display:grid;grid-template-columns:${gridCols};gap:3rem;align-items:center;max-width:1200px;margin:0 auto">
                    ${imageSide === 'right' ? contentCol + imageCol : imageCol + contentCol}
                </div>
            </section>`,
        );
    }

    if (templateKey === 'value_cards_row') {
        const cards = Array.isArray(data.cards) ? data.cards : [];
        const solidBg =
            data.bg === 'primary'
                ? 'background:#1a2236;color:white'
                : data.bg === 'surface'
                    ? 'background:#f5f6fa'
                    : 'background:white';
        const bgImg = mediaUrl(data.bg_image);
        const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 0)));
        const sectionStyle = bgImg
            ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;`
            : solidBg + ';';
        const overlayHtml = bgImg
            ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
            : '';
        const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';

        return wrap(
            customCss +
            `<section style="${sectionStyle}padding:3rem 0">
                ${overlayHtml}
                <div style="${contentStyle}display:grid;grid-template-columns:repeat(${cards.length || 3},1fr);gap:1rem;padding:0 1rem">
                    ${cards.map((card: any) => buildCardPreview({ ...card, body: card.body || card.description })).join('')}
                </div>
            </section>`,
        );
    }

    if (templateKey === 'stat_row') return wrap(buildStatRowPreview(data, customCss));
    if (templateKey === 'cta_banner') return wrap(buildCtaBannerPreview(data, customCss));
    if (templateKey === 'hero_section') return wrap(buildHeroSectionPreview(data, customCss));
    if (templateKey === 'image_gallery') return wrap(buildImageGalleryPreview(data, customCss));
    if (templateKey === 'timeline') return wrap(buildTimelinePreview(data, customCss));
    if (templateKey === 'quote_highlight') return wrap(buildQuoteHighlightPreview(data, customCss));
    if (templateKey === 'configurable_cards')
        return wrap(buildConfigurableCardsPreview(data, customCss));
    if (templateKey === 'article_sections') return buildArticleSectionsPreview(data);

    return wrap(customCss + content);
}

export function buildArticleSectionCardPreview(s: any): string {
    const variant = s.card_variant || 'content';
    const sz: Record<string, string> = {
        sm: '0.875rem',
        base: '1rem',
        lg: '1.125rem',
        xl: '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
    };
    const wt: Record<string, string> = {
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
        extrabold: '800',
        black: '900',
    };
    const lh: Record<string, string> = {
        tight: '1.25',
        snug: '1.375',
        normal: '1.5',
        relaxed: '1.65',
        loose: '2',
    };
    const rr: Record<string, string> = {
        none: '0',
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        full: '9999px',
    };
    const shMap: Record<string, string> = {
        none: '',
        sm: '0 1px 2px rgba(0,0,0,0.05)',
        md: '0 4px 6px rgba(0,0,0,0.07)',
        lg: '0 10px 15px rgba(0,0,0,0.1)',
        xl: '0 20px 25px rgba(0,0,0,0.1)',
    };
    const pdMap: Record<string, string> = {
        none: '',
        sm: '0.75rem',
        md: '1.25rem',
        lg: '2rem',
        xl: '3rem',
    };
    const radMap: Record<string, string> = {
        none: '',
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '24px',
    };

    const styles: string[] = [];
    if (s.card_bg) styles.push(`background:${s.card_bg}`);
    if (s.card_border && s.card_border !== 'none')
        styles.push(
            `border:${s.card_border_width || 1}px ${s.card_border} ${s.card_border_color || '#e5e7eb'}`,
        );
    if (radMap[s.card_radius])
        styles.push(`border-radius:${radMap[s.card_radius]};overflow:hidden`);
    if (shMap[s.card_shadow]) styles.push(`box-shadow:${shMap[s.card_shadow]}`);
    if (pdMap[s.card_padding]) styles.push(`padding:${pdMap[s.card_padding]}`);
    if (s.card_align) styles.push(`text-align:${s.card_align}`);
    const maxW =
        s.card_width === 'wide'
            ? 'max-width:900px;margin-left:auto;margin-right:auto;'
            : s.card_width === 'medium'
                ? 'max-width:640px;margin-left:auto;margin-right:auto;'
                : s.card_width === 'narrow'
                    ? 'max-width:480px;margin-left:auto;margin-right:auto;'
                    : '';
    const wrapStyle = maxW + styles.join(';');

    const titleHtml =
        s.title && variant !== 'text_only'
            ? `<h3 style="margin:0 0 0.5rem;font-size:${sz[s.title_size || 'xl'] || '1.25rem'};font-weight:${wt[s.title_weight || 'bold'] || '700'};font-style:${s.title_italic ? 'italic' : 'normal'};line-height:${lh[s.title_line_height || 'tight'] || '1.25'};color:${s.title_color || '#111827'}">${escapeHtml(s.title)}</h3>`
            : '';
    const subtitleHtml =
        s.subtitle && ['content', 'image_text'].includes(variant)
            ? `<p style="margin:0 0 0.75rem;font-size:${sz[s.subtitle_size || 'sm'] || '0.875rem'};font-weight:${wt[s.subtitle_weight || 'medium'] || '500'};font-style:${s.subtitle_italic ? 'italic' : 'normal'};color:${s.subtitle_color || '#6b7280'}">${escapeHtml(s.subtitle)}</p>`
            : '';
    const parasHtml = (Array.isArray(s.paragraphs) ? s.paragraphs : [])
        .map(
            (p: any) =>
                `<p style="margin:0 0 0.75rem;font-size:${sz[p.size || 'base'] || '1rem'};font-weight:${wt[p.weight || 'normal'] || '400'};font-style:${p.italic ? 'italic' : 'normal'};line-height:${lh[p.line_height || 'relaxed'] || '1.65'};color:${p.color || '#374151'}">${escapeHtml(p.text || '')}</p>`,
        )
        .join('');

    let inner = '';
    if (variant === 'quote') {
        const accent = s.quote_accent_color || '#f59e0b';
        const qsize = sz[s.quote_size || 'xl'] || '1.25rem';
        const qcolor = s.quote_text_color || '#1a2236';
        const acolor = s.quote_author_color || '#f59e0b';
        const qtext = s.quote_text || '';
        const qauthor = s.quote_author
            ? `<footer style="margin-top:0.6rem;font-size:0.85rem;font-weight:700;color:${acolor}">— ${escapeHtml(s.quote_author)}${s.quote_org ? `<span style="font-weight:400;color:#6b7280">, ${escapeHtml(s.quote_org)}</span>` : ''}</footer>`
            : '';
        if (!qtext) {
            inner = '<p style="color:#9ca3af;font-size:0.85rem">Sin texto de cita.</p>';
        } else if ((s.quote_style || 'border_left') === 'quotes') {
            inner = `<div style="position:relative;padding:1.5rem 1.5rem 1rem"><span style="position:absolute;top:0;left:0;font-size:4rem;line-height:1;color:${accent};opacity:0.25;font-family:Georgia,serif">"</span><p style="font-size:${qsize};font-style:italic;font-weight:600;color:${qcolor};line-height:1.7;margin:0;position:relative">${escapeHtml(qtext)}</p>${qauthor}</div>`;
        } else if ((s.quote_style || 'border_left') === 'minimal') {
            inner = `<div><p style="font-size:${qsize};font-style:italic;color:${qcolor};line-height:1.7;margin:0">${escapeHtml(qtext)}</p>${qauthor}</div>`;
        } else {
            inner = `<blockquote style="border-left:4px solid ${accent};padding:0.5rem 0 0.5rem 1.25rem;margin:0"><p style="font-size:${qsize};font-style:italic;font-weight:600;color:${qcolor};line-height:1.7;margin:0">${escapeHtml(qtext)}</p>${qauthor}</blockquote>`;
        }
    } else if (variant === 'image_text') {
        const imgUrl = mediaUrl(s.image);
        const pos = s.image_position || 'right';
        const iw = s.image_width || '40%';
        const ir = rr[s.image_radius || 'md'] || '8px';
        const ap =
            s.image_aspect === 'square'
                ? '100%'
                : s.image_aspect === 'video'
                    ? '56.25%'
                    : s.image_aspect === 'auto'
                        ? undefined
                        : '75%';
        const imgEl = imgUrl
            ? ap
                ? `<div style="position:relative;padding-bottom:${ap};overflow:hidden;border-radius:${ir}"><img src="${escapeHtml(imgUrl)}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" /></div>`
                : `<img src="${escapeHtml(imgUrl)}" alt="" style="width:100%;border-radius:${ir};display:block" />`
            : `<div style="background:#e5e7eb;padding-bottom:75%;border-radius:${ir};display:flex;align-items:center;justify-content:center"><span style="color:#9ca3af;font-size:0.8rem">Sin imagen</span></div>`;
        const textEl = `<div>${titleHtml}${subtitleHtml}${parasHtml}</div>`;
        if (pos === 'top' || pos === 'bottom') {
            inner =
                pos === 'top'
                    ? `${imgEl}<div style="padding-top:1rem">${textEl}</div>`
                    : `${textEl}<div style="padding-top:1rem">${imgEl}</div>`;
        } else {
            inner = `<div style="display:flex;gap:1.5rem;align-items:flex-start${pos === 'left' ? '' : ';flex-direction:row-reverse'}"><div style="flex-shrink:0;width:${iw}">${imgEl}</div><div style="flex:1">${textEl}</div></div>`;
        }
    } else if (variant === 'gallery_card') {
        const imgs = Array.isArray(s.gallery_images) ? s.gallery_images : [];
        const cols = parseInt(s.gallery_cols || '2');
        const ap =
            s.gallery_aspect === 'square'
                ? '100%'
                : s.gallery_aspect === 'video'
                    ? '56.25%'
                    : '75%';
        if (imgs.length === 0) {
            inner = '<p style="color:#9ca3af;font-size:0.85rem">Sin imágenes seleccionadas.</p>';
        } else {
            inner = `<div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:0.5rem">${imgs
                .map((img: any) => {
                    const u = mediaUrl(img);
                    return u
                        ? `<div style="position:relative;padding-bottom:${ap};overflow:hidden;border-radius:8px"><img src="${escapeHtml(u)}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" /></div>`
                        : `<div style="background:#e5e7eb;padding-bottom:${ap};border-radius:8px"></div>`;
                })
                .join('')}</div>`;
        }
    } else if (variant === 'bg_card') {
        const bgUrl = mediaUrl(s.bg_image);
        const ov = Math.min(100, Math.max(0, Number(s.bg_overlay ?? 50)));
        const ovc = s.bg_overlay_color || '#000000';
        const content = titleHtml + subtitleHtml + parasHtml;
        inner = bgUrl
            ? `<div style="position:relative;overflow:hidden;min-height:200px;background:url('${escapeHtml(bgUrl)}') center/cover no-repeat;border-radius:inherit"><div style="position:absolute;inset:0;background:${ovc};opacity:${ov / 100}"></div><div style="position:relative;z-index:1;padding:2rem;color:white">${content}</div></div>`
            : `<div>${content}</div>`;
    } else {
        inner = titleHtml + subtitleHtml + parasHtml;
        if (!inner.trim())
            inner =
                '<p style="color:#9ca3af;font-size:0.85rem">Card vacía. Agrega título o párrafos.</p>';
    }

    return `<div style="${wrapStyle}">${inner}</div>`;
}

export function buildArticleSectionsPreview(data: any): string {
    const sections = Array.isArray(data.sections) ? data.sections : [];
    if (sections.length === 0) {
        return '<div style="padding:2rem;color:#9ca3af;font-size:0.9rem">Sin secciones.</div>';
    }
    const parts = sections.map((s: any) => {
        switch (s.type) {
            case 'text':
                return `<div style="font-size:1rem;line-height:1.85;color:#374151;margin-bottom:1.25rem">${s.content || ''}</div>`;
            case 'image': {
                const url = mediaUrl(s.image);
                if (!url)
                    return '<div style="background:#e5e7eb;height:120px;border-radius:12px;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:center;font-size:0.8rem;color:#9ca3af">Sin imagen</div>';
                const maxW = s.width === 'medium' ? '600px' : s.width === 'wide' ? '900px' : '100%';
                return `<figure style="margin:0 0 1.25rem;max-width:${maxW};${s.align === 'left' ? 'margin-right:auto' : s.align === 'right' ? 'margin-left:auto' : 'margin-left:auto;margin-right:auto'}">
                    <img src="${escapeHtml(url)}" alt="${escapeHtml(s.caption || '')}" style="width:100%;border-radius:12px;object-fit:cover;display:block" />
                    ${s.caption ? `<figcaption style="text-align:center;font-size:0.78rem;color:#9ca3af;margin-top:6px">${escapeHtml(s.caption)}</figcaption>` : ''}
                </figure>`;
            }
            case 'video': {
                const u = s.url || '';
                let embedUrl = u;
                const ytMatch = u.match(
                    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]+)/,
                );
                if (ytMatch) embedUrl = `https://www.youtube.com/embed/${ytMatch[1]}`;
                if (!embedUrl) return '';
                return `<figure style="margin:0 0 1.25rem">
                    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px">
                        <iframe src="${escapeHtml(embedUrl)}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0" allowfullscreen></iframe>
                    </div>
                    ${s.caption ? `<figcaption style="text-align:center;font-size:0.78rem;color:#9ca3af;margin-top:6px">${escapeHtml(s.caption)}</figcaption>` : ''}
                </figure>`;
            }
            case 'quote':
                if (!s.text) return '';
                return `<blockquote style="border-left:4px solid #f59e0b;padding:0.5rem 0 0.5rem 1.25rem;margin:0 0 1.25rem">
                    <p style="font-size:1.1rem;font-style:italic;font-weight:600;color:#1a2236;line-height:1.7;margin:0">${escapeHtml(s.text)}</p>
                    ${s.author_name ? `<footer style="margin-top:0.6rem;font-size:0.85rem;font-weight:700;color:#f59e0b">${escapeHtml(s.author_name)}${s.author_title ? `<span style="font-weight:400;color:#6b7280">, ${escapeHtml(s.author_title)}</span>` : ''}</footer>` : ''}
                </blockquote>`;
            case 'callout': {
                if (!s.title && !s.body) return '';
                const [bg, border, text, icon] =
                    s.callout_type === 'warning'
                        ? ['#fffbeb', '#f59e0b', '#92400e', '⚠️']
                        : s.callout_type === 'success'
                            ? ['#f0fdf4', '#22c55e', '#14532d', '✅']
                            : ['#eff6ff', '#3b82f6', '#1e3a8a', 'ℹ️'];
                return `<div style="border-left:4px solid ${border};background:${bg};color:${text};padding:1rem 1.25rem;border-radius:0 8px 8px 0;margin-bottom:1.25rem">
                    ${s.title ? `<p style="margin:0 0 4px;font-size:0.9rem;font-weight:700">${icon} ${escapeHtml(s.title)}</p>` : ''}
                    ${s.body ? `<p style="margin:0;font-size:0.875rem;line-height:1.6">${escapeHtml(s.body)}</p>` : ''}
                </div>`;
            }
            case 'separator':
                if (s.style === 'dots')
                    return '<div style="display:flex;justify-content:center;gap:0.5rem;margin:2rem 0"><span style="width:6px;height:6px;border-radius:50%;background:#f59e0b;opacity:0.5;display:inline-block"></span><span style="width:6px;height:6px;border-radius:50%;background:#f59e0b;opacity:0.5;display:inline-block"></span><span style="width:6px;height:6px;border-radius:50%;background:#f59e0b;opacity:0.5;display:inline-block"></span></div>';
                if (s.style === 'space') return '<div style="height:3rem"></div>';
                return '<hr style="border:none;border-top:1px solid #e5e7eb;margin:2rem 0" />';
            case 'gallery': {
                const gallery = {
                    cols: s.cols || '3',
                    aspect: s.aspect || 'photo',
                    items: Array.isArray(s.images)
                        ? s.images.map((im: any) => ({ image: im, caption: im?.caption || '' }))
                        : [],
                    title: s.caption || '',
                };
                return buildImageGalleryPreview(gallery, '');
            }
            case 'embed': {
                if (s.html && s.html.trim()) {
                    return `<figure style="margin:0 0 1.25rem">${s.html}${s.caption ? `<figcaption style="text-align:center;font-size:0.78rem;color:#9ca3af;margin-top:6px">${escapeHtml(s.caption)}</figcaption>` : ''}</figure>`;
                }
                const u = s.url || '';
                let embedUrl = u;
                const ytMatch = u.match(
                    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]+)/,
                );
                if (ytMatch) embedUrl = `https://www.youtube.com/embed/${ytMatch[1]}`;
                if (!embedUrl) return '';
                return `<figure style="margin:0 0 1.25rem"><div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px"><iframe src="${escapeHtml(embedUrl)}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>${s.caption ? `<figcaption style="text-align:center;font-size:0.78rem;color:#9ca3af;margin-top:6px">${escapeHtml(s.caption)}</figcaption>` : ''}</figure>`;
            }
            case 'card':
                return buildArticleSectionCardPreview(s);
            default:
                return '';
        }
    });
    // apply per-section block wrapper styles
    const wrapped = parts.map((p: string, i: number) =>
        applySectionBlockWrapperPreview(p, sections[i] || {}),
    );
    return `<div style="padding:2rem;max-width:780px">${wrapped.join('')}</div>`;
}

export function applySectionBlockWrapperPreview(html: string, s: any): string {
    if (!s) return html;
    const resolveColor = (v: string) => {
        if (!v) return '';
        if (v === 'primary') return '#1a2236';
        if (v === 'secondary') return '#f59e0b';
        if (v === 'surface') return '#f5f6fa';
        if (v === 'white') return '#ffffff';
        return v;
    };

    const styles: string[] = [];
    const bg = s.block_bg || '';
    if (bg && bg !== 'transparent') styles.push(`background:${resolveColor(bg)}`);

    if (s.block_border && s.block_border !== 'none') {
        const bw = s.block_border_width || '1';
        const bcolor = resolveColor(s.block_border_color || '#e5e7eb');
        styles.push(`border:${bw}px ${s.block_border} ${bcolor}`);
    }

    const radiusMap: Record<string, string> = {
        none: '',
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '24px',
    };
    if (radiusMap[s.block_radius]) {
        styles.push(`border-radius:${radiusMap[s.block_radius]}`);
        if (radiusMap[s.block_radius]) styles.push('overflow:hidden');
    }

    const shadowMap: Record<string, string> = {
        none: '',
        sm: '0 1px 2px rgba(0,0,0,0.05)',
        md: '0 4px 6px rgba(0,0,0,0.07)',
        lg: '0 10px 15px rgba(0,0,0,0.1)',
        xl: '0 20px 25px rgba(0,0,0,0.1)',
        '2xl': '0 25px 50px rgba(0,0,0,0.12)',
    };
    if (shadowMap[s.block_shadow]) styles.push(`box-shadow:${shadowMap[s.block_shadow]}`);

    const paddingMap: Record<string, string> = {
        none: '',
        sm: '0.75rem',
        md: '1.25rem',
        lg: '2rem',
        xl: '3rem',
    };
    if (paddingMap[s.block_padding]) styles.push(`padding:${paddingMap[s.block_padding]}`);

    if (s.text_align) styles.push(`text-align:${s.text_align}`);
    if (s.text_size === 'lg') styles.push('font-size:1.125rem');
    if (s.text_size === 'xl') styles.push('font-size:1.25rem');

    if (styles.length === 0) return html;
    return `<div style="${styles.join(';')}">${html}</div>`;
}

export function buildStatRowPreview(data: any, customCss: string): string {
    const stats = Array.isArray(data.stats) ? data.stats : [];
    const solidBg =
        data.bg === 'primary'
            ? 'background:#1a2236;color:white'
            : data.bg === 'surface'
                ? 'background:#f5f6fa;color:#1a2236'
                : 'background:white;color:#1a2236';
    const bgImg = mediaUrl(data.bg_image);
    const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 0)));
    const sectionStyle = bgImg
        ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;color:white;`
        : solidBg + ';';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
        : '';
    const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';
    const cols = stats.length || 3;

    const radiusMap: Record<string, string> = {
        none: '0',
        sm: '8px',
        md: '12px',
        lg: '20px',
        full: '9999px',
    };
    const shadowMap: Record<string, string> = {
        none: 'none',
        sm: '0 1px 4px rgba(0,0,0,0.08)',
        md: '0 4px 12px rgba(0,0,0,0.12)',
        lg: '0 8px 24px rgba(0,0,0,0.15)',
    };
    const paddingMap: Record<string, string> = {
        none: '0',
        sm: '0.75rem',
        md: '1.25rem',
        lg: '2rem',
    };

    const items = stats
        .map((s: any) => {
            const bg = s.card_bg && s.card_bg !== 'transparent' ? s.card_bg : 'transparent';
            const border =
                s.card_border === 'solid'
                    ? `${s.card_border_width || 1}px solid ${s.card_border_color || '#e5e7eb'}`
                    : 'none';
            const radius = radiusMap[s.card_radius] ?? '0';
            const shadow = shadowMap[s.card_shadow] ?? 'none';
            const padding = paddingMap[s.card_padding] ?? '0';
            return `
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;text-align:center;background:${bg};border:${border};border-radius:${radius};box-shadow:${shadow};padding:${padding}">
                ${s.icon ? `<span class="material-symbols-outlined" style="font-size:32px;color:${s.icon_color || 'inherit'};opacity:${s.icon_color ? 1 : 0.7}">${escapeHtml(s.icon)}</span>` : ''}
                <p style="font-size:2.5rem;font-weight:900;line-height:1;margin:0;color:${s.number_color || 'inherit'}">${escapeHtml(s.number || '')}<span style="opacity:0.5">${escapeHtml(s.suffix || '')}</span></p>
                <p style="font-size:0.8rem;font-weight:500;opacity:0.6;margin:0;color:${s.label_color || 'inherit'}">${escapeHtml(s.label || '')}</p>
            </div>`;
        })
        .join('');
    return (
        customCss +
        `<section style="padding:4rem 2rem;${sectionStyle}">
        ${overlayHtml}
        <div style="${contentStyle}">
        ${data.eyebrow ? `<p style="text-align:center;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.2em;opacity:0.5;margin-bottom:2rem">${escapeHtml(data.eyebrow)}</p>` : ''}
        <div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:2rem;max-width:900px;margin:0 auto">${items}</div>
        </div>
    </section>`
    );
}

export function buildCtaBannerPreview(data: any, customCss: string): string {
    const isSplit = (data.alignment || 'split') === 'split';
    const solidBg =
        data.bg === 'surface'
            ? 'background:#f5f6fa;color:#1a2236'
            : 'background:#1a2236;color:white';
    const bgImg = mediaUrl(data.bg_image);
    const fgImg = mediaUrl(data.fg_image);
    const fgImageStyle = buildMediaFrameStyle(data, 'fg_image');
    const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 60)));
    const sectionStyle = bgImg
        ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;color:white;`
        : solidBg + ';';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
        : '';
    const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';
    return (
        customCss +
        `<section style="padding:3rem 2rem;${sectionStyle}">
        ${overlayHtml}
        <div style="${contentStyle}max-width:900px;margin:0 auto;display:flex;flex-direction:${isSplit ? 'row' : 'column'};align-items:center;justify-content:space-between;gap:1.5rem;flex-wrap:wrap;text-align:${isSplit ? 'left' : 'center'}">
            <div>
                ${data.title ? `<h2 style="font-size:1.75rem;font-weight:900;margin:0 0 0.5rem">${escapeHtml(data.title)}</h2>` : ''}
                ${data.subtitle ? `<p style="margin:0;opacity:0.75">${escapeHtml(data.subtitle)}</p>` : ''}
            </div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
                ${data.cta_primary_label ? `<a href="${escapeHtml(data.cta_primary_url || '#')}" style="display:inline-flex;align-items:center;background:white;color:#1a2236;border-radius:9999px;padding:0.6rem 1.5rem;font-size:0.85rem;font-weight:700;text-decoration:none">${escapeHtml(data.cta_primary_label)}</a>` : ''}
                ${data.cta_secondary_label ? `<a href="${escapeHtml(data.cta_secondary_url || '#')}" style="display:inline-flex;align-items:center;border:2px solid rgba(255,255,255,0.6);border-radius:9999px;padding:0.6rem 1.5rem;font-size:0.85rem;font-weight:700;text-decoration:none;color:inherit">${escapeHtml(data.cta_secondary_label)}</a>` : ''}
            </div>
            ${fgImg ? `<div style="display:inline-block;width:160px;height:160px;flex-shrink:0;${fgImageStyle}"><img src="${escapeHtml(fgImg)}" alt="fg" style="height:100%;width:100%;object-fit:cover" /></div>` : ''}
        </div>
    </section>`
    );
}

export function buildHeroSectionPreview(data: any, customCss: string): string {
    const heights: Record<string, string> = {
        sm: '200px',
        md: '320px',
        lg: '440px',
        full: '500px',
    };
    const h = heights[data.height || 'md'] || '320px';
    const opacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 50)));
    const bgImg = mediaUrl(data.bg_image);
    const fgImg = mediaUrl(data.fg_image);
    const fgImageStyle = buildMediaFrameStyle(data, 'fg_image');
    const fgPos = (data.fg_position || 'right') === 'left' ? 'left' : 'right';
    const solidBg = (() => {
        const v = data.bg || 'primary';
        if (v === 'primary') return '#1a2236';
        if (v === 'secondary') return '#f59e0b';
        if (v === 'surface') return '#f5f6fa';
        if (v === 'white') return '#ffffff';
        return v;
    })();
    const bgStyle = bgImg
        ? `background:url('${escapeHtml(bgImg)}') center/cover no-repeat;`
        : `background:${solidBg};`;
    const overlayBg =
        data.overlay_color === 'primary'
            ? '#1a2236'
            : data.overlay_color === 'secondary'
                ? '#f59e0b'
                : 'black';
    const textAlignStyle = fgImg
        ? fgPos === 'left'
            ? 'text-align:right;'
            : 'text-align:left;'
        : (data.text_align || 'left') === 'center'
            ? 'text-align:center;'
            : '';
    const flexDir = fgImg && fgPos === 'left' ? 'flex-direction:row-reverse;' : '';
    const flexLayout = fgImg
        ? `display:flex;align-items:center;gap:2rem;${flexDir}`
        : 'max-width:800px;';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:${overlayBg};opacity:${opacity / 100}"></div>`
        : '';
    const posStyle = bgImg ? 'position:relative;overflow:hidden;' : '';
    return (
        customCss +
        `<section style="${posStyle}min-height:${h};display:flex;align-items:center;${bgStyle}">
        ${overlayHtml}
        <div style="position:relative;z-index:1;padding:4rem 2rem;color:white;width:100%;${fgImg ? 'margin:0 auto;max-width:1280px;' : ''}${flexLayout}">
            <div${fgImg ? ' style="flex:1"' : ''}>
                ${data.eyebrow ? `<p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.25em;opacity:0.6;margin:0 0 0.75rem">${escapeHtml(data.eyebrow)}</p>` : ''}
                ${data.badge_text ? `<span style="display:inline-block;background:rgba(255,255,255,0.2);border-radius:9999px;padding:0.25rem 0.75rem;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.75rem">${escapeHtml(data.badge_text)}</span>` : ''}
                ${data.title ? `<h1 style="font-size:3rem;font-weight:900;line-height:1.1;margin:0 0 0.75rem">${escapeHtml(data.title)}</h1>` : ''}
                ${data.subtitle ? `<p style="font-size:1rem;opacity:0.8;margin:0 0 1.5rem;line-height:1.7">${escapeHtml(data.subtitle)}</p>` : ''}
                ${data.cta_label ? `<a href="${escapeHtml(data.cta_url || '#')}" style="display:inline-flex;background:#1a2236;border-radius:9999px;padding:0.6rem 1.5rem;font-size:0.85rem;font-weight:700;text-decoration:none;color:white">${escapeHtml(data.cta_label)}</a>` : ''}
            </div>
            ${fgImg ? `<div style="display:inline-block;flex-shrink:0;height:280px;width:240px;${fgImageStyle}"><img src="${escapeHtml(fgImg)}" alt="fg" style="height:100%;width:100%;object-fit:cover" /></div>` : ''}
        </div>
    </section>`
    );
}

export function buildImageGalleryPreview(data: any, customCss: string): string {
    const cols = parseInt(data.cols || '3', 10);
    const aspectMap: Record<string, string> = { square: '100%', video: '56.25%', photo: '75%' };
    const paddingBottom = aspectMap[data.aspect || 'photo'] || '75%';
    const radiusMap: Record<string, string> = {
        none: '0',
        sm: '8px',
        md: '12px',
        lg: '20px',
        full: '9999px',
    };
    const shadowMap: Record<string, string> = {
        none: 'none',
        sm: '0 1px 4px rgba(0,0,0,0.08)',
        md: '0 4px 12px rgba(0,0,0,0.12)',
        lg: '0 8px 24px rgba(0,0,0,0.15)',
    };
    const items = (data.items || [])
        .map((item: any) => {
            const url = mediaUrl(item.image);
            const bg = item.card_bg || '#e5e7eb';
            const border =
                item.card_border === 'solid'
                    ? `${item.card_border_width || 1}px solid ${item.card_border_color || '#e5e7eb'}`
                    : 'none';
            const radius = radiusMap[item.card_radius] ?? '12px';
            const shadow = shadowMap[item.card_shadow] ?? 'none';
            const captionColor = item.caption_color || '#6b7280';
            return `<figure style="margin:0;overflow:hidden;border-radius:${radius};background:${bg};border:${border};box-shadow:${shadow}">
            ${url
                    ? `<div style="position:relative;padding-bottom:${paddingBottom};overflow:hidden"><img src="${escapeHtml(url)}" alt="${escapeHtml(item.caption || '')}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" /></div>`
                    : `<div style="padding-bottom:${paddingBottom};background:#d1d5db;display:flex;align-items:center;justify-content:center;font-size:0.75rem;color:#6b7280"></div>`
                }
            ${item.caption ? `<figcaption style="padding:0.4rem 0.6rem;font-size:0.7rem;color:${captionColor}">${escapeHtml(item.caption)}</figcaption>` : ''}
        </figure>`;
        })
        .join('');
    return (
        customCss +
        `<section style="padding:3rem 2rem">
        ${data.title ? `<h2 style="font-size:1.5rem;font-weight:900;color:#1a2236;margin:0 0 2rem">${escapeHtml(data.title)}</h2>` : ''}
        <div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:1rem">${items}</div>
    </section>`
    );
}

export function buildTimelinePreview(data: any, customCss: string): string {
    const solidBg =
        data.bg === 'primary'
            ? 'background:#1a2236;color:white'
            : data.bg === 'surface'
                ? 'background:#f5f6fa'
                : 'background:white';
    const bgImg = mediaUrl(data.bg_image);
    const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 0)));
    const sectionStyle = bgImg
        ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;`
        : solidBg + ';';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
        : '';
    const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';
    const items = (data.items || [])
        .map((item: any) => {
            const accentColor = item.accent_color || '#1a2236';
            const iconColor = item.icon_color || '#ffffff';
            const dateColor = item.date_color
                ? `color:${item.date_color}`
                : 'color:#1a2236;opacity:0.5';
            const titleColor = item.title_color || '#111111';
            const descColor = item.description_color || '#6b7280';
            return `
        <div style="position:relative;padding-left:1rem;padding-bottom:2.5rem">
            <span style="position:absolute;left:-1.4rem;top:2px;width:18px;height:18px;border-radius:50%;background:${accentColor};display:flex;align-items:center;justify-content:center">
                ${item.icon ? `<span class="material-symbols-outlined" style="font-size:10px;color:${iconColor}">${escapeHtml(item.icon)}</span>` : ''}
            </span>
            ${item.date ? `<p style="margin:0 0 2px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;${dateColor}">${escapeHtml(item.date)}</p>` : ''}
            ${item.title ? `<h3 style="margin:0 0 4px;font-size:1.1rem;font-weight:900;color:${titleColor}">${escapeHtml(item.title)}</h3>` : ''}
            ${item.description ? `<p style="margin:0;font-size:0.875rem;line-height:1.6;color:${descColor}">${escapeHtml(item.description)}</p>` : ''}
        </div>`;
        })
        .join('');
    return (
        customCss +
        `<section style="padding:3rem 2rem;${sectionStyle}">
        ${overlayHtml}
        <div style="${contentStyle}">
        ${data.title ? `<h2 style="font-size:1.75rem;font-weight:900;margin:0 0 3rem">${escapeHtml(data.title)}</h2>` : ''}
        <div style="position:relative;border-left:2px solid rgba(26,34,54,0.2);padding-left:2rem;margin-left:0.5rem">${items}</div>
        </div>
    </section>`
    );
}

export function buildQuoteHighlightPreview(data: any, customCss: string): string {
    const solidBg =
        data.bg === 'primary'
            ? 'background:#1a2236;color:white'
            : data.bg === 'white'
                ? 'background:white;color:#1a2236'
                : 'background:#f5f6fa;color:#1a2236';
    const bgImg = mediaUrl(data.bg_image);
    const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 0)));
    const sectionStyle = bgImg
        ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;color:white;`
        : solidBg + ';';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
        : '';
    const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';
    const authorImage = mediaUrl(data.author_image);
    const authorImageStyle = buildMediaFrameStyle(data, 'author_image');
    return (
        customCss +
        `<section style="padding:5rem 2rem;text-align:center;${sectionStyle}">
        ${overlayHtml}
        <div style="max-width:700px;margin:0 auto;${contentStyle}">
            <div style="font-size:7rem;line-height:1;opacity:0.1;margin-bottom:-2rem">&ldquo;</div>
            ${data.quote ? `<blockquote style="font-size:1.4rem;font-weight:700;font-style:italic;line-height:1.7;margin:0 0 2rem">${escapeHtml(data.quote)}</blockquote>` : ''}
            ${data.author_name
            ? `<div style="display:flex;align-items:center;justify-content:center;gap:0.75rem">
                ${authorImage ? `<div style="display:inline-block;width:44px;height:44px;${authorImageStyle}"><img src="${escapeHtml(authorImage)}" alt="${escapeHtml(data.author_name)}" style="height:100%;width:100%;object-fit:cover" /></div>` : ''}
                <div style="text-align:left">
                    <p style="margin:0;font-weight:700">${escapeHtml(data.author_name)}</p>
                    ${data.author_title ? `<p style="margin:0;font-size:0.8rem;opacity:0.55">${escapeHtml(data.author_title)}</p>` : ''}
                </div>
            </div>`
            : ''
        }
        </div>
    </section>`
    );
}

export function applyCardWrapperPreview(html: string, data: any): string {
    const bg = data._wrap_bg || 'transparent';
    const border = data._wrap_border || 'none';
    const bWidth = data._wrap_border_width || '1';
    const bColor = data._wrap_border_color || '#e5e7eb';
    const radius = data._wrap_radius || 'none';
    const shadow = data._wrap_shadow || 'none';
    const px = data._wrap_padding_x || 'none';
    const py = data._wrap_padding_y || 'none';

    const isDefault =
        bg === 'transparent' &&
        border === 'none' &&
        radius === 'none' &&
        shadow === 'none' &&
        px === 'none' &&
        py === 'none';
    if (isDefault) return html;

    const resolveColor = (v: string) => {
        if (v === 'primary') return '#1a2236';
        if (v === 'secondary') return '#f59e0b';
        if (v === 'surface') return '#f5f6fa';
        if (v === 'white') return '#ffffff';
        return v;
    };

    const styles: string[] = [];
    const bgHex = resolveColor(bg);
    if (bgHex && bgHex !== 'transparent') styles.push(`background:${bgHex}`);

    if (border !== 'none') {
        styles.push(`border:${bWidth}px ${border} ${resolveColor(bColor)}`);
    }

    const radiusMap: Record<string, string> = {
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '24px',
    };
    if (radiusMap[radius]) {
        styles.push(`border-radius:${radiusMap[radius]}`);
        styles.push('overflow:hidden');
    }

    const shadowMap: Record<string, string> = {
        sm: '0 1px 2px rgba(0,0,0,0.05)',
        md: '0 4px 6px rgba(0,0,0,0.07)',
        lg: '0 10px 15px rgba(0,0,0,0.1)',
        xl: '0 20px 25px rgba(0,0,0,0.1)',
        '2xl': '0 25px 50px rgba(0,0,0,0.12)',
    };
    if (shadowMap[shadow]) styles.push(`box-shadow:${shadowMap[shadow]}`);

    const pxMap: Record<string, string> = { sm: '1rem', md: '1.5rem', lg: '2rem', xl: '3rem' };
    const pyMap: Record<string, string> = { sm: '0.75rem', md: '1.25rem', lg: '2rem', xl: '3rem' };
    if (pxMap[px]) styles.push(`padding-left:${pxMap[px]};padding-right:${pxMap[px]}`);
    if (pyMap[py]) styles.push(`padding-top:${pyMap[py]};padding-bottom:${pyMap[py]}`);

    return `<div style="${styles.join(';')}">${html}</div>`;
}

export function buildConfigurableCardsPreview(data: any, customCss: string): string {
    const cards = Array.isArray(data.cards) ? data.cards : [];
    const cols = Math.max(1, Math.min(4, parseInt(String(data.cols || 3), 10)));
    const gapMap: Record<string, string> = { sm: '1rem', md: '1.5rem', lg: '2rem', xl: '3rem' };
    const gap = gapMap[data.gap || 'md'] || '1.5rem';

    const solidBg =
        data.bg === 'primary'
            ? 'background:#1a2236;color:white'
            : data.bg === 'surface'
                ? 'background:#f5f6fa'
                : 'background:white';
    const bgImg = mediaUrl(data.bg_image);
    const overlayOpacity = Math.min(100, Math.max(0, Number(data.overlay_opacity ?? 0)));
    const sectionStyle = bgImg
        ? `position:relative;background:url('${escapeHtml(bgImg)}') center/cover no-repeat;`
        : solidBg + ';';
    const overlayHtml = bgImg
        ? `<div style="position:absolute;inset:0;background:black;opacity:${overlayOpacity / 100}"></div>`
        : '';
    const contentStyle = bgImg ? 'position:relative;z-index:1;' : '';

    const cardsHtml = cards.map((card: any) => buildCardPreview(card)).join('');

    return (
        customCss +
        `<section style="padding:4rem 2rem;${sectionStyle}">
            ${overlayHtml}
            <div style="${contentStyle}">
                ${data.section_title ? `<h2 style="font-size:1.75rem;font-weight:900;color:#1a2236;margin:0 0 0.5rem">${escapeHtml(data.section_title)}</h2>` : ''}
                ${data.section_subtitle ? `<p style="margin:0 0 2rem;color:#6b7280">${escapeHtml(data.section_subtitle)}</p>` : ''}
                <div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:${gap}">
                    ${cardsHtml}
                </div>
            </div>
        </section>`
    );
}

export function buildCardPreview(card: any): string {
    const resolveColor = (v: string) => {
        if (!v) return '';
        if (v === 'primary') return '#1a2236';
        if (v === 'secondary') return '#f59e0b';
        if (v === 'surface') return '#f5f6fa';
        if (v === 'white') return '#ffffff';
        if (v === 'transparent') return 'transparent';
        return v;
    };

    const cardBg = resolveColor(card.card_bg || '#ffffff') || '#ffffff';
    const border = card.card_border || 'none';
    const bWidth = card.card_border_width || '1';
    const bColor = resolveColor(card.card_border_color || '#e5e7eb') || '#e5e7eb';
    const radiusMap: Record<string, string> = {
        none: '0',
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '24px',
    };
    const radius = radiusMap[card.card_radius || 'lg'] || '12px';
    const shadowMap: Record<string, string> = {
        none: '',
        sm: '0 1px 2px rgba(0,0,0,0.05)',
        md: '0 4px 6px rgba(0,0,0,0.07)',
        lg: '0 10px 15px rgba(0,0,0,0.1)',
        xl: '0 20px 25px rgba(0,0,0,0.1)',
    };
    const shadow = shadowMap[card.card_shadow || 'md'] || '';
    const paddingMap: Record<string, string> = {
        none: '0',
        sm: '0.75rem',
        md: '1.25rem',
        lg: '1.75rem',
        xl: '2.5rem',
    };
    const padding = paddingMap[card.card_padding || 'md'] || '1.25rem';
    const textAlign = card.text_align || 'left';
    const borderStyle = border !== 'none' ? `border:${bWidth}px ${border} ${bColor};` : '';
    const cardStyle = `background:${cardBg};border-radius:${radius};overflow:hidden;${borderStyle}${shadow ? `box-shadow:${shadow};` : ''}`;

    const imgUrl = mediaUrl(card.image);
    const imgPosition = card.image_position || 'top';
    const aspectPbMap: Record<string, string> = {
        square: '100%',
        photo: '75%',
        video: '56.25%',
        auto: '60%',
    };
    const aspectPb = aspectPbMap[card.image_aspect || 'photo'] || '75%';

    const titleSizeMap: Record<string, string> = {
        sm: '0.9rem',
        md: '1.1rem',
        lg: '1.35rem',
        xl: '1.75rem',
        '2xl': '2.25rem',
    };
    const titleSize = titleSizeMap[card.title_size || 'lg'] || '1.35rem';
    const iconColor = resolveColor(card.icon_color || '#1a2236') || '#1a2236';
    const titleColor = resolveColor(card.title_color || '') || '#111827';
    const subtitleColor = resolveColor(card.subtitle_color || '') || '#6b7280';
    const bodyColor = resolveColor(card.body_color || '') || '#6b7280';

    let innerHtml = '';

    if (imgUrl && imgPosition === 'top') {
        innerHtml += `<div style="position:relative;padding-bottom:${aspectPb};overflow:hidden"><img src="${escapeHtml(imgUrl)}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" /></div>`;
    }

    const isHoriz = imgUrl && (imgPosition === 'left' || imgPosition === 'right');
    let contentHtml = '';

    if (card.icon) {
        const iconCenter = textAlign === 'center' ? ';margin-left:auto;margin-right:auto' : '';
        contentHtml += `<div style="width:40px;height:40px;border-radius:50%;background:rgba(0,0,0,0.06);display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem${iconCenter}"><span class="material-symbols-outlined" style="font-size:20px;color:${iconColor}">${escapeHtml(card.icon)}</span></div>`;
    }
    if (card.title) {
        contentHtml += `<h3 style="font-size:${titleSize};font-weight:900;color:${titleColor};margin:0 0 0.35rem;line-height:1.25">${escapeHtml(card.title)}</h3>`;
    }
    if (card.subtitle) {
        contentHtml += `<p style="font-size:0.8rem;font-weight:600;color:${subtitleColor};margin:0 0 0.5rem;opacity:0.8">${escapeHtml(card.subtitle)}</p>`;
    }
    if (card.body) {
        contentHtml += `<p style="font-size:0.875rem;line-height:1.6;color:${bodyColor};margin:0">${escapeHtml(card.body)}</p>`;
    }
    if (card.cta_label) {
        contentHtml += `<div style="margin-top:0.75rem"><a href="${escapeHtml(card.cta_url || '#')}" style="font-size:0.8rem;font-weight:700;color:#1a2236;text-decoration:none">${escapeHtml(card.cta_label)} \u2192</a></div>`;
    }

    if (isHoriz) {
        const flexDir = imgPosition === 'right' ? 'row-reverse' : 'row';
        innerHtml += `<div style="display:flex;flex-direction:${flexDir};align-items:flex-start">`;
        innerHtml += `<div style="width:33%;flex-shrink:0;overflow:hidden;position:relative;min-height:120px"><img src="${escapeHtml(imgUrl!)}" alt="" style="width:100%;height:100%;min-height:120px;object-fit:cover;position:absolute;inset:0" /></div>`;
        innerHtml += `<div style="flex:1;padding:${padding};text-align:${textAlign}">${contentHtml}</div>`;
        innerHtml += `</div>`;
    } else {
        innerHtml += `<div style="padding:${padding};text-align:${textAlign}">${contentHtml}</div>`;
    }

    if (imgUrl && imgPosition === 'bottom') {
        innerHtml += `<div style="position:relative;padding-bottom:${aspectPb};overflow:hidden"><img src="${escapeHtml(imgUrl)}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" /></div>`;
    }

    return `<article style="${cardStyle}">${innerHtml}</article>`;
}
