import React, { useEffect, useRef, useState } from 'react';
import {
    FolderIcon,
    FolderOpenIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    PhotoIcon,
    NewspaperIcon,
    CalendarDaysIcon,
    BriefcaseIcon,
} from '@heroicons/react/24/outline';

export interface CollectionNode {
    id: number;
    name: string;
    slug: string;
    color: string;
    icon: string;
    media_count: number;
    collectable_type?: string | null;
    collectable_id?: number | null;
    children: CollectionNode[];
}

interface FolderSidebarProps {
    tree: CollectionNode[];
    selectedCollectionId: number | null;
    onSelect: (id: number | null) => void;
}

// ─── Icon map ────────────────────────────────────────────────────────────────
const ICON_MAP: Record<string, React.ElementType> = {
    calendar: CalendarDaysIcon,
    newspaper: NewspaperIcon,
    photo: PhotoIcon,
    briefcase: BriefcaseIcon,
};

function CollectionIcon({ icon, className }: { icon: string; className?: string }) {
    const Tag = ICON_MAP[icon] ?? FolderIcon;
    return <Tag className={className ?? 'h-4 w-4'} />;
}

function csrfToken(): string {
    return (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
}

// ─── Single tree node row ─────────────────────────────────────────────────────
function CollectionNodeRow({
    node,
    depth,
    selectedId,
    onSelect,
    onRefresh,
}: {
    node: CollectionNode;
    depth: number;
    selectedId: number | null;
    onSelect: (id: number | null) => void;
    onRefresh: () => void;
}) {
    const [open, setOpen] = useState(depth === 0);
    const [renaming, setRenaming] = useState(false);
    const [renameValue, setRenameValue] = useState(node.name);
    const [creating, setCreating] = useState(false);
    const [newName, setNewName] = useState('');
    const renameRef = useRef<HTMLInputElement>(null);
    const createRef = useRef<HTMLInputElement>(null);

    const isSelected = selectedId === node.id;

    useEffect(() => {
        if (renaming) renameRef.current?.focus();
    }, [renaming]);
    useEffect(() => {
        if (creating) createRef.current?.focus();
    }, [creating]);

    const handleRename = () => {
        const trimmed = renameValue.trim();
        if (!trimmed || trimmed === node.name) {
            setRenaming(false);
            return;
        }
        fetch(route('media-collections.update', node.id), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ name: trimmed }),
        }).then(() => {
            setRenaming(false);
            onRefresh();
        });
    };

    const handleDelete = () => {
        if (
            !confirm(
                `¿Eliminar carpeta "${node.name}"? Los archivos se moverán a la carpeta padre.`,
            )
        )
            return;
        fetch(route('media-collections.destroy', node.id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
        }).then(() => {
            if (selectedId === node.id) onSelect(null);
            onRefresh();
        });
    };

    const handleCreate = () => {
        const trimmed = newName.trim();
        if (!trimmed) {
            setCreating(false);
            return;
        }
        fetch(route('media-collections.store'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ name: trimmed, parent_id: node.id, color: node.color }),
        }).then(() => {
            setCreating(false);
            setNewName('');
            setOpen(true);
            onRefresh();
        });
    };

    const hasChildren = node.children.length > 0 || creating;

    return (
        <div>
            <div
                className={`group flex items-center gap-1 rounded-md px-2 py-1 cursor-pointer text-sm select-none transition-colors ${
                    isSelected ? 'bg-primary text-gold' : 'text-gray-700 hover:bg-gray-100'
                }`}
                style={{ paddingLeft: `${8 + depth * 14}px` }}
                onClick={() => onSelect(isSelected ? null : node.id)}
            >
                {/* Chevron */}
                <button
                    className="shrink-0 text-gray-400 hover:text-gray-600"
                    onClick={e => {
                        e.stopPropagation();
                        setOpen(o => !o);
                    }}
                >
                    {hasChildren ? (
                        open ? (
                            <ChevronDownIcon className="h-3.5 w-3.5" />
                        ) : (
                            <ChevronRightIcon className="h-3.5 w-3.5" />
                        )
                    ) : (
                        <span className="w-3.5 block" />
                    )}
                </button>

                {/* Icon */}
                <span className="shrink-0" style={{ color: node.color }}>
                    {open && node.children.length > 0 ? (
                        <FolderOpenIcon className="h-4 w-4" />
                    ) : (
                        <CollectionIcon icon={node.icon} className="h-4 w-4" />
                    )}
                </span>

                {/* Name / rename input */}
                {renaming ? (
                    <input
                        ref={renameRef}
                        value={renameValue}
                        onChange={e => setRenameValue(e.target.value)}
                        onBlur={handleRename}
                        onKeyDown={e => {
                            if (e.key === 'Enter') handleRename();
                            if (e.key === 'Escape') {
                                setRenaming(false);
                                setRenameValue(node.name);
                            }
                        }}
                        onClick={e => e.stopPropagation()}
                        className="flex-1 min-w-0 rounded border border-blue-400 px-1 text-xs text-gray-900 bg-white outline-none"
                    />
                ) : (
                    <span className="flex-1 truncate font-medium">{node.name}</span>
                )}

                {/* Count badge */}
                <span
                    className={`text-xs rounded-full px-1.5 shrink-0 ${
                        isSelected ? 'bg-white/20 text-gold' : 'bg-gray-100 text-gray-500'
                    }`}
                >
                    {node.media_count}
                </span>

                {/* Hover actions */}
                {!renaming && (
                    <div className="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-1">
                        <button
                            title="Nueva sub-carpeta"
                            onClick={e => {
                                e.stopPropagation();
                                setCreating(c => !c);
                                setOpen(true);
                            }}
                            className="rounded p-0.5 hover:bg-gray-200 text-gray-400 hover:text-gray-600"
                        >
                            <PlusIcon className="h-3.5 w-3.5" />
                        </button>
                        <button
                            title="Renombrar"
                            onClick={e => {
                                e.stopPropagation();
                                setRenaming(true);
                                setRenameValue(node.name);
                            }}
                            className="rounded p-0.5 hover:bg-gray-200 text-gray-400 hover:text-gray-600"
                        >
                            <PencilIcon className="h-3.5 w-3.5" />
                        </button>
                        <button
                            title="Eliminar carpeta"
                            onClick={e => {
                                e.stopPropagation();
                                handleDelete();
                            }}
                            className="rounded p-0.5 hover:bg-red-100 text-gray-400 hover:text-red-600"
                        >
                            <TrashIcon className="h-3.5 w-3.5" />
                        </button>
                    </div>
                )}
            </div>

            {/* Inline sub-folder create input */}
            {creating && (
                <div
                    style={{ paddingLeft: `${8 + (depth + 1) * 14 + 18}px` }}
                    className="pr-2 py-0.5"
                >
                    <input
                        ref={createRef}
                        value={newName}
                        placeholder="Nombre de carpeta..."
                        onChange={e => setNewName(e.target.value)}
                        onBlur={handleCreate}
                        onKeyDown={e => {
                            if (e.key === 'Enter') handleCreate();
                            if (e.key === 'Escape') {
                                setCreating(false);
                                setNewName('');
                            }
                        }}
                        onClick={e => e.stopPropagation()}
                        className="w-full rounded border border-blue-400 px-2 py-0.5 text-xs outline-none bg-blue-50"
                    />
                </div>
            )}

            {/* Children */}
            {open && node.children.length > 0 && (
                <div>
                    {node.children.map(child => (
                        <CollectionNodeRow
                            key={child.id}
                            node={child}
                            depth={depth + 1}
                            selectedId={selectedId}
                            onSelect={onSelect}
                            onRefresh={onRefresh}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}

// ─── Sidebar ─────────────────────────────────────────────────────────────────
export default function FolderSidebar({
    tree,
    selectedCollectionId,
    onSelect,
}: FolderSidebarProps) {
    const [localTree, setLocalTree] = useState<CollectionNode[]>(tree);
    const [creatingRoot, setCreatingRoot] = useState(false);
    const [newRootName, setNewRootName] = useState('');
    const rootInputRef = useRef<HTMLInputElement>(null);

    // Sync with server-side Inertia prop changes
    useEffect(() => {
        setLocalTree(tree);
    }, [tree]);

    useEffect(() => {
        if (creatingRoot) rootInputRef.current?.focus();
    }, [creatingRoot]);

    const refreshTree = () => {
        fetch('/api/media/collections')
            .then(r => r.json())
            .then(json => {
                if (json.success) setLocalTree(json.data as CollectionNode[]);
            });
    };

    const handleCreateRoot = () => {
        const trimmed = newRootName.trim();
        if (!trimmed) {
            setCreatingRoot(false);
            return;
        }
        fetch(route('media-collections.store'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ name: trimmed, parent_id: null }),
        }).then(() => {
            setCreatingRoot(false);
            setNewRootName('');
            refreshTree();
        });
    };

    return (
        <aside className="w-56 shrink-0 border-r border-gray-200 bg-white flex flex-col overflow-hidden">
            {/* Header */}
            <div className="flex items-center justify-between px-3 py-2 border-b border-gray-100">
                <span className="text-xs font-semibold uppercase tracking-wide text-gray-400">
                    Carpetas
                </span>
                <button
                    title="Nueva carpeta raíz"
                    onClick={() => setCreatingRoot(c => !c)}
                    className="rounded p-1 hover:bg-gray-100 text-gray-400 hover:text-gray-700"
                >
                    <PlusIcon className="h-4 w-4" />
                </button>
            </div>

            {/* "All files" entry */}
            <div className="px-2 pt-2">
                <button
                    onClick={() => onSelect(null)}
                    className={`w-full flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium transition-colors ${
                        selectedCollectionId === null
                            ? 'bg-primary text-gold'
                            : 'text-gray-700 hover:bg-gray-100'
                    }`}
                >
                    <PhotoIcon className="h-4 w-4 shrink-0 text-gray-400" />
                    Todos los archivos
                </button>
            </div>

            {/* Tree */}
            <div className="flex-1 overflow-y-auto py-2 px-1 space-y-0.5">
                {localTree.map(node => (
                    <CollectionNodeRow
                        key={node.id}
                        node={node}
                        depth={0}
                        selectedId={selectedCollectionId}
                        onSelect={onSelect}
                        onRefresh={refreshTree}
                    />
                ))}

                {/* Root create input */}
                {creatingRoot && (
                    <div className="px-2 py-1">
                        <input
                            ref={rootInputRef}
                            value={newRootName}
                            placeholder="Nombre de la carpeta..."
                            onChange={e => setNewRootName(e.target.value)}
                            onBlur={handleCreateRoot}
                            onKeyDown={e => {
                                if (e.key === 'Enter') handleCreateRoot();
                                if (e.key === 'Escape') {
                                    setCreatingRoot(false);
                                    setNewRootName('');
                                }
                            }}
                            className="w-full rounded border border-blue-400 px-2 py-1 text-xs outline-none bg-blue-50"
                        />
                    </div>
                )}
            </div>
        </aside>
    );
}
