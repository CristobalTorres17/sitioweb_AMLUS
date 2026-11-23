<?php

return [
    /**
     * Banner pages configuration
     *
     * List of pages available for banner assignment.
     * Add or remove pages as needed.
     */
    'banner_pages' => [
        'home'     => 'Home',
        'services' => 'Services',
        'articles' => 'Articles',
        'about'    => 'About',
        'contact'  => 'Contact',
        // District pages are merged dynamically in BannerController
    ],

    /**
     * CMS blocks pages configuration
     *
     * Pages where CMS blocks can be assigned.
     * Add `footer` for footer-only blocks.
     */
    'pages' => [
        'home' => 'Home',
        'about' => 'About',
        'integridad-transparencia' => 'Integridad y Transparencia',
        'projects' => 'Projects',
        'nosotros' => 'Nosotros',
        'faq' => 'FAQ',
        'mapa' => 'Mapa del Sitio',
        'contact' => 'Contact',
        'services' => 'Services',
        'articles' => 'Articles',
        'events' => 'Events',
        'footer' => 'Footer',
    ],
];
