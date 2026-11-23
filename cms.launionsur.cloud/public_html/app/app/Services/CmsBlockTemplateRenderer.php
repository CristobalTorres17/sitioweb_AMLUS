<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CmsBlockTemplateRenderer
{
    public function definitions(): array
    {
        return [
            'rich_text' => [
                'label' => 'Contenido libre',
                'description' => 'Editor rich text con imágenes embebidas.',
                'zones' => [
                    'after_hero' => 'Debajo del hero',
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',
                ],
            ],
            'editorial_split_showcase' => [
                'label' => 'Sección editorial dividida',
                'description' => 'Imagen doble a la izquierda y contenido a la derecha.',
                'zones' => [
                    'after_hero' => 'Debajo del hero',
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',

                ],
            ],
            'value_cards_row' => [
                'label' => 'Fila de valores',
                'description' => 'Tres columnas con ícono centrado, título y descripción.',
                'zones' => [
                    'after_hero' => 'Debajo del hero',
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',
                ],
            ],
            'stat_row' => [
                'label' => 'Estadísticas / KPI',
                'description' => 'Fila de números grandes con etiqueta (cifras de impacto).',
                'zones' => [
                    'after_hero' => 'Debajo del hero',
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',
                ],
            ],
            'cta_banner' => [
                'label' => 'Llamada a la acción',
                'description' => 'Franja horizontal con título, texto y botón CTA.',
                'zones' => [
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'after_hero' => 'Debajo del hero',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',
                ],
            ],
            'hero_section' => [
                'label' => 'Hero de sección',
                'description' => 'Hero interior con imagen de fondo, título y badge.',
                'zones' => [
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'after_hero' => 'Debajo del hero',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',

                ],
            ],
            'image_gallery' => [
                'label' => 'Galería de imágenes',
                'description' => 'Grid de 2–4 columnas con imágenes y caption opcional.',
                'zones' => [
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',
                    'after_hero' => 'Debajo del hero',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',

                ],
            ],
            'timeline' => [
                'label' => 'Línea de tiempo',
                'description' => 'Lista vertical cronológica de hitos.',
                'zones' => [
                    'content' => 'Contenido principal',
                    'mid_content' => 'Mitad del contenido',

                ],
            ],
            'quote_highlight' => [
                'label' => 'Cita destacada',
                'description' => 'Cita grande centrada con autor.',
                'zones' => [
                    'content' => 'Contenido principal',
                    'after_hero' => 'Debajo del hero',
                    'mid_content' => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',
                    'footer' => 'Footer',

                ],
            ],
            'configurable_cards' => [
                'label' => 'Tarjetas configurables',
                'description' => 'Grid de tarjetas parametrizables: imagen, icono, título, subtítulo, estilos individuales.',
                'zones' => [
                    'after_hero'    => 'Debajo del hero',
                    'content'       => 'Contenido principal',
                    'mid_content'   => 'Mitad del contenido',
                    'before_footer' => 'Antes del footer',
                    'footer'        => 'Footer',
                ],
            ],
            'article_sections' => [
                'label' => 'Constructor de artículo',
                'description' => 'Ensambla el cuerpo de una noticia con secciones de texto, imagen, video, cita y más.',
                'zones' => [
                    'mid_content' => 'Cuerpo del artículo',
                    'content'     => 'Contenido principal',
                    'after_hero'  => 'Debajo del hero',
                ],
            ],
        ];
    }

    public function keys(): array
    {
        return array_keys($this->definitions());
    }

    public function defaultTemplateKey(): string
    {
        return 'rich_text';
    }

    public function defaultZone(string $templateKey): string
    {
        return match ($templateKey) {
            'editorial_split_showcase', 'value_cards_row', 'stat_row' => 'after_hero',
            default => 'content',
        };
    }

    public function defaultData(string $templateKey): array
    {
        return match ($templateKey) {
            'editorial_split_showcase' => [
                'eyebrow' => 'CONOCE NUESTRA GESTIÓN',
                'title' => 'Alcaldía de La Unión Sur: Gestión Transformadora',
                'descriptions' => [
                    [
                        'subtitle' => 'Innovación con visión',
                        'description' => 'Victoria Gutiérrez lidera con visión e innovación, impulsando proyectos que transforman la ciudad.',
                        'icon' => 'rocket_launch',
                        'icon_position' => 'left',
                    ],
                    [
                        'subtitle' => 'Gestión para todos',
                        'description' => 'Su gestión moderniza infraestructuras y fortalece la participación ciudadana para un desarrollo real.',
                        'icon' => 'groups',
                        'icon_position' => 'right',
                    ],
                ],
                'cta_label' => 'MÁS INFORMACIÓN',
                'cta_url' => '/about',
                'badge_text' => 'PRIMERA ALCALDESA DE LA UNIÓN SUR',
                'main_image' => null,
                'secondary_image' => null,
                'main_image_border' => 'none',
                'main_image_border_width' => '1',
                'main_image_border_color' => '#e5e7eb',
                'main_image_radius' => 'xl',
                'main_image_shadow' => 'xl',
                'secondary_image_border' => 'none',
                'secondary_image_border_width' => '1',
                'secondary_image_border_color' => '#e5e7eb',
                'secondary_image_radius' => 'lg',
                'secondary_image_shadow' => 'lg',
                'bg' => 'white',
                'image_side' => 'left',
                'title_color' => '',
                'description_color' => '',
            ],
            'value_cards_row' => [
                'cards' => [
                    [
                        'icon' => 'person',
                        'title' => 'Liderazgo Inspirador',
                        'description' => 'La alcaldesa actúa con compromiso y lidera con inspiración.',
                        'icon_color' => '#1a2236',
                        'title_color' => '',
                        'description_color' => '',
                        'text_align' => 'center',
                        'card_bg' => '#ffffff',
                        'card_border' => 'none',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius' => 'none',
                        'card_shadow' => 'none',
                        'card_padding' => 'md',
                    ],
                    [
                        'icon' => 'verified',
                        'title' => 'Gestión Eficiente',
                        'description' => 'Optimiza recursos y moderniza la ciudad para el progreso.',
                        'icon_color' => '#1a2236',
                        'title_color' => '',
                        'description_color' => '',
                        'text_align' => 'center',
                        'card_bg' => '#ffffff',
                        'card_border' => 'none',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius' => 'none',
                        'card_shadow' => 'none',
                        'card_padding' => 'md',
                    ],
                    [
                        'icon' => 'volunteer_activism',
                        'title' => 'Compromiso Social',
                        'description' => 'Impulsa proyectos que fortalecen el tejido social.',
                        'icon_color' => '#1a2236',
                        'title_color' => '',
                        'description_color' => '',
                        'text_align' => 'center',
                        'card_bg' => '#ffffff',
                        'card_border' => 'none',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius' => 'none',
                        'card_shadow' => 'none',
                        'card_padding' => 'md',
                    ],
                ],
                'bg' => 'white',
                'bg_image' => null,
                'overlay_opacity' => 0,
            ],
            'stat_row' => [
                'eyebrow' => '',
                'bg' => 'white',
                'stats' => [
                    ['number' => '3,200', 'suffix' => '+', 'label' => 'Ciudadanos atendidos', 'icon' => 'groups', 'icon_color' => '', 'number_color' => '', 'label_color' => '', 'card_bg' => 'transparent', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'none', 'card_shadow' => 'none', 'card_padding' => 'none'],
                    ['number' => '12', 'suffix' => '', 'label' => 'Proyectos terminados', 'icon' => 'fact_check', 'icon_color' => '', 'number_color' => '', 'label_color' => '', 'card_bg' => 'transparent', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'none', 'card_shadow' => 'none', 'card_padding' => 'none'],
                    ['number' => '98', 'suffix' => '%', 'label' => 'Satisfacción ciudadana', 'icon' => 'volunteer_activism', 'icon_color' => '', 'number_color' => '', 'label_color' => '', 'card_bg' => 'transparent', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'none', 'card_shadow' => 'none', 'card_padding' => 'none'],
                ],
                'bg_image' => null,
                'overlay_opacity' => 0,
            ],
            'cta_banner' => [
                'title' => '¿Necesitas ayuda?',
                'subtitle' => 'Contáctanos y te orientamos con los trámites disponibles.',
                'cta_primary_label' => 'Contactar ahora',
                'cta_primary_url' => '/contact',
                'cta_secondary_label' => '',
                'cta_secondary_url' => '',
                'bg' => 'primary',
                'alignment' => 'split',
                'bg_image' => null,
                'fg_image' => null,
                'fg_image_border' => 'none',
                'fg_image_border_width' => '1',
                'fg_image_border_color' => '#e5e7eb',
                'fg_image_radius' => 'xl',
                'fg_image_shadow' => 'lg',
                'overlay_opacity' => 70,
                'title_color' => '',
                'subtitle_color' => '',
            ],
            'hero_section' => [
                'eyebrow' => 'ALCALDÍA DE LA UNIÓN SUR',
                'title' => 'Transformando nuestra ciudad',
                'subtitle' => 'Una gestión comprometida con el bienestar de cada ciudadano.',
                'badge_text' => '',
                'cta_label' => 'Conoce más',
                'cta_url' => '/about',
                'height' => 'md',
                'overlay_opacity' => 50,
                'bg_image' => null,
                'bg' => 'primary',
                'text_align' => 'left',
                'overlay_color' => 'black',
                'fg_image_border' => 'none',
                'fg_image_border_width' => '1',
                'fg_image_border_color' => '#e5e7eb',
                'fg_image_radius' => 'xl',
                'fg_image_shadow' => '2xl',
                'eyebrow_color' => '',
                'title_color' => '',
                'subtitle_color' => '',
            ],
            'image_gallery' => [
                'title' => '',
                'cols' => '3',
                'aspect' => 'photo',
                'items' => [
                    ['image' => null, 'caption' => '', 'caption_color' => '', 'card_bg' => '#e5e7eb', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'md', 'card_shadow' => 'none'],
                    ['image' => null, 'caption' => '', 'caption_color' => '', 'card_bg' => '#e5e7eb', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'md', 'card_shadow' => 'none'],
                    ['image' => null, 'caption' => '', 'caption_color' => '', 'card_bg' => '#e5e7eb', 'card_border' => 'none', 'card_border_width' => '1', 'card_border_color' => '#e5e7eb', 'card_radius' => 'md', 'card_shadow' => 'none'],
                ],
            ],
            'timeline' => [
                'title' => 'Hitos de la gestión',
                'items' => [
                    ['date' => '2021', 'title' => 'Inicio de gestión', 'description' => 'Victoria Gutiérrez asumió como primera alcaldesa de La Unión Sur.', 'icon' => 'flag', 'icon_color' => '#ffffff', 'title_color' => '', 'date_color' => '', 'description_color' => '', 'accent_color' => '#1a2236'],
                    ['date' => '2022', 'title' => 'Renovación del parque central', 'description' => 'Inauguración del parque renovado con nuevas instalaciones deportivas.', 'icon' => 'park', 'icon_color' => '#ffffff', 'title_color' => '', 'date_color' => '', 'description_color' => '', 'accent_color' => '#1a2236'],
                    ['date' => '2023', 'title' => 'Modernización digital', 'description' => 'Lanzamiento del portal de trámites en línea.', 'icon' => 'computer', 'icon_color' => '#ffffff', 'title_color' => '', 'date_color' => '', 'description_color' => '', 'accent_color' => '#1a2236'],
                ],
                'bg' => 'white',
                'bg_image' => null,
                'overlay_opacity' => 0,
            ],
            'quote_highlight' => [
                'quote' => 'Trabajamos cada día para que La Unión Sur sea una ciudad más justa, segura y próspera para todos.',
                'author_name' => 'Victoria Gutiérrez',
                'author_title' => 'Alcaldesa de La Unión Sur',
                'author_image' => null,
                'bg' => 'surface',
                'bg_image' => null,
                'overlay_opacity' => 0,
                'quote_color' => '',
                'author_color' => '',
                'author_image_border' => 'none',
                'author_image_border_width' => '1',
                'author_image_border_color' => '#e5e7eb',
                'author_image_radius' => 'full',
                'author_image_shadow' => 'none',
            ],
            'configurable_cards' => [
                'section_title'   => '',
                'section_subtitle' => '',
                'bg'              => 'white',
                'bg_image'        => null,
                'overlay_opacity' => 0,
                'cols'            => 3,
                'gap'             => 'md',
                'cards'           => [
                    [
                        'type'              => 'text',
                        'image'             => null,
                        'image_position'    => 'top',
                        'image_aspect'      => 'photo',
                        'icon'              => 'verified',
                        'icon_color'        => '#1a2236',
                        'title'             => 'Primera tarjeta',
                        'title_size'        => 'lg',
                        'title_color'       => '',
                        'subtitle'          => '',
                        'subtitle_color'    => '',
                        'body'              => 'Descripción breve de este elemento con información relevante.',
                        'body_color'        => '',
                        'cta_label'         => '',
                        'cta_url'           => '',
                        'text_align'        => 'left',
                        'card_bg'           => '#ffffff',
                        'card_border'       => 'solid',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius'       => 'lg',
                        'card_shadow'       => 'md',
                        'card_padding'      => 'md',
                    ],
                    [
                        'type'              => 'text',
                        'image'             => null,
                        'image_position'    => 'top',
                        'image_aspect'      => 'photo',
                        'icon'              => 'lightbulb',
                        'icon_color'        => '#1a2236',
                        'title'             => 'Segunda tarjeta',
                        'title_size'        => 'lg',
                        'title_color'       => '',
                        'subtitle'          => '',
                        'subtitle_color'    => '',
                        'body'              => 'Otra descripción con información relevante para mostrar aquí.',
                        'body_color'        => '',
                        'cta_label'         => '',
                        'cta_url'           => '',
                        'text_align'        => 'left',
                        'card_bg'           => '#ffffff',
                        'card_border'       => 'solid',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius'       => 'lg',
                        'card_shadow'       => 'md',
                        'card_padding'      => 'md',
                    ],
                    [
                        'type'              => 'text',
                        'image'             => null,
                        'image_position'    => 'top',
                        'image_aspect'      => 'photo',
                        'icon'              => 'handshake',
                        'icon_color'        => '#1a2236',
                        'title'             => 'Tercera tarjeta',
                        'title_size'        => 'lg',
                        'title_color'       => '',
                        'subtitle'          => '',
                        'subtitle_color'    => '',
                        'body'              => 'Tercera descripción con datos e información adicional relevante.',
                        'body_color'        => '',
                        'cta_label'         => '',
                        'cta_url'           => '',
                        'text_align'        => 'left',
                        'card_bg'           => '#ffffff',
                        'card_border'       => 'solid',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius'       => 'lg',
                        'card_shadow'       => 'md',
                        'card_padding'      => 'md',
                    ],
                ],
            ],
            'article_sections' => [
                'sections' => [
                    [
                        'type'    => 'text',
                        'content' => '<p>Escribe el contenido de tu artículo aquí. Puedes agregar más secciones usando el botón de abajo.</p>',
                        'block_bg' => '',
                        'block_border' => 'none',
                        'block_border_width' => '1',
                        'block_border_color' => '#e5e7eb',
                        'block_radius' => 'none',
                        'block_shadow' => 'none',
                        'block_padding' => 'none',
                        'text_align' => 'left',
                        'text_size' => 'normal',
                    ],
                    [
                        'type'         => 'card',
                        'card_variant' => 'content',
                        'title'        => 'Ejemplo de card',
                        'title_size'   => 'xl',
                        'title_weight' => 'bold',
                        'title_italic' => false,
                        'title_line_height' => 'tight',
                        'title_color' => '',
                        'subtitle'     => 'Subtítulo opcional',
                        'subtitle_size' => 'sm',
                        'subtitle_weight' => 'medium',
                        'subtitle_italic' => false,
                        'subtitle_color' => '',
                        'paragraphs'   => [
                            ['text' => 'Este es un párrafo dentro de la card. Puedes editar su color, tamaño, peso, cursiva e interlineado.', 'color' => '', 'size' => 'base', 'weight' => 'normal', 'italic' => false, 'line_height' => 'relaxed'],
                        ],
                        'card_bg' => '#ffffff',
                        'card_border' => 'solid',
                        'card_border_width' => '1',
                        'card_border_color' => '#e5e7eb',
                        'card_radius' => 'lg',
                        'card_shadow' => 'md',
                        'card_padding' => 'lg',
                        'card_align' => 'left',
                        'card_width' => 'full',
                    ],
                ],
            ],
            default => [],
        };
    }

    public function normalizeData(string $templateKey, ?array $data): array
    {
        $wrapDefaults = [
            '_wrap_bg'           => 'transparent',
            '_wrap_border'       => 'none',
            '_wrap_border_width' => '1',
            '_wrap_border_color' => '#e5e7eb',
            '_wrap_radius'       => 'none',
            '_wrap_shadow'       => 'none',
            '_wrap_padding_x'    => 'none',
            '_wrap_padding_y'    => 'none',
        ];

        $merged = array_replace_recursive($wrapDefaults + $this->defaultData($templateKey), $data ?? []);

        // array_replace_recursive merges numeric arrays by index, which causes deleted list
        // items (cards, stats, items) to be "restored" from defaults. Override those fields
        // with the user's exact arrays so a deletion is not undone.
        foreach (['cards', 'items', 'stats', 'sections'] as $listKey) {
            if (isset($data[$listKey]) && is_array($data[$listKey])) {
                $merged[$listKey] = $data[$listKey];
            }
        }

        return $merged;
    }

    public function render(string $templateKey, ?array $data, ?string $fallbackContent = null): string
    {
        $customHtml = is_array($data) ? ($data['_custom_html'] ?? '') : '';
        $customCss  = is_array($data) ? ($data['_custom_css']  ?? '') : '';
        $styleTag   = $customCss ? "<style>\n{$customCss}\n</style>\n" : '';

        if ($customHtml) {
            return $styleTag . $customHtml;
        }

        $templateKey = $templateKey ?: $this->defaultTemplateKey();
        $normalized  = $this->normalizeData($templateKey, $data);

        $html = match ($templateKey) {
            'editorial_split_showcase' => $this->renderEditorialSplitShowcase($normalized),
            'value_cards_row'          => $this->renderValueCardsRow($normalized),
            'stat_row'                 => $this->renderStatRow($normalized),
            'cta_banner'               => $this->renderCtaBanner($normalized),
            'hero_section'             => $this->renderHeroSection($normalized),
            'image_gallery'            => $this->renderImageGallery($normalized),
            'timeline'                 => $this->renderTimeline($normalized),
            'quote_highlight'          => $this->renderQuoteHighlight($normalized),
            'configurable_cards'       => $this->renderConfigurableCards($normalized),
            'article_sections'         => $this->renderArticleSections($normalized),
            default                    => (string) ($fallbackContent ?? ''),
        };

        return $styleTag . $this->applyCardWrapper($html, $normalized);
    }

    public function renderRichTextFallback(?string $content): string
    {
        return (string) ($content ?? '');
    }

    private function editorialDescriptions(array $data): array
    {
        $items = $data['descriptions'] ?? null;

        if (!is_array($items) || empty($items)) {
            $items = [];

            if (!empty($data['description_1'])) {
                $items[] = [
                    'subtitle' => 'Descripción 1',
                    'description' => $data['description_1'],
                    'icon' => '',
                    'icon_position' => 'left',
                ];
            }

            if (!empty($data['description_2'])) {
                $items[] = [
                    'subtitle' => 'Descripción 2',
                    'description' => $data['description_2'],
                    'icon' => '',
                    'icon_position' => 'left',
                ];
            }
        }

        return array_values(array_map(static function (array $item, int $index): array {
            return [
                'subtitle' => (string) ($item['subtitle'] ?? $item['title'] ?? 'Descripción ' . ($index + 1)),
                'description' => (string) ($item['description'] ?? $item['body'] ?? ''),
                'icon' => (string) ($item['icon'] ?? ''),
                'icon_position' => ($item['icon_position'] ?? 'left') === 'right' ? 'right' : 'left',
            ];
        }, $items, array_keys($items)));
    }

    private function renderEditorialSplitShowcase(array $data): string
    {
        $mainImage      = $this->imageUrl($data['main_image'] ?? null);
        $secondaryImage = $this->imageUrl($data['secondary_image'] ?? null);
        $imageSide      = ($data['image_side'] ?? 'left') === 'right' ? 'right' : 'left';
        $bgClass        = match ($data['bg'] ?? 'white') {
            'primary' => 'bg-primary text-white',
            'surface' => 'bg-surface',
            default   => 'bg-white',
        };
        $gridCols = $imageSide === 'right' ? 'lg:grid-cols-[0.95fr_1.05fr]' : 'lg:grid-cols-[1.05fr_0.95fr]';
        $mainImageStyle = $this->buildMediaFrameStyle($data, 'main_image');
        $secondaryImageStyle = $this->buildMediaFrameStyle($data, 'secondary_image');
        $descriptions = $this->editorialDescriptions($data);

        // ── Image column ──────────────────────────────────────────────────────
        $imageCol  = '<div class="relative flex items-start gap-5" style="min-height:420px;">';
        if ($mainImage) {
            $imageCol .= '<div style="display:inline-block;height:420px;width:360px;' . $mainImageStyle . '">';
            $imageCol .= '<img src="' . e($mainImage) . '" alt="' . e($this->imageAlt($data['main_image'] ?? null, $data['title'] ?? '')) . '" class="h-full w-full object-cover" />';
            $imageCol .= '</div>';
        }
        if ($secondaryImage) {
            $imageCol .= '<div style="display:inline-block;margin-top:3rem;height:260px;width:260px;' . $secondaryImageStyle . '">';
            $imageCol .= '<img src="' . e($secondaryImage) . '" alt="' . e($this->imageAlt($data['secondary_image'] ?? null, $data['title'] ?? '')) . '" class="h-full w-full object-cover" />';
            $imageCol .= '</div>';
        }
        if (!empty($data['badge_text'])) {
            $imageCol .= '<div class="absolute right-0 top-6 rounded-3xl bg-secondary-container px-6 py-5 text-sm font-bold uppercase tracking-wider text-on-secondary-container shadow-lg">';
            $imageCol .= e($data['badge_text']);
            $imageCol .= '</div>';
        }
        $imageCol .= '</div>';

        // ── Content column ────────────────────────────────────────────────────
        $titleColor  = $this->resolveColorStyle((string) ($data['title_color'] ?? ''));
        $descColor   = $this->resolveColorStyle((string) ($data['description_color'] ?? ''));
        $contentCol  = '<div class="space-y-6">';
        if (!empty($data['eyebrow'])) {
            $contentCol .= '<p class="text-sm font-bold uppercase tracking-[0.22em] text-secondary">' . e($data['eyebrow']) . '</p>';
        }
        if (!empty($data['title'])) {
            $tStyle = $titleColor ? " style=\"color:{$titleColor}\"" : '';
            $contentCol .= "<h2 class=\"text-4xl font-black leading-tight tracking-tight text-primary md:text-5xl\"{$tStyle}>" . e($data['title']) . '</h2>';
        }
        foreach ($descriptions as $index => $item) {
            $isRight = ($item['icon_position'] ?? 'left') === 'right';
            $subtitle = trim((string) ($item['subtitle'] ?? ''));
            $body = trim((string) ($item['description'] ?? ''));
            $icon = trim((string) ($item['icon'] ?? ''));

            if ($subtitle === '' && $body === '' && $icon === '') {
                continue;
            }

            $iconHtml = $icon
                ? '<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-secondary-container text-secondary">'
                . '<span class="material-symbols-outlined text-[20px] leading-none">' . e($icon) . '</span>'
                . '</div>'
                : '';
            $textHtml = '<div class="flex-1 space-y-1">';
            if ($subtitle !== '') {
                $textHtml .= '<p class="text-sm font-extrabold uppercase tracking-[0.08em]"' . ($titleColor ? ' style="color:' . $titleColor . '"' : '') . '>' . e($subtitle) . '</p>';
            }
            if ($body !== '') {
                $textHtml .= '<p class="max-w-2xl text-base leading-8 text-text-secondary"' . ($descColor ? ' style="color:' . $descColor . '"' : '') . '>' . e($body) . '</p>';
            }
            $textHtml .= '</div>';

            $contentCol .= '<div class="flex items-start gap-4"' . ($isRight ? ' style="flex-direction:row-reverse;"' : '') . '>' . $iconHtml . $textHtml . '</div>';
        }
        if (!empty($data['cta_label'])) {
            $ctaUrl = $data['cta_url'] ?: '#';
            $contentCol .= '<a href="' . e($ctaUrl) . '" class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-[0.18em] text-primary transition hover:text-secondary">';
            $contentCol .= e($data['cta_label']);
            $contentCol .= '</a>';
        }
        $contentCol .= '</div>';

        $html  = "<section class=\"cms-block cms-block--editorial-split {$bgClass} py-16\">";
        $html .= "<div class=\"mx-auto grid max-w-screen-2xl items-center gap-12 px-6 {$gridCols} md:px-8\">";
        $html .= $imageSide === 'right' ? ($contentCol . $imageCol) : ($imageCol . $contentCol);
        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    private function renderValueCardsRow(array $data): string
    {
        $cards = $data['cards'] ?? [];
        $bgImage  = $this->imageUrl($data['bg_image'] ?? null);
        $opacity  = intval($data['overlay_opacity'] ?? 0);
        $bgClass  = match ($data['bg'] ?? 'white') {
            'primary' => 'bg-primary text-white',
            'surface' => 'bg-surface',
            default   => 'bg-white',
        };
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage
            ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>"
            : '';
        $posClass = $bgImage ? ' relative overflow-hidden' : '';
        $zClass   = $bgImage ? ' relative z-10' : '';

        $cols = max(1, count($cards));
        $html  = "<section class=\"cms-block cms-block--value-cards {$bgClass}{$posClass} py-20\"{$sectionStyle}>{$overlayHtml}";
        $html .= "<div class=\"mx-auto max-w-screen-2xl px-6 md:px-8{$zClass}\" style=\"display:grid;grid-template-columns:repeat({$cols},1fr);gap:1rem;\">";

        foreach ($cards as $card) {
            $title        = (string) ($card['title'] ?? '');
            $description  = (string) ($card['description'] ?? '');
            $icon         = (string) ($card['icon'] ?? 'verified');
            $iconColor    = (string) ($card['icon_color'] ?? '#1a2236');
            $titleColor   = $this->resolveColorStyle((string) ($card['title_color'] ?? ''));
            $descColor    = $this->resolveColorStyle((string) ($card['description_color'] ?? '#6b7280'));
            $textAlign    = in_array($card['text_align'] ?? 'center', ['left', 'center', 'right']) ? $card['text_align'] : 'center';
            $cardStyle    = $this->buildItemCardStyle($card);

            $html .= "<article style=\"{$cardStyle}text-align:{$textAlign}\" class=\"flex flex-col items-center gap-4\">";
            $html .= '<div class="flex h-16 w-16 items-center justify-center rounded-full border border-gray-100 bg-white shadow-sm">';
            $html .= '<span class="material-symbols-outlined text-[32px]" style="color:' . e($iconColor) . '">' . e($icon) . '</span>';
            $html .= '</div>';

            if ($title !== '') {
                $titleStyle = $titleColor ? "style=\"color:{$titleColor}\"" : '';
                $html .= "<h3 class=\"text-2xl font-black leading-tight\" {$titleStyle}>" . e($title) . '</h3>';
            }

            if ($description !== '') {
                $descStyle = $descColor ? "style=\"color:{$descColor}\"" : 'style="color:#6b7280"';
                $html .= "<p class=\"text-base leading-7\" {$descStyle}>" . e($description) . '</p>';
            }

            $html .= '</article>';
        }

        $html .= '</div></section>';

        return $html;
    }

    private function renderStatRow(array $data): string
    {
        $stats    = $data['stats'] ?? [];
        $bgImage  = $this->imageUrl($data['bg_image'] ?? null);
        $opacity  = intval($data['overlay_opacity'] ?? 0);
        $bg       = match ($data['bg'] ?? 'white') {
            'primary' => 'bg-primary text-white',
            'surface' => 'bg-surface text-primary',
            default   => 'bg-white text-primary',
        };
        $count = max(1, count($stats));
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>" : '';
        $posClass     = $bgImage ? ' relative overflow-hidden' : '';
        $zClass       = $bgImage ? ' relative z-10' : '';

        $html  = "<section class=\"cms-block cms-block--stat-row {$bg}{$posClass} py-16\"{$sectionStyle}>{$overlayHtml}";
        $html .= "<div class=\"mx-auto max-w-screen-2xl px-6 md:px-8{$zClass}\">";
        if (!empty($data['eyebrow'])) {
            $html .= '<p class="mb-8 text-center text-sm font-bold uppercase tracking-[0.2em] opacity-60">' . e($data['eyebrow']) . '</p>';
        }
        $html .= "<div class=\"text-center\" style=\"display:grid;grid-template-columns:repeat({$count},1fr);gap:1.5rem;\">";
        foreach ($stats as $stat) {
            $icon        = (string) ($stat['icon'] ?? '');
            $number      = (string) ($stat['number'] ?? '');
            $suffix      = (string) ($stat['suffix'] ?? '');
            $label       = (string) ($stat['label'] ?? '');
            $iconColor   = $this->resolveColorStyle((string) ($stat['icon_color'] ?? ''));
            $numColor    = $this->resolveColorStyle((string) ($stat['number_color'] ?? ''));
            $labelColor  = $this->resolveColorStyle((string) ($stat['label_color'] ?? ''));
            $cardStyle   = $this->buildItemCardStyle($stat);

            $html .= '<div class="flex flex-col items-center gap-2" style="' . $cardStyle . '">';
            if ($icon) {
                $iStyle = $iconColor ? "style=\"color:{$iconColor}\"" : 'style="opacity:0.7"';
                $html .= "<span class=\"material-symbols-outlined text-4xl\" {$iStyle}>" . e($icon) . '</span>';
            }
            $nStyle  = $numColor ? "style=\"color:{$numColor}\"" : '';
            $lStyle  = $labelColor ? "style=\"color:{$labelColor};opacity:0.7\"" : 'style="opacity:0.7"';
            $html .= "<p class=\"text-5xl font-black leading-none\" {$nStyle}>" . e($number) . '<span class="opacity-60">' . e($suffix) . '</span></p>';
            $html .= "<p class=\"text-sm font-medium\" {$lStyle}>" . e($label) . '</p>';
            $html .= '</div>';
        }
        $html .= '</div></div></section>';

        return $html;
    }

    private function renderCtaBanner(array $data): string
    {
        $isSplit  = ($data['alignment'] ?? 'split') === 'split';
        $bgImage  = $this->imageUrl($data['bg_image'] ?? null);
        $fgImage  = $this->imageUrl($data['fg_image'] ?? null);
        $opacity  = intval($data['overlay_opacity'] ?? 70);
        $bg       = match ($data['bg'] ?? 'primary') {
            'secondary' => 'bg-secondary',
            'surface'   => 'bg-surface text-primary',
            default     => 'bg-primary text-white',
        };
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>" : '';
        $posClass     = $bgImage ? ' relative overflow-hidden' : '';
        $zClass       = $bgImage ? ' relative z-10' : '';
        $fgImageStyle  = $this->buildMediaFrameStyle($data, 'fg_image');

        $html  = "<section class=\"cms-block cms-block--cta-banner {$bg}{$posClass} py-12\"{$sectionStyle}>{$overlayHtml}";
        $html .= '<div class="mx-auto max-w-screen-2xl px-6 md:px-8">';

        if ($fgImage) {
            // Three column layout: text | buttons | fg image
            $html .= '<div class="flex flex-col items-center justify-between gap-6 md:flex-row">';
        } else {
            $html .= $isSplit
                ? '<div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">'
                : '<div class="flex flex-col items-center gap-6 text-center">';
        }

        $titleColor    = $this->resolveColorStyle((string) ($data['title_color'] ?? ''));
        $subtitleColor = $this->resolveColorStyle((string) ($data['subtitle_color'] ?? ''));

        $html .= "<div class=\"{$zClass}\">";
        if (!empty($data['title'])) {
            $tStyle = $titleColor ? " style=\"color:{$titleColor}\"" : '';
            $html .= "<h2 class=\"text-3xl font-black leading-tight\"{$tStyle}>" . e($data['title']) . '</h2>';
        }
        if (!empty($data['subtitle'])) {
            $sStyle = $subtitleColor ? "style=\"color:{$subtitleColor}\"" : 'style="opacity:0.8"';
            $html .= "<p class=\"mt-2\" {$sStyle}>" . e($data['subtitle']) . '</p>';
        }
        $html .= '</div>';

        $html .= "<div class=\"flex shrink-0 flex-wrap gap-3{$zClass}\">";
        if (!empty($data['cta_primary_label'])) {
            $html .= '<a href="' . e($data['cta_primary_url'] ?: '#') . '" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-bold text-primary shadow transition hover:opacity-90">';
            $html .= e($data['cta_primary_label']) . '</a>';
        }
        if (!empty($data['cta_secondary_label'])) {
            $html .= '<a href="' . e($data['cta_secondary_url'] ?: '#') . '" class="inline-flex items-center rounded-full border-2 border-white/70 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">';
            $html .= e($data['cta_secondary_label']) . '</a>';
        }
        $html .= '</div>';

        if ($fgImage) {
            $html .= "<div class=\"relative z-10 shrink-0\" style=\"display:inline-block;{$fgImageStyle}\">";
            $html .= '<img src="' . e($fgImage) . '" alt="' . e($this->imageAlt($data['fg_image'] ?? null, $data['title'] ?? '')) . '" class="h-48 w-48 object-cover md:h-56 md:w-56" />';
            $html .= '</div>';
        }

        $html .= '</div></div></section>';

        return $html;
    }

    private function renderHeroSection(array $data): string
    {
        $heights = ['sm' => 'min-h-[280px]', 'md' => 'min-h-[420px]', 'lg' => 'min-h-[560px]', 'full' => 'min-h-screen'];
        $minH    = $heights[$data['height'] ?? 'md'] ?? 'min-h-[420px]';
        $opacity = intval($data['overlay_opacity'] ?? 50);
        $bgImage = $this->imageUrl($data['bg_image'] ?? null);
        $fgImage = $this->imageUrl($data['fg_image'] ?? null);
        $fgPos   = ($data['fg_position'] ?? 'right') === 'left' ? 'left' : 'right';
        $bgStyle = $bgImage
            ? "background-image:url('{$bgImage}');background-size:cover;background-position:center;"
            : '';
        $posClass    = $bgImage ? ' relative overflow-hidden' : '';
        $overlayHtml  = $bgImage ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>" : '';
        $fgImageStyle = $this->buildMediaFrameStyle($data, 'fg_image');

        $html  = "<section class=\"cms-block cms-block--hero-section flex {$minH} items-center{$posClass}\" style=\"{$bgStyle}\">";
        $html .= $overlayHtml;

        if ($fgImage) {
            // Layout con imagen frontal: flex row
            $flexOrder  = ($fgPos === 'left') ? 'flex-row' : 'flex-row-reverse';
            $textAlign  = ($fgPos === 'left') ? 'text-right' : 'text-left';
            $html .= "<div class=\"relative z-10 mx-auto max-w-screen-xl w-full px-6 py-16 md:px-8 flex {$flexOrder} items-center gap-8\">";
            $html .= "<div class=\"flex-1 text-white {$textAlign}\">";
        } else {
            // Layout tradicional sin imagen frontal
            $textAlignClass = ($data['text_align'] ?? 'left') === 'center' ? ' text-center' : '';
            $html .= "<div class=\"relative z-10 mx-auto max-w-screen-xl px-6 py-16 text-white md:px-8{$textAlignClass}\">";
        }

        $eyebrowColor  = $this->resolveColorStyle((string) ($data['eyebrow_color'] ?? ''));
        $titleColor    = $this->resolveColorStyle((string) ($data['title_color'] ?? ''));
        $subtitleColor = $this->resolveColorStyle((string) ($data['subtitle_color'] ?? ''));

        if (!empty($data['eyebrow'])) {
            $eStyle = $eyebrowColor ? "style=\"color:{$eyebrowColor}\"" : 'style="color:rgba(255,255,255,0.7)"';
            $html .= "<p class=\"mb-3 text-xs font-bold uppercase tracking-[0.25em]\" {$eStyle}>" . e($data['eyebrow']) . '</p>';
        }
        if (!empty($data['badge_text'])) {
            $html .= '<span class="mb-4 inline-block rounded-full bg-secondary px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white">' . e($data['badge_text']) . '</span>';
        }
        if (!empty($data['title'])) {
            $tStyle = $titleColor ? "style=\"color:{$titleColor}\"" : 'style="color:#ffffff"';
            $html .= "<h1 class=\"text-5xl font-black leading-tight tracking-tight md:text-6xl\" {$tStyle}>" . e($data['title']) . '</h1>';
        }
        if (!empty($data['subtitle'])) {
            $sStyle = $subtitleColor ? "style=\"color:{$subtitleColor}\"" : 'style="color:rgba(255,255,255,0.8)"';
            $html .= "<p class=\"mt-4 max-w-2xl text-lg leading-8\" {$sStyle}>" . e($data['subtitle']) . '</p>';
        }
        if (!empty($data['cta_label'])) {
            $html .= '<a href="' . e($data['cta_url'] ?: '#') . '" class="mt-8 inline-flex items-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-white shadow-lg transition hover:opacity-90">';
            $html .= e($data['cta_label']) . '</a>';
        }

        if ($fgImage) {
            $html .= '</div>'; // cierra div del texto
            $html .= '<div class="shrink-0" style="display:inline-block;height:24rem;width:20rem;' . $fgImageStyle . '"><img src="' . e($fgImage) . '" alt="' . e($this->imageAlt($data['fg_image'] ?? null, $data['title'] ?? '')) . '" class="h-full w-full object-cover" /></div>';
            $html .= '</div>'; // cierra div del flex
        } else {
            $html .= '</div>'; // cierra div del contenido
        }

        $html .= '</section>';

        return $html;
    }

    private function renderImageGallery(array $data): string
    {
        $cols  = in_array($data['cols'] ?? '3', ['2', '3', '4']) ? $data['cols'] : '3';
        $aspect = match ($data['aspect'] ?? 'photo') {
            'square' => 'aspect-square',
            'video'  => 'aspect-video',
            default  => 'aspect-[4/3]',
        };
        $items = $data['items'] ?? [];

        $html  = '<section class="cms-block cms-block--image-gallery py-12">';
        $html .= '<div class="mx-auto max-w-screen-2xl px-6 md:px-8">';
        if (!empty($data['title'])) {
            $html .= '<h2 class="mb-8 text-2xl font-black text-primary">' . e($data['title']) . '</h2>';
        }
        $html .= "<div style=\"display:grid;grid-template-columns:repeat({$cols},1fr);gap:1rem;\">";
        foreach ($items as $item) {
            $url          = $this->imageUrl($item['image'] ?? null);
            $caption      = (string) ($item['caption'] ?? '');
            $captionColor = $this->resolveColorStyle((string) ($item['caption_color'] ?? '#6b7280'));
            $cardStyle    = $this->buildItemCardStyle($item);
            $figureStyle  = 'overflow:hidden;' . ($cardStyle ?: 'border-radius:12px;background:#e5e7eb;');

            $html .= "<figure style=\"{$figureStyle}\">";
            if ($url) {
                $html .= "<img src=\"{$url}\" alt=\"{$caption}\" class=\"{$aspect} h-full w-full object-cover transition hover:scale-105\" />";
            } else {
                $html .= "<div class=\"{$aspect} flex items-center justify-center bg-gray-200 text-sm text-gray-400\">Sin imagen</div>";
            }
            if ($caption) {
                $cStyle = $captionColor ? "style=\"color:{$captionColor}\"" : 'style="color:#6b7280"';
                $html .= "<figcaption class=\"px-3 py-2 text-xs\" {$cStyle}>" . e($caption) . '</figcaption>';
            }
            $html .= '</figure>';
        }
        $html .= '</div></div></section>';

        return $html;
    }

    private function renderTimeline(array $data): string
    {
        $items    = $data['items'] ?? [];
        $bgImage  = $this->imageUrl($data['bg_image'] ?? null);
        $opacity  = intval($data['overlay_opacity'] ?? 0);
        $bgClass  = match ($data['bg'] ?? 'white') {
            'primary' => 'bg-primary text-white',
            'surface' => 'bg-surface',
            default   => 'bg-white',
        };
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>" : '';
        $posClass     = $bgImage ? ' relative overflow-hidden' : '';
        $zClass       = $bgImage ? ' relative z-10' : '';

        $html  = "<section class=\"cms-block cms-block--timeline {$bgClass}{$posClass} py-16\"{$sectionStyle}>{$overlayHtml}";
        $html .= "<div class=\"mx-auto max-w-screen-lg px-6 md:px-8{$zClass}\">";
        if (!empty($data['title'])) {
            $html .= '<h2 class="mb-12 text-3xl font-black text-primary">' . e($data['title']) . '</h2>';
        }
        $html .= '<div class="relative border-l-2 border-primary/30 pl-8 space-y-10">';
        foreach ($items as $item) {
            $accentColor = $this->resolveColorStyle((string) ($item['accent_color'] ?? '#1a2236')) ?: '#1a2236';
            $iconColor   = $this->resolveColorStyle((string) ($item['icon_color'] ?? '#ffffff')) ?: '#ffffff';
            $dateColor   = $this->resolveColorStyle((string) ($item['date_color'] ?? ''));
            $titleColor  = $this->resolveColorStyle((string) ($item['title_color'] ?? '#111111'));
            $descColor   = $this->resolveColorStyle((string) ($item['description_color'] ?? '#6b7280'));

            $html .= '<div class="relative">';
            $html .= '<span class="absolute -left-[2.65rem] top-1 flex h-5 w-5 items-center justify-center rounded-full ring-4 ring-white" style="background-color:' . e($accentColor) . '">';
            if (!empty($item['icon'])) {
                $html .= '<span class="material-symbols-outlined text-[11px]" style="color:' . e($iconColor) . '">' . e($item['icon']) . '</span>';
            }
            $html .= '</span>';
            if (!empty($item['date'])) {
                $dStyle = $dateColor ? "style=\"color:{$dateColor}\"" : 'style="opacity:0.6"';
                $html .= '<p class="mb-1 text-xs font-bold uppercase tracking-widest" ' . $dStyle . '>' . e($item['date']) . '</p>';
            }
            if (!empty($item['title'])) {
                $tStyle = $titleColor ? "style=\"color:{$titleColor}\"" : 'style="color:#111111"';
                $html .= "<h3 class=\"text-xl font-black\" {$tStyle}>" . e($item['title']) . '</h3>';
            }
            if (!empty($item['description'])) {
                $descStyle = $descColor ? "style=\"color:{$descColor}\"" : 'style="color:#6b7280"';
                $html .= "<p class=\"mt-1 text-base leading-7\" {$descStyle}>" . e($item['description']) . '</p>';
            }
            $html .= '</div>';
        }
        $html .= '</div></div></section>';

        return $html;
    }

    private function renderQuoteHighlight(array $data): string
    {
        $bgImage = $this->imageUrl($data['bg_image'] ?? null);
        $opacity = intval($data['overlay_opacity'] ?? 0);
        $bg      = match ($data['bg'] ?? 'surface') {
            'primary' => 'bg-primary text-white',
            'white'   => 'bg-white text-primary',
            default   => 'bg-surface text-primary',
        };
        $authorImage  = $this->imageUrl($data['author_image'] ?? null);
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>" : '';
        $posClass     = $bgImage ? ' relative overflow-hidden' : '';
        $zClass       = $bgImage ? ' relative z-10' : '';
        $authorImageStyle = $this->buildMediaFrameStyle($data, 'author_image');

        $quoteColor  = $this->resolveColorStyle((string) ($data['quote_color'] ?? ''));
        $authorColor = $this->resolveColorStyle((string) ($data['author_color'] ?? ''));

        $html  = "<section class=\"cms-block cms-block--quote-highlight {$bg}{$posClass} py-20\"{$sectionStyle}>{$overlayHtml}";
        $html .= "<div class=\"mx-auto max-w-screen-md px-6 text-center md:px-8{$zClass}\">";
        $html .= '<div class="select-none text-9xl leading-none opacity-10">&ldquo;</div>';
        if (!empty($data['quote'])) {
            $qStyle = $quoteColor ? " style=\"color:{$quoteColor}\"" : '';
            $html .= "<blockquote class=\"-mt-8 text-2xl font-bold italic leading-relaxed\"{$qStyle}>" . e($data['quote']) . '</blockquote>';
        }
        if (!empty($data['author_name'])) {
            $aStyle = $authorColor ? " style=\"color:{$authorColor}\"" : '';
            $html .= "<div class=\"mt-8 flex items-center justify-center gap-3\"{$aStyle}>";
            if ($authorImage) {
                $html .= '<div style="display:inline-block;height:2.75rem;width:2.75rem;' . $authorImageStyle . '"><img src="' . e($authorImage) . '" alt="' . e($data['author_name']) . '" class="h-full w-full object-cover" /></div>';
            }
            $html .= '<div class="text-left">';
            $html .= '<p class="font-bold">' . e($data['author_name']) . '</p>';
            if (!empty($data['author_title'])) {
                $html .= '<p class="text-sm opacity-60">' . e($data['author_title']) . '</p>';
            }
            $html .= '</div></div>';
        }
        $html .= '</div></section>';

        return $html;
    }

    private function imageUrl(mixed $image): ?string
    {
        if (!$image) {
            return null;
        }

        if (is_array($image)) {
            // Prefer relative 'path' (e.g. "media/original/img.png") so the URL is
            // resolved via Storage::url() and works on both the CMS and website.
            // Fall back to 'url' (may include /storage/ prefix) or 'filename'.
            $candidate = $image['path'] ?? $image['url'] ?? $image['filename'] ?? null;
        } else {
            $candidate = data_get($image, 'path') ?: data_get($image, 'url') ?: data_get($image, 'filename');
        }

        if (!$candidate) {
            return null;
        }

        if (str_starts_with($candidate, 'http://') || str_starts_with($candidate, 'https://')) {
            return $candidate;
        }

        // Strip /storage/ prefix so we always work with a relative storage path
        // that can be resolved correctly via Storage::url() on any disk.
        if (str_starts_with($candidate, '/storage/')) {
            $candidate = ltrim(substr($candidate, 9), '/');
        } elseif (str_starts_with($candidate, '/')) {
            // Other absolute paths returned as-is (e.g. already a public URL).
            return $candidate;
        }

        // Use the original variant if only the flat basename was stored.
        if (!str_contains($candidate, '/')) {
            $candidate = 'media/original/' . $candidate;
        }

        return Storage::url($candidate);
    }

    private function imageAlt(mixed $image, string $fallback): string
    {
        if (is_array($image)) {
            return (string) ($image['alt_text'] ?? $image['alt'] ?? $image['filename'] ?? $fallback);
        }

        $alt = data_get($image, 'alt_text') ?: data_get($image, 'alt') ?: data_get($image, 'filename');

        return (string) ($alt ?: $fallback);
    }

    private function resolveColorStyle(string $value): string
    {
        return match ($value) {
            'primary'     => '#1a2236',
            'secondary'   => '#f59e0b',
            'surface'     => '#f5f6fa',
            'white'       => '#ffffff',
            'transparent' => 'transparent',
            ''            => '',
            default       => (str_starts_with($value, '#') || str_starts_with($value, 'rgb')) ? $value : '',
        };
    }

    /**
     * Build an inline CSS style string for per-item card styling
     * from card_bg, card_border, card_border_width, card_border_color,
     * card_radius, card_shadow, card_padding fields.
     */
    private function buildItemCardStyle(array $item): string
    {
        $radiusMap  = ['none' => '0', 'sm' => '8px', 'md' => '12px', 'lg' => '20px', 'full' => '9999px'];
        $shadowMap  = [
            'none' => 'none',
            'sm'   => '0 1px 4px rgba(0,0,0,0.08)',
            'md'   => '0 4px 12px rgba(0,0,0,0.12)',
            'lg'   => '0 8px 24px rgba(0,0,0,0.15)',
        ];
        $paddingMap = ['none' => '0', 'sm' => '0.75rem', 'md' => '1.25rem', 'lg' => '2rem'];

        $bg      = (string) ($item['card_bg'] ?? '');
        $border  = (string) ($item['card_border'] ?? 'none');
        $bWidth  = intval($item['card_border_width'] ?? 1);
        $bColor  = (string) ($item['card_border_color'] ?? '#e5e7eb');
        $radius  = $radiusMap[$item['card_radius'] ?? 'none'] ?? '0';
        $shadow  = $shadowMap[$item['card_shadow']  ?? 'none'] ?? 'none';
        $padding = $paddingMap[$item['card_padding'] ?? 'none'] ?? '0';

        $style = '';
        if ($bg && $bg !== 'transparent') {
            $style .= "background-color:{$bg};";
        }
        if ($border === 'solid') {
            $style .= "border:{$bWidth}px solid {$bColor};";
        }
        if ($radius !== '0') {
            $style .= "border-radius:{$radius};";
        }
        if ($shadow !== 'none') {
            $style .= "box-shadow:{$shadow};";
        }
        if ($padding !== '0') {
            $style .= "padding:{$padding};";
        }
        return $style;
    }

    private function buildMediaFrameStyle(array $data, string $prefix): string
    {
        return $this->buildItemCardStyle([
            'card_bg' => '',
            'card_border' => (string) ($data[$prefix . '_border'] ?? 'none'),
            'card_border_width' => (string) ($data[$prefix . '_border_width'] ?? '1'),
            'card_border_color' => (string) ($data[$prefix . '_border_color'] ?? '#e5e7eb'),
            'card_radius' => (string) ($data[$prefix . '_radius'] ?? 'none'),
            'card_shadow' => (string) ($data[$prefix . '_shadow'] ?? 'none'),
            'card_padding' => 'none',
        ]);
    }

    private function applyCardWrapper(string $html, array $data): string
    {
        $bg     = $data['_wrap_bg']           ?? 'transparent';
        $border = $data['_wrap_border']        ?? 'none';
        $bWidth = $data['_wrap_border_width']  ?? '1';
        $bColor = $data['_wrap_border_color']  ?? '#e5e7eb';
        $radius = $data['_wrap_radius']        ?? 'none';
        $shadow = $data['_wrap_shadow']        ?? 'none';
        $px     = $data['_wrap_padding_x']     ?? 'none';
        $py     = $data['_wrap_padding_y']     ?? 'none';

        $isDefault = $bg === 'transparent' && $border === 'none' && $radius === 'none'
            && $shadow === 'none' && $px === 'none' && $py === 'none';

        if ($isDefault) {
            return $html;
        }

        $styles = [];

        $bgHex = $this->resolveColorStyle($bg);
        if ($bgHex && $bgHex !== 'transparent') {
            $styles[] = "background:{$bgHex}";
        }

        if ($border !== 'none') {
            $bColorHex = $this->resolveColorStyle($bColor) ?: $bColor;
            $styles[] = "border:{$bWidth}px {$border} {$bColorHex}";
        }

        $radiusMap = ['sm' => '4px', 'md' => '8px', 'lg' => '12px', 'xl' => '16px', '2xl' => '24px'];
        if (isset($radiusMap[$radius])) {
            $styles[] = "border-radius:{$radiusMap[$radius]}";
            $styles[] = "overflow:hidden";
        }

        $shadowMap = [
            'sm'  => '0 1px 2px rgba(0,0,0,0.05)',
            'md'  => '0 4px 6px rgba(0,0,0,0.07)',
            'lg'  => '0 10px 15px rgba(0,0,0,0.1)',
            'xl'  => '0 20px 25px rgba(0,0,0,0.1)',
            '2xl' => '0 25px 50px rgba(0,0,0,0.12)',
        ];
        if (isset($shadowMap[$shadow])) {
            $styles[] = "box-shadow:{$shadowMap[$shadow]}";
        }

        $pxMap = ['sm' => '1rem',    'md' => '1.5rem', 'lg' => '2rem', 'xl' => '3rem'];
        $pyMap = ['sm' => '0.75rem', 'md' => '1.25rem', 'lg' => '2rem', 'xl' => '3rem'];
        if (isset($pxMap[$px])) {
            $styles[] = "padding-left:{$pxMap[$px]};padding-right:{$pxMap[$px]}";
        }
        if (isset($pyMap[$py])) {
            $styles[] = "padding-top:{$pyMap[$py]};padding-bottom:{$pyMap[$py]}";
        }

        $styleStr = implode(';', $styles);

        return "<div class=\"cms-block-wrapper\" style=\"{$styleStr}\">{$html}</div>";
    }

    private function renderConfigurableCards(array $data): string
    {
        $cards    = $data['cards'] ?? [];
        $cols     = max(1, min(4, intval($data['cols'] ?? 3)));
        $gapMap   = ['sm' => 'gap-4', 'md' => 'gap-6', 'lg' => 'gap-8', 'xl' => 'gap-12'];
        $gap      = $gapMap[$data['gap'] ?? 'md'] ?? 'gap-6';
        $gapCssMap = ['sm' => '1rem', 'md' => '1.5rem', 'lg' => '2rem', 'xl' => '3rem'];
        $gapCss    = $gapCssMap[$data['gap'] ?? 'md'] ?? '1.5rem';
        $bgImage  = $this->imageUrl($data['bg_image'] ?? null);
        $opacity  = intval($data['overlay_opacity'] ?? 0);
        $bgClass  = match ($data['bg'] ?? 'white') {
            'primary' => 'bg-primary text-white',
            'surface' => 'bg-surface',
            default   => 'bg-white',
        };
        $sectionStyle = $bgImage
            ? " style=\"background-image:url('{$bgImage}');background-size:cover;background-position:center;\""
            : '';
        $overlayHtml  = $bgImage
            ? "<div class=\"absolute inset-0 bg-black\" style=\"opacity:{$opacity}%\"></div>"
            : '';
        $posClass = $bgImage ? ' relative overflow-hidden' : '';
        $zClass   = $bgImage ? ' relative z-10' : '';

        $html  = "<section class=\"cms-block cms-block--configurable-cards {$bgClass}{$posClass} py-16\"{$sectionStyle}>{$overlayHtml}";
        $html .= "<div class=\"mx-auto max-w-screen-2xl px-6 md:px-8{$zClass}\">";

        if (!empty($data['section_title'])) {
            $html .= '<h2 class="mb-4 text-3xl font-black text-primary">' . e($data['section_title']) . '</h2>';
        }
        if (!empty($data['section_subtitle'])) {
            $html .= '<p class="mb-8 text-base text-text-secondary">' . e($data['section_subtitle']) . '</p>';
        }

        $html .= "<div style=\"display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gapCss};\">";
        foreach ($cards as $card) {
            $html .= $this->renderConfigurableCard($card);
        }
        $html .= '</div>';
        $html .= '</div></section>';

        return $html;
    }

    private function renderConfigurableCard(array $card): string
    {
        $cardBgHex = $this->resolveColorStyle($card['card_bg'] ?? '#ffffff') ?: ($card['card_bg'] ?? '#ffffff');
        $border    = $card['card_border'] ?? 'none';
        $bWidth    = $card['card_border_width'] ?? '1';
        $bColorHex = $this->resolveColorStyle($card['card_border_color'] ?? '#e5e7eb') ?: ($card['card_border_color'] ?? '#e5e7eb');

        $radiusMap = ['none' => '0', 'sm' => '4px', 'md' => '8px', 'lg' => '12px', 'xl' => '16px', '2xl' => '24px'];
        $radius    = $radiusMap[$card['card_radius'] ?? 'lg'] ?? '12px';

        $shadowMap = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(0,0,0,0.05)',
            'md'   => '0 4px 6px rgba(0,0,0,0.07)',
            'lg'   => '0 10px 15px rgba(0,0,0,0.1)',
            'xl'   => '0 20px 25px rgba(0,0,0,0.1)',
            '2xl'  => '0 25px 50px rgba(0,0,0,0.12)',
        ];
        $shadow = $shadowMap[$card['card_shadow'] ?? 'md'] ?? '';

        $paddingMap = ['none' => '0', 'sm' => '0.75rem', 'md' => '1.25rem', 'lg' => '1.75rem', 'xl' => '2.5rem'];
        $padding    = $paddingMap[$card['card_padding'] ?? 'md'] ?? '1.25rem';

        $textAlignClass = match ($card['text_align'] ?? 'left') {
            'center' => 'text-center',
            'right'  => 'text-right',
            default  => 'text-left',
        };

        $borderStyle = $border !== 'none' ? "border:{$bWidth}px {$border} {$bColorHex};" : '';
        $shadowStyle = $shadow ? "box-shadow:{$shadow};" : '';
        $cardStyle   = "border-radius:{$radius};overflow:hidden;background:{$cardBgHex};{$borderStyle}{$shadowStyle}";

        $imgUrl      = $this->imageUrl($card['image'] ?? null);
        $imgPosition = $card['image_position'] ?? 'top';
        $aspectMap   = ['square' => 'aspect-square', 'photo' => 'aspect-[4/3]', 'video' => 'aspect-video', 'auto' => ''];
        $aspectClass = $aspectMap[$card['image_aspect'] ?? 'photo'] ?? 'aspect-[4/3]';

        $html = "<article style=\"{$cardStyle}\">";

        if ($imgUrl && $imgPosition === 'top') {
            $imgAlt = $this->imageAlt($card['image'] ?? null, $card['title'] ?? '');
            $html  .= "<div class=\"overflow-hidden\"><img src=\"" . e($imgUrl) . "\" alt=\"" . e($imgAlt) . "\" class=\"w-full object-cover {$aspectClass}\" /></div>";
        }

        $isHoriz = $imgUrl && in_array($imgPosition, ['left', 'right']);
        if ($isHoriz) {
            $flexDir = $imgPosition === 'right' ? 'flex-row-reverse' : 'flex-row';
            $imgAlt  = $this->imageAlt($card['image'] ?? null, $card['title'] ?? '');
            $html   .= "<div class=\"flex items-start {$flexDir}\">";
            $html   .= "<div class=\"w-1/3 flex-shrink-0 self-stretch overflow-hidden\"><img src=\"" . e($imgUrl) . "\" alt=\"" . e($imgAlt) . "\" class=\"h-full w-full object-cover\" /></div>";
            $html   .= "<div class=\"flex-1 {$textAlignClass}\" style=\"padding:{$padding}\">";
        } else {
            $html .= "<div class=\"{$textAlignClass}\" style=\"padding:{$padding}\">";
        }

        if (!empty($card['icon'])) {
            $iconColorHex   = $this->resolveColorStyle($card['icon_color'] ?? '#1a2236') ?: ($card['icon_color'] ?? '#1a2236');
            $iconAlignStyle = ($card['text_align'] ?? 'left') === 'center' ? 'margin-left:auto;margin-right:auto;' : '';
            $html .= "<div class=\"mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100\" style=\"color:{$iconColorHex};{$iconAlignStyle}\">";
            $html .= '<span class="material-symbols-outlined text-2xl">' . e($card['icon']) . '</span>';
            $html .= '</div>';
        }

        if (!empty($card['title'])) {
            $titleSizeMap  = ['sm' => '1rem', 'md' => '1.25rem', 'lg' => '1.5rem', 'xl' => '2rem', '2xl' => '2.5rem'];
            $titleSize     = $titleSizeMap[$card['title_size'] ?? 'lg'] ?? '1.5rem';
            $titleColorHex = $card['title_color'] ? ($this->resolveColorStyle($card['title_color']) ?: $card['title_color']) : '';
            $titleStyle    = "font-size:{$titleSize};" . ($titleColorHex ? "color:{$titleColorHex};" : '');
            $html .= "<h3 class=\"font-black leading-tight tracking-tight\" style=\"{$titleStyle}\">" . e($card['title']) . '</h3>';
        }

        if (!empty($card['subtitle'])) {
            $subtitleColorHex = $card['subtitle_color'] ? ($this->resolveColorStyle($card['subtitle_color']) ?: $card['subtitle_color']) : '';
            $subtitleStyle    = $subtitleColorHex ? " style=\"color:{$subtitleColorHex}\"" : '';
            $html .= "<p class=\"mt-1 text-sm font-semibold opacity-70\"{$subtitleStyle}>" . e($card['subtitle']) . '</p>';
        }

        if (!empty($card['body'])) {
            $bodyColorHex = $card['body_color'] ? ($this->resolveColorStyle($card['body_color']) ?: $card['body_color']) : '';
            $bodyStyle    = $bodyColorHex ? " style=\"color:{$bodyColorHex}\"" : '';
            $html .= "<p class=\"mt-3 text-sm leading-relaxed text-text-secondary\"{$bodyStyle}>" . e($card['body']) . '</p>';
        }

        if (!empty($card['cta_label'])) {
            $html .= '<div class="mt-4"><a href="' . e($card['cta_url'] ?: '#') . '" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">' . e($card['cta_label']) . ' &rarr;</a></div>';
        }

        $html .= '</div>';
        if ($isHoriz) {
            $html .= '</div>';
        }

        if ($imgUrl && $imgPosition === 'bottom') {
            $imgAlt = $this->imageAlt($card['image'] ?? null, $card['title'] ?? '');
            $html  .= "<div class=\"overflow-hidden\"><img src=\"" . e($imgUrl) . "\" alt=\"" . e($imgAlt) . "\" class=\"w-full object-cover {$aspectClass}\" /></div>";
        }

        $html .= '</article>';

        return $html;
    }

    // ─── Article Sections ────────────────────────────────────────────────────

    private function renderArticleSections(array $data): string
    {
        $sections = $data['sections'] ?? [];
        if (empty($sections)) {
            return '';
        }

        $html = '<div class="cms-article-sections space-y-8">';
        foreach ($sections as $section) {
            $type  = $section['type'] ?? 'text';
            $inner = match ($type) {
                'text'      => $this->renderArticleSectionText($section),
                'image'     => $this->renderArticleSectionImage($section),
                'video'     => $this->renderArticleSectionVideo($section),
                'quote'     => $this->renderArticleSectionQuote($section),
                'callout'   => $this->renderArticleSectionCallout($section),
                'gallery'   => $this->renderArticleSectionGallery($section),
                'embed'     => $this->renderArticleSectionEmbed($section),
                'card'      => $this->renderArticleSectionCard($section),
                'separator' => $this->renderArticleSectionSeparator($section),
                default     => '',
            };

            $html .= $this->renderArticleSectionBlockWrapper($section, $inner);
        }
        $html .= '</div>';

        return $html;
    }

    private function renderArticleSectionText(array $s): string
    {
        $content = (string) ($s['content'] ?? '');
        if (trim(strip_tags($content)) === '') {
            return '';
        }

        return '<div class="cms-article-section cms-article-section--text '
            . 'space-y-4 text-base leading-8 text-on-surface-variant '
            . '[&_h2]:mt-10 [&_h2]:text-3xl [&_h2]:font-black [&_h2]:tracking-tight [&_h2]:text-primary '
            . '[&_h3]:mt-8 [&_h3]:text-2xl [&_h3]:font-bold [&_h3]:text-primary '
            . '[&_p]:leading-8 [&_img]:my-6 [&_img]:w-full [&_img]:rounded-2xl [&_img]:object-cover '
            . '[&_blockquote]:border-l-4 [&_blockquote]:border-secondary [&_blockquote]:pl-6 [&_blockquote]:text-xl [&_blockquote]:italic [&_blockquote]:text-primary '
            . '[&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6">'
            . $content
            . '</div>';
    }

    private function renderArticleSectionImage(array $s): string
    {
        $url = $this->imageUrl($s['image'] ?? null);
        if (!$url) {
            return '';
        }

        $caption = (string) ($s['caption'] ?? '');
        $width   = match ($s['width'] ?? 'full') {
            'medium' => 'max-width:600px;',
            'wide'   => 'max-width:900px;',
            default  => '',
        };
        $align = match ($s['align'] ?? 'center') {
            'left'  => 'margin-right:auto;',
            'right' => 'margin-left:auto;',
            default => 'margin-left:auto;margin-right:auto;',
        };
        $figStyle = ($width || $align) ? ' style="' . $width . $align . '"' : '';
        $alt      = $this->imageAlt($s['image'] ?? null, $caption);

        $html  = "<figure class=\"cms-article-section cms-article-section--image my-8\"{$figStyle}>";
        $html .= "<img src=\"" . e($url) . "\" alt=\"" . e($alt) . "\" class=\"w-full rounded-2xl object-cover\" />";
        if ($caption !== '') {
            $html .= '<figcaption class="mt-3 text-center text-sm text-on-surface-variant">' . e($caption) . '</figcaption>';
        }
        $html .= '</figure>';

        return $html;
    }

    private function renderArticleSectionVideo(array $s): string
    {
        $url = trim((string) ($s['url'] ?? ''));
        if ($url === '') {
            return '';
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_\-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
        } else {
            $embedUrl = e($url);
        }

        $caption = (string) ($s['caption'] ?? '');

        $html  = '<figure class="cms-article-section cms-article-section--video my-8">';
        $html .= '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:1rem">';
        $html .= "<iframe src=\"{$embedUrl}\" style=\"position:absolute;top:0;left:0;width:100%;height:100%;border:0\" allowfullscreen loading=\"lazy\" title=\"" . e($caption ?: 'Video') . "\"></iframe>";
        $html .= '</div>';
        if ($caption !== '') {
            $html .= '<figcaption class="mt-3 text-center text-sm text-on-surface-variant">' . e($caption) . '</figcaption>';
        }
        $html .= '</figure>';

        return $html;
    }

    private function renderArticleSectionQuote(array $s): string
    {
        $text   = (string) ($s['text'] ?? '');
        $author = (string) ($s['author_name'] ?? '');
        $title  = (string) ($s['author_title'] ?? '');

        if (trim($text) === '') {
            return '';
        }

        $html  = '<blockquote class="cms-article-section cms-article-section--quote my-10 border-l-4 border-secondary py-2 pl-6">';
        $html .= '<p class="text-xl font-semibold italic leading-8 text-primary">' . e($text) . '</p>';
        if ($author !== '') {
            $html .= '<footer class="mt-4 text-sm font-bold text-secondary">' . e($author);
            if ($title !== '') {
                $html .= '<span class="font-normal text-on-surface-variant">, ' . e($title) . '</span>';
            }
            $html .= '</footer>';
        }
        $html .= '</blockquote>';

        return $html;
    }

    private function renderArticleSectionCallout(array $s): string
    {
        $title = (string) ($s['title'] ?? '');
        $body  = (string) ($s['body'] ?? '');
        $type  = match ($s['callout_type'] ?? 'info') {
            'warning' => 'warning',
            'success' => 'success',
            default   => 'info',
        };

        if (trim($title) === '' && trim($body) === '') {
            return '';
        }

        [$borderColor, $bgColor, $textColor, $iconName] = match ($type) {
            'warning' => ['border-amber-400', 'bg-amber-50', 'text-amber-900', 'warning'],
            'success' => ['border-green-400', 'bg-green-50', 'text-green-900', 'check_circle'],
            default   => ['border-blue-400', 'bg-blue-50', 'text-blue-900', 'info'],
        };

        $html  = "<div class=\"cms-article-section cms-article-section--callout my-8 rounded-xl border-l-4 {$borderColor} {$bgColor} {$textColor} p-5\">";
        $html .= '<div class="flex items-start gap-3">';
        $html .= "<span class=\"material-symbols-outlined text-xl leading-none\">{$iconName}</span>";
        $html .= '<div class="flex-1">';
        if ($title !== '') {
            $html .= '<p class="mb-1 text-sm font-bold">' . e($title) . '</p>';
        }
        if ($body !== '') {
            $html .= '<p class="text-sm leading-6">' . e($body) . '</p>';
        }
        $html .= '</div></div></div>';

        return $html;
    }

    private function renderArticleSectionGallery(array $s): string
    {
        $images = $s['images'] ?? [];
        if (empty($images) || !is_array($images)) {
            return '';
        }

        $cols = intval($s['cols'] ?? 3);
        if ($cols < 2) $cols = 2;
        if ($cols > 4) $cols = 4;

        $aspect = $s['aspect'] ?? 'photo';
        $paddingMap = ['photo' => '75%', 'square' => '100%', 'video' => '56.25%'];
        $paddingBottom = $paddingMap[$aspect] ?? '75%';

        $width = $s['width'] ?? 'content';
        $containerStart = $width === 'full' ? '' : '<div style="max-width:900px;margin-left:auto;margin-right:auto">';
        $containerEnd = $width === 'full' ? '' : '</div>';

        $html = '<figure class="cms-article-section cms-article-section--gallery my-8">';
        $html .= $containerStart;
        $html .= '<div style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:1rem">';

        foreach ($images as $item) {
            $url = $this->imageUrl($item ?? null);
            $alt = $this->imageAlt($item ?? null, '');
            $caption = is_array($item) ? ($item['caption'] ?? '') : (data_get($item, 'caption') ?: '');
            if (!$url) {
                $html .= '<div style="background:#e5e7eb;padding-bottom:' . $paddingBottom . ';border-radius:12px"></div>';
            } else {
                $html .= '<div>';
                $html .= '<div style="position:relative;padding-bottom:' . $paddingBottom . ';overflow:hidden;border-radius:12px">';
                $html .= '<img src="' . e($url) . '" alt="' . e($alt) . '" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" />';
                $html .= '</div>';
                if ($caption !== '') {
                    $html .= '<figcaption class="mt-2 text-sm text-on-surface-variant">' . e($caption) . '</figcaption>';
                }
                $html .= '</div>';
            }
        }

        $html .= '</div>' . $containerEnd;
        $html .= '</figure>';

        return $html;
    }

    private function renderArticleSectionEmbed(array $s): string
    {
        $htmlRaw = trim((string) ($s['html'] ?? ''));
        $url = trim((string) ($s['url'] ?? ''));
        $caption = (string) ($s['caption'] ?? '');

        if ($htmlRaw !== '') {
            return '<figure class="cms-article-section cms-article-section--embed my-8">' . $htmlRaw . ($caption ? '<figcaption class="mt-3 text-center text-sm text-on-surface-variant">' . e($caption) . '</figcaption>' : '') . '</figure>';
        }

        if ($url === '') {
            return '';
        }

        $embedUrl = $url;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_\-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
        } else {
            $embedUrl = e($url);
        }

        $html = '<figure class="cms-article-section cms-article-section--embed my-8">';
        $html .= '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:1rem">';
        $html .= '<iframe src="' . $embedUrl . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0" allowfullscreen loading="lazy" title="' . e($caption ?: 'Embed') . '"></iframe>';
        $html .= '</div>';
        if ($caption !== '') {
            $html .= '<figcaption class="mt-3 text-center text-sm text-on-surface-variant">' . e($caption) . '</figcaption>';
        }
        $html .= '</figure>';

        return $html;
    }

    private function renderArticleSectionCard(array $s): string
    {
        $variant = $s['card_variant'] ?? 'content';
        $sizeMap   = ['sm' => '0.875rem', 'base' => '1rem', 'lg' => '1.125rem', 'xl' => '1.25rem', '2xl' => '1.5rem', '3xl' => '1.875rem'];
        $weightMap = ['normal' => '400', 'medium' => '500', 'semibold' => '600', 'bold' => '700', 'extrabold' => '800', 'black' => '900'];
        $lhMap     = ['tight' => '1.25', 'snug' => '1.375', 'normal' => '1.5', 'relaxed' => '1.65', 'loose' => '2'];

        // Card wrapper styles
        $styles = [];
        if (!empty($s['card_bg'])) {
            $bg = $this->resolveColorStyle((string) $s['card_bg']);
            if ($bg) $styles[] = "background:{$bg}";
        }
        if (!empty($s['card_border']) && $s['card_border'] !== 'none') {
            $bw = intval($s['card_border_width'] ?? 1);
            $bc = $this->resolveColorStyle((string) ($s['card_border_color'] ?? '#e5e7eb'));
            $styles[] = "border:{$bw}px {$s['card_border']} {$bc}";
        }
        $radMap = ['none' => '', 'sm' => '4px', 'md' => '8px', 'lg' => '12px', 'xl' => '16px', '2xl' => '24px'];
        if (!empty($s['card_radius']) && !empty($radMap[$s['card_radius']])) {
            $styles[] = "border-radius:{$radMap[$s['card_radius']]}";
            $styles[] = 'overflow:hidden';
        }
        $shMap = ['none' => '', 'sm' => '0 1px 2px rgba(0,0,0,0.05)', 'md' => '0 4px 6px rgba(0,0,0,0.07)', 'lg' => '0 10px 15px rgba(0,0,0,0.1)', 'xl' => '0 20px 25px rgba(0,0,0,0.1)'];
        if (!empty($s['card_shadow']) && !empty($shMap[$s['card_shadow']])) {
            $styles[] = "box-shadow:{$shMap[$s['card_shadow']]}";
        }
        $pdMap = ['none' => '', 'sm' => '0.75rem', 'md' => '1.25rem', 'lg' => '2rem', 'xl' => '3rem'];
        if (!empty($s['card_padding']) && !empty($pdMap[$s['card_padding']])) {
            $styles[] = "padding:{$pdMap[$s['card_padding']]}";
        }
        if (!empty($s['card_align']) && in_array($s['card_align'], ['left', 'center', 'right'])) {
            $styles[] = "text-align:{$s['card_align']}";
        }
        $maxW = match ($s['card_width'] ?? 'full') {
            'wide'   => 'max-width:900px;margin-left:auto;margin-right:auto;',
            'medium' => 'max-width:640px;margin-left:auto;margin-right:auto;',
            'narrow' => 'max-width:480px;margin-left:auto;margin-right:auto;',
            default  => '',
        };
        $cardStyle = $maxW . ($styles ? implode(';', $styles) : '');

        // Text helpers
        $titleHtml = $subtitleHtml = $parasHtml = '';
        if (!empty($s['title']) && $variant !== 'text_only') {
            $fs     = $sizeMap[$s['title_size'] ?? 'xl'] ?? '1.25rem';
            $fw     = $weightMap[$s['title_weight'] ?? 'bold'] ?? '700';
            $lhv    = $lhMap[$s['title_line_height'] ?? 'tight'] ?? '1.25';
            $fstyle = !empty($s['title_italic']) ? 'italic' : 'normal';
            $color  = $this->resolveColorStyle((string) ($s['title_color'] ?? '')) ?: '#111827';
            $titleHtml = "<h3 style=\"margin:0 0 0.5rem;font-size:{$fs};font-weight:{$fw};font-style:{$fstyle};line-height:{$lhv};color:{$color}\">" . e($s['title']) . '</h3>';
        }
        if (!empty($s['subtitle']) && in_array($variant, ['content', 'image_text'])) {
            $fs     = $sizeMap[$s['subtitle_size'] ?? 'sm'] ?? '0.875rem';
            $fw     = $weightMap[$s['subtitle_weight'] ?? 'medium'] ?? '500';
            $fstyle = !empty($s['subtitle_italic']) ? 'italic' : 'normal';
            $color  = $this->resolveColorStyle((string) ($s['subtitle_color'] ?? '')) ?: '#6b7280';
            $subtitleHtml = "<p style=\"margin:0 0 0.75rem;font-size:{$fs};font-weight:{$fw};font-style:{$fstyle};color:{$color}\">" . e($s['subtitle']) . '</p>';
        }
        if (is_array($s['paragraphs'] ?? null)) {
            foreach ($s['paragraphs'] as $p) {
                if (empty(trim((string) ($p['text'] ?? '')))) continue;
                $fs     = $sizeMap[$p['size'] ?? 'base'] ?? '1rem';
                $fw     = $weightMap[$p['weight'] ?? 'normal'] ?? '400';
                $lhv    = $lhMap[$p['line_height'] ?? 'relaxed'] ?? '1.65';
                $fstyle = !empty($p['italic']) ? 'italic' : 'normal';
                $color  = $this->resolveColorStyle((string) ($p['color'] ?? '')) ?: '#374151';
                $parasHtml .= "<p style=\"margin:0 0 0.75rem;font-size:{$fs};font-weight:{$fw};font-style:{$fstyle};line-height:{$lhv};color:{$color}\">" . e($p['text']) . '</p>';
            }
        }

        $innerHtml = '';
        switch ($variant) {
            case 'quote':
                $accent  = $this->resolveColorStyle((string) ($s['quote_accent_color'] ?? '#f59e0b')) ?: '#f59e0b';
                $qstyle  = $s['quote_style'] ?? 'border_left';
                $qsize   = $sizeMap[$s['quote_size'] ?? 'xl'] ?? '1.25rem';
                $qcolor  = $this->resolveColorStyle((string) ($s['quote_text_color'] ?? '')) ?: '#1a2236';
                $acolor  = $this->resolveColorStyle((string) ($s['quote_author_color'] ?? '')) ?: '#f59e0b';
                $qtext   = (string) ($s['quote_text'] ?? '');
                if ($qtext === '') break;
                $authorHtml = '';
                if (!empty($s['quote_author'])) {
                    $authorHtml = "<footer style=\"margin-top:0.6rem;font-size:0.85rem;font-weight:700;color:{$acolor}\">— " . e($s['quote_author']);
                    if (!empty($s['quote_org'])) $authorHtml .= '<span style="font-weight:400;color:#6b7280">, ' . e($s['quote_org']) . '</span>';
                    $authorHtml .= '</footer>';
                }
                if ($qstyle === 'quotes') {
                    $innerHtml = "<div style=\"position:relative;padding:1.5rem 1.5rem 1rem\"><span style=\"position:absolute;top:0;left:0;font-size:4rem;line-height:1;color:{$accent};opacity:0.25;font-family:Georgia,serif\">&ldquo;</span><p style=\"font-size:{$qsize};font-style:italic;font-weight:600;color:{$qcolor};line-height:1.7;margin:0;position:relative\">" . e($qtext) . "</p>{$authorHtml}</div>";
                } elseif ($qstyle === 'minimal') {
                    $innerHtml = "<div><p style=\"font-size:{$qsize};font-style:italic;color:{$qcolor};line-height:1.7;margin:0\">" . e($qtext) . "</p>{$authorHtml}</div>";
                } else {
                    $innerHtml = "<blockquote style=\"border-left:4px solid {$accent};padding:0.5rem 0 0.5rem 1.25rem;margin:0\"><p style=\"font-size:{$qsize};font-style:italic;font-weight:600;color:{$qcolor};line-height:1.7;margin:0\">" . e($qtext) . "</p>{$authorHtml}</blockquote>";
                }
                break;

            case 'image_text':
                $imgUrl  = $this->imageUrl($s['image'] ?? null);
                $imgAlt  = $this->imageAlt($s['image'] ?? null, '');
                $pos     = $s['image_position'] ?? 'right';
                $iw      = $s['image_width'] ?? '40%';
                $irMap   = ['none' => '0', 'sm' => '4px', 'md' => '8px', 'lg' => '12px', 'xl' => '16px', 'full' => '9999px'];
                $ir      = $irMap[$s['image_radius'] ?? 'md'] ?? '8px';
                $apPad   = match ($s['image_aspect'] ?? 'photo') {
                    'square' => '100%',
                    'video' => '56.25%',
                    'auto' => null,
                    default => '75%',
                };
                $imgEl   = $imgUrl
                    ? ($apPad
                        ? "<div style=\"position:relative;padding-bottom:{$apPad};overflow:hidden;border-radius:{$ir}\"><img src=\"" . e($imgUrl) . "\" alt=\"" . e($imgAlt) . "\" style=\"position:absolute;inset:0;width:100%;height:100%;object-fit:cover\" /></div>"
                        : "<img src=\"" . e($imgUrl) . "\" alt=\"" . e($imgAlt) . "\" style=\"width:100%;border-radius:{$ir};display:block\" />")
                    : "<div style=\"background:#e5e7eb;padding-bottom:75%;border-radius:{$ir}\"></div>";
                $textEl  = $titleHtml . $subtitleHtml . $parasHtml;
                if (in_array($pos, ['top', 'bottom'])) {
                    $innerHtml = $pos === 'top'
                        ? "{$imgEl}<div style=\"padding-top:1rem\">{$textEl}</div>"
                        : "{$textEl}<div style=\"padding-top:1rem\">{$imgEl}</div>";
                } else {
                    $dir = $pos === 'left' ? '' : ';flex-direction:row-reverse';
                    $innerHtml = "<div style=\"display:flex;gap:1.5rem;align-items:flex-start{$dir}\"><div style=\"flex-shrink:0;width:{$iw}\">{$imgEl}</div><div style=\"flex:1\">{$textEl}</div></div>";
                }
                break;

            case 'gallery_card':
                $imgs    = is_array($s['gallery_images'] ?? null) ? $s['gallery_images'] : [];
                $gcols   = max(2, min(4, intval($s['gallery_cols'] ?? 2)));
                $gAspect = match ($s['gallery_aspect'] ?? 'photo') {
                    'square' => '100%',
                    'video' => '56.25%',
                    default => '75%',
                };
                if (empty($imgs)) break;
                $gitems = '';
                foreach ($imgs as $img) {
                    $u   = $this->imageUrl($img);
                    $alt = $this->imageAlt($img, '');
                    $gitems .= $u
                        ? "<div style=\"position:relative;padding-bottom:{$gAspect};overflow:hidden;border-radius:8px\"><img src=\"" . e($u) . "\" alt=\"" . e($alt) . "\" style=\"position:absolute;inset:0;width:100%;height:100%;object-fit:cover\" /></div>"
                        : "<div style=\"background:#e5e7eb;padding-bottom:{$gAspect};border-radius:8px\"></div>";
                }
                $innerHtml = "<div style=\"display:grid;grid-template-columns:repeat({$gcols},1fr);gap:0.5rem\">{$gitems}</div>";
                break;

            case 'bg_card':
                $bgUrl   = $this->imageUrl($s['bg_image'] ?? null);
                $ov      = min(100, max(0, intval($s['bg_overlay'] ?? 50)));
                $ovc     = $this->resolveColorStyle((string) ($s['bg_overlay_color'] ?? '#000000')) ?: '#000000';
                $content = $titleHtml . $subtitleHtml . $parasHtml;
                if ($bgUrl) {
                    $innerHtml = "<div style=\"position:relative;overflow:hidden;min-height:200px;background:url('" . e($bgUrl) . "') center/cover no-repeat;border-radius:inherit\"><div style=\"position:absolute;inset:0;background:{$ovc};opacity:" . ($ov / 100) . "\"></div><div style=\"position:relative;z-index:1;padding:2rem;color:white\">{$content}</div></div>";
                } else {
                    $innerHtml = $content;
                }
                break;

            default: // content, text_only
                $innerHtml = $titleHtml . $subtitleHtml . $parasHtml;
                break;
        }

        if (trim($innerHtml) === '') {
            return '';
        }
        $styleAttr = $cardStyle ? " style=\"{$cardStyle}\"" : '';
        return "<div class=\"cms-article-section cms-article-section--card\"{$styleAttr}>{$innerHtml}</div>";
    }

    private function renderArticleSectionSeparator(array $s): string
    {
        return match ($s['style'] ?? 'line') {
            'dots'  => '<div class="cms-article-section my-10 flex justify-center gap-3">'
                . '<span class="inline-block h-1.5 w-1.5 rounded-full bg-secondary opacity-50"></span>'
                . '<span class="inline-block h-1.5 w-1.5 rounded-full bg-secondary opacity-50"></span>'
                . '<span class="inline-block h-1.5 w-1.5 rounded-full bg-secondary opacity-50"></span>'
                . '</div>',
            'space' => '<div class="cms-article-section my-16"></div>',
            default => '<hr class="cms-article-section my-10 border-border" />',
        };
    }

    private function renderArticleSectionBlockWrapper(array $s, string $innerHtml): string
    {
        if (trim($innerHtml) === '') {
            return '';
        }

        $styles = [];

        if (!empty($s['block_bg'])) {
            $bg = $this->resolveColorStyle((string) $s['block_bg']);
            if ($bg !== '') {
                $styles[] = "background:{$bg}";
            }
        }

        if (!empty($s['block_border']) && $s['block_border'] !== 'none') {
            $bw = intval($s['block_border_width'] ?? 1);
            $bcolor = $this->resolveColorStyle((string) ($s['block_border_color'] ?? '#e5e7eb'));
            $styles[] = "border:{$bw}px {$s['block_border']} {$bcolor}";
        }

        $radiusMap = ['none' => '0', 'sm' => '8px', 'md' => '12px', 'lg' => '16px', 'xl' => '20px', '2xl' => '28px'];
        if (!empty($s['block_radius']) && isset($radiusMap[$s['block_radius']])) {
            $styles[] = "border-radius:{$radiusMap[$s['block_radius']]}";
            $styles[] = 'overflow:hidden';
        }

        $shadowMap = [
            'none' => '',
            'sm' => '0 1px 2px rgba(0,0,0,0.05)',
            'md' => '0 4px 6px rgba(0,0,0,0.07)',
            'lg' => '0 10px 15px rgba(0,0,0,0.1)',
            'xl' => '0 20px 25px rgba(0,0,0,0.1)',
            '2xl' => '0 25px 50px rgba(0,0,0,0.12)',
        ];
        if (!empty($s['block_shadow']) && isset($shadowMap[$s['block_shadow']]) && $shadowMap[$s['block_shadow']]) {
            $styles[] = "box-shadow:{$shadowMap[$s['block_shadow']]}";
        }

        $paddingMap = ['none' => '', 'sm' => '0.75rem', 'md' => '1.25rem', 'lg' => '2rem', 'xl' => '3rem'];
        if (!empty($s['block_padding']) && isset($paddingMap[$s['block_padding']]) && $paddingMap[$s['block_padding']]) {
            $styles[] = "padding:{$paddingMap[$s['block_padding']]}";
        }

        if (!empty($s['text_align'])) {
            $ta = in_array($s['text_align'], ['left', 'center', 'right']) ? $s['text_align'] : 'left';
            $styles[] = "text-align:{$ta}";
        }

        if (!empty($s['text_size'])) {
            if ($s['text_size'] === 'lg') $styles[] = 'font-size:1.125rem';
            if ($s['text_size'] === 'xl') $styles[] = 'font-size:1.25rem';
        }

        if (empty($styles)) {
            return $innerHtml;
        }

        $styleAttr = implode(';', $styles);
        return '<div class="cms-article-section-wrapper" style="' . $styleAttr . '">' . $innerHtml . '</div>';
    }
}
