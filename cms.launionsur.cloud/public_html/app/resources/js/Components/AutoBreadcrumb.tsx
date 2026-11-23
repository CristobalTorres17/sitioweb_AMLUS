import { useMemo } from 'react';
import { usePage } from '@inertiajs/react';
import Breadcrumb, { type BreadcrumbItem } from '@/Components/Breadcrumb';

type SectionConfig = {
    match: (pathname: string) => boolean;
    label: string;
    href: string;
    key:
        | 'dashboard'
        | 'pages'
        | 'media'
        | 'users'
        | 'roles'
        | 'permissions'
        | 'settings'
        | 'menus'
        | 'profile'
        | 'articles'
        | 'events'
        | 'banners'
        | 'services'
        | 'serviceCategories'
        | 'categories'
        | 'cmsBlocks'
        | 'auditLogs'
        | 'mediaCollections';
};

function stripQuery(url: string): string {
    return url.split('?')[0].replace(/\/+$/, '') || '/';
}

function humanize(value: string): string {
    return value
        .replace(/[-_]/g, ' ')
        .replace(/\b\w/g, letter => letter.toUpperCase())
        .trim();
}

function resolveSection(pathname: string): SectionConfig | null {
    const sections: SectionConfig[] = [
        {
            match: path => path === '/dashboard',
            label: 'Inicio',
            href: route('dashboard'),
            key: 'dashboard',
        },
        {
            match: path => path.startsWith('/admin/pages'),
            label: 'Páginas',
            href: route('pages.index'),
            key: 'pages',
        },
        {
            match: path =>
                path.startsWith('/media-collections') ||
                path.startsWith('/media-tags') ||
                path.startsWith('/media'),
            label: 'Medios',
            href: route('media.index'),
            key: 'media',
        },
        {
            match: path => path.startsWith('/admin/users'),
            label: 'Usuarios',
            href: route('admin.users.index'),
            key: 'users',
        },
        {
            match: path => path.startsWith('/admin/roles'),
            label: 'Roles',
            href: route('admin.roles.index'),
            key: 'roles',
        },
        {
            match: path => path.startsWith('/admin/permissions'),
            label: 'Permisos',
            href: route('admin.permissions.index'),
            key: 'permissions',
        },
        {
            match: path => path.startsWith('/admin/settings'),
            label: 'Configuración',
            href: route('admin.settings.index'),
            key: 'settings',
        },
        {
            match: path => path.startsWith('/admin/menus'),
            label: 'Menús',
            href: route('admin.menus.index'),
            key: 'menus',
        },
        {
            match: path => path.startsWith('/profile'),
            label: 'Perfil',
            href: route('profile.edit'),
            key: 'profile',
        },
        {
            match: path => path.startsWith('/articles'),
            label: 'Artículos',
            href: route('articles.index'),
            key: 'articles',
        },
        {
            match: path => path.startsWith('/events'),
            label: 'Eventos',
            href: route('events.index'),
            key: 'events',
        },
        {
            match: path => path.startsWith('/banners'),
            label: 'Banners',
            href: route('banners.index'),
            key: 'banners',
        },
        {
            match: path => path.startsWith('/services'),
            label: 'Servicios',
            href: route('services.index'),
            key: 'services',
        },
        {
            match: path => path.startsWith('/service-categories'),
            label: 'Categorías de servicios',
            href: route('service-categories.index'),
            key: 'serviceCategories',
        },
        {
            match: path => path.startsWith('/categories'),
            label: 'Categorías',
            href: route('categories.index'),
            key: 'categories',
        },
        {
            match: path => path.startsWith('/cms-blocks'),
            label: 'Bloques CMS',
            href: route('cms-blocks.index'),
            key: 'cmsBlocks',
        },
        {
            match: path => path.startsWith('/admin/audit-logs'),
            label: 'Auditoría',
            href: route('admin.audit-logs'),
            key: 'auditLogs',
        },
    ];

    return sections.find(section => section.match(pathname)) || null;
}

function resolveActionLabel(
    pathname: string,
    props: any,
    sectionKey: SectionConfig['key'],
): string | null {
    if (pathname.endsWith('/create')) {
        return 'Crear';
    }

    if (pathname.endsWith('/edit') || pathname === '/profile') {
        switch (sectionKey) {
            case 'pages':
                return props.page?.title || 'Editar';
            case 'media':
                return props.media?.original_filename || props.media?.filename || 'Editar';
            case 'users':
                return props.user?.name || 'Editar';
            case 'roles':
                return props.role?.display_name || props.role?.name || 'Editar';
            case 'permissions':
                return props.permission?.display_name || props.permission?.name || 'Editar';
            case 'settings':
                return props.setting?.key || 'Editar';
            case 'menus':
                return props.menu?.name || 'Editar';
            case 'articles':
                return props.article?.title || 'Editar';
            case 'events':
                return props.event?.title || 'Editar';
            case 'banners':
                return props.banner?.title || 'Editar';
            case 'services':
                return props.service?.name || 'Editar';
            case 'categories':
                return props.category?.name || 'Editar';
            case 'serviceCategories':
                return props.serviceCategory?.name || 'Editar';
            case 'cmsBlocks':
                return props.cmsBlock?.name || props.cmsBlock?.title || 'Editar';
            case 'profile':
                return 'Editar perfil';
            case 'auditLogs':
                return 'Detalle';
            default:
                return 'Editar';
        }
    }

    if (pathname.includes('/versions')) {
        return 'Historial';
    }

    if (pathname.includes('/revisions')) {
        return 'Versiones';
    }

    if (pathname.endsWith('/list')) {
        return null;
    }

    return null;
}

export default function AutoBreadcrumb() {
    const { props, url } = usePage<any>();

    return useMemo(() => {
        const pathname = stripQuery(url);
        const section = resolveSection(pathname);

        if (!section) {
            if (pathname === '/dashboard') {
                return <Breadcrumb items={[{ label: 'Inicio', href: null, current: true }]} />;
            }

            return null;
        }

        const items: BreadcrumbItem[] = [
            {
                label: 'Inicio',
                href: route('dashboard'),
                current: false,
            },
        ];

        const actionLabel = resolveActionLabel(pathname, props, section.key);
        const isSectionCurrent = !actionLabel;

        items.push({
            label: section.label,
            href: isSectionCurrent ? null : section.href,
            current: isSectionCurrent,
        });

        if (actionLabel) {
            items.push({
                label: humanize(actionLabel),
                href: null,
                current: true,
            });
        }

        return <Breadcrumb items={items} />;
    }, [props, url]);
}
