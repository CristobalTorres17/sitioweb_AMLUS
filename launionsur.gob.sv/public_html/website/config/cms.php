<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS page slugs
    |--------------------------------------------------------------------------
    | Maps Laravel route names to CMS page slugs. When a block is assigned to
    | a page slug it will appear on every route that resolves to that slug.
    |
    | Keep index and show pages under different slugs so editors can target
    | listing vs detail pages independently.
    */
    'pages' => [
        'home'           => 'home',

        // Services (listado vs detalle separados)
        'services.index' => 'services',
        'services.show'  => 'service-detail',

        // Trámites (listado vs detalle separados)
        'tramites.index' => 'tramites',
        'tramites.show'  => 'service-detail', // comparte slug con service-detail

        // Eventos
        'events.index'   => 'events',
        'events.show'    => 'event-detail',

        // Distritos
        'districts.index' => 'districts',
        'districts.show'  => 'district-detail',

        // Páginas institucionales
        'about'          => 'about',
        'team'           => 'about',
        'faq'            => 'faq',
        'pricing'        => 'pricing',
        'mapa'           => 'mapa',
        'integridad'     => 'integridad-transparencia',
        'projects'       => 'projects',
        'projects.show'  => 'project-detail',
        'testimonials'   => 'testimonials',
        'contact.show'   => 'contact',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS zones
    |--------------------------------------------------------------------------
    | Consistent set of placement zones available across all pages.
    |
    |  after_hero    — justo después del hero / banner principal
    |  mid_content   — entre las secciones principales de contenido
    |  before_footer — antes del CTA final / footer
    |  sidebar       — panel lateral (páginas de detalle)
    |  footer        — zona global del footer (compartida en todas las páginas)
    */
    'zones' => [
        'after_hero'    => 'Después del hero',
        'mid_content'   => 'Contenido intermedio',
        'before_footer' => 'Antes del footer',
        'sidebar'       => 'Barra lateral',
        'footer'        => 'Footer global',
    ],
];
