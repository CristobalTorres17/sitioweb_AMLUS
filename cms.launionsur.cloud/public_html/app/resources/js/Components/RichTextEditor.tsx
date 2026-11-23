import React, { useState } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import Image from '@tiptap/extension-image';
import MediaPickerModal from './MediaPickerModal';

interface RichTextEditorProps {
    value?: string;
    onChange?: (html: string) => void;
    placeholder?: string;
    disabled?: boolean;
    className?: string;
}

const RichTextEditor: React.FC<RichTextEditorProps> = ({
    value = '',
    onChange,
    placeholder = 'Escribe aquí...',
    disabled = false,
    className = '',
}) => {
    const [showLinkInput, setShowLinkInput] = useState(false);
    const [linkUrl, setLinkUrl] = useState('');
    const [isPickerOpen, setIsPickerOpen] = useState(false);

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                link: false,
                underline: false,
            }),
            Image,
            Underline,
            Link.configure({
                openOnClick: false,
            }),
        ],
        content: value,
        onUpdate: ({ editor }) => {
            onChange?.(editor.getHTML());
        },
        editable: !disabled,
    });

    if (!editor) {
        return <div className="text-gray-500">Cargando editor...</div>;
    }

    const handleAddLink = () => {
        if (linkUrl) {
            editor.chain().focus().extendMarkRange('link').setLink({ href: linkUrl }).run();
            setLinkUrl('');
            setShowLinkInput(false);
        }
    };

    const fileInputRef = React.createRef<HTMLInputElement>();

    const uploadImage = async (file: File) => {
        const fd = new FormData();
        fd.append('file', file);

        const res = await fetch(route('media.store'), {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-TOKEN': (
                    document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
                ).content,
            },
        });

        if (!res.ok) return null;

        const json = await res.json();
        return json.url || json.path || null;
    };

    const handleImageButton = () => {
        fileInputRef.current?.click();
    };

    const handleMediaSelect = (media: any, caption: string, altText: string) => {
        // Insert the selected image with caption and alt text
        editor
            .chain()
            .focus()
            .setImage({
                src: media.url,
                alt: altText || media.filename,
                title: caption || media.filename,
            })
            .run();

        // Store metadata in a data attribute (will be synced to DB via syncMediaFromContent)
        const htmlContent = editor.getHTML();
        onChange?.(htmlContent);
    };

    const onFileChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const f = e.target.files?.[0];
        if (!f) return;
        const url = await uploadImage(f);
        if (url) {
            editor.chain().focus().setImage({ src: url }).run();
        }
    };

    const toggleBold = () => editor.chain().focus().toggleBold().run();
    const toggleItalic = () => editor.chain().focus().toggleItalic().run();
    const toggleUnderline = () => editor.chain().focus().toggleUnderline().run();
    const toggleBulletList = () => editor.chain().focus().toggleBulletList().run();
    const toggleOrderedList = () => editor.chain().focus().toggleOrderedList().run();
    const toggleBlockquote = () => editor.chain().focus().toggleBlockquote().run();
    const toggleH1 = () => editor.chain().focus().toggleHeading({ level: 1 }).run();
    const toggleH2 = () => editor.chain().focus().toggleHeading({ level: 2 }).run();
    const toggleH3 = () => editor.chain().focus().toggleHeading({ level: 3 }).run();
    const toggleParagraph = () => editor.chain().focus().setParagraph().run();

    const buttonClass = (isActive: boolean) =>
        `p-2 rounded transition ${
            isActive ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'
        } disabled:opacity-50 disabled:cursor-not-allowed`;

    return (
        <div className={`border border-gray-300 rounded-lg overflow-hidden bg-white ${className}`}>
            {/* Toolbar */}
            <div className="bg-gray-100 p-3 border-b border-gray-300 flex flex-wrap gap-1">
                {/* Headings / Normal text */}
                <div className="flex gap-1 border-r border-gray-300 pr-2">
                    <button
                        type="button"
                        onClick={toggleParagraph}
                        disabled={disabled}
                        title="Texto normal"
                        className={buttonClass(editor.isActive('paragraph'))}
                    >
                        Texto
                    </button>
                    <button
                        type="button"
                        onClick={toggleH1}
                        disabled={disabled}
                        title="Encabezado 1"
                        className={buttonClass(editor.isActive('heading', { level: 1 }))}
                    >
                        H1
                    </button>
                    <button
                        type="button"
                        onClick={toggleH2}
                        disabled={disabled}
                        title="Encabezado 2"
                        className={buttonClass(editor.isActive('heading', { level: 2 }))}
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        onClick={toggleH3}
                        disabled={disabled}
                        title="Encabezado 3"
                        className={buttonClass(editor.isActive('heading', { level: 3 }))}
                    >
                        H3
                    </button>
                </div>

                {/* Text formatting */}
                <div className="flex gap-1 border-r border-gray-300 pr-2">
                    <button
                        type="button"
                        onClick={toggleBold}
                        disabled={disabled}
                        title="Negrita (Ctrl+B)"
                        className={buttonClass(editor.isActive('bold'))}
                    >
                        <strong>B</strong>
                    </button>
                    <button
                        type="button"
                        onClick={toggleItalic}
                        disabled={disabled}
                        title="Cursiva (Ctrl+I)"
                        className={buttonClass(editor.isActive('italic'))}
                    >
                        <em>I</em>
                    </button>
                    <button
                        type="button"
                        onClick={toggleUnderline}
                        disabled={disabled}
                        title="Subrayado (Ctrl+U)"
                        className={buttonClass(editor.isActive('underline'))}
                    >
                        <u>U</u>
                    </button>
                </div>

                {/* Lists */}
                <div className="flex gap-1 border-r border-gray-300 pr-2">
                    <button
                        type="button"
                        onClick={toggleBulletList}
                        disabled={disabled}
                        title="Lista con viñetas"
                        className={buttonClass(editor.isActive('bulletList'))}
                    >
                        ◦ Viñetas
                    </button>
                    <button
                        type="button"
                        onClick={toggleOrderedList}
                        disabled={disabled}
                        title="Lista numerada"
                        className={buttonClass(editor.isActive('orderedList'))}
                    >
                        1. Numerada
                    </button>
                </div>

                {/* Blockquote */}
                <div className="flex gap-1 border-r border-gray-300 pr-2">
                    <button
                        type="button"
                        onClick={toggleBlockquote}
                        disabled={disabled}
                        title="Cita"
                        className={buttonClass(editor.isActive('blockquote'))}
                    >
                        ❝ Cita
                    </button>
                </div>

                {/* Link */}
                <div className="flex gap-1">
                    <button
                        type="button"
                        onClick={() => setShowLinkInput(!showLinkInput)}
                        disabled={disabled}
                        title="Agregar enlace"
                        className={buttonClass(editor.isActive('link'))}
                    >
                        🔗 Enlace
                    </button>
                    <button
                        type="button"
                        onClick={handleImageButton}
                        disabled={disabled}
                        title="Insertar imagen"
                        className={buttonClass(false)}
                    >
                        🖼️ Subir
                    </button>
                    <button
                        type="button"
                        onClick={() => setIsPickerOpen(true)}
                        disabled={disabled}
                        title="Galería de imágenes"
                        className={buttonClass(false)}
                    >
                        🏞️ Galería
                    </button>
                    <input
                        ref={fileInputRef}
                        type="file"
                        accept="image/*"
                        onChange={onFileChange}
                        className="hidden"
                    />
                </div>
            </div>

            {/* Link input */}
            {showLinkInput && (
                <div className="bg-blue-50 p-3 border-b border-gray-300 flex gap-2">
                    <input
                        type="url"
                        placeholder="https://ejemplo.com"
                        value={linkUrl}
                        onChange={e => setLinkUrl(e.target.value)}
                        onKeyDown={e => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                handleAddLink();
                            }
                        }}
                        className="flex-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        autoFocus
                    />
                    <button
                        type="button"
                        onClick={handleAddLink}
                        disabled={!linkUrl || disabled}
                        className="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Agregar
                    </button>
                    <button
                        type="button"
                        onClick={() => {
                            setShowLinkInput(false);
                            setLinkUrl('');
                        }}
                        disabled={disabled}
                        className="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Cancelar
                    </button>
                </div>
            )}

            {/* Editor */}
            <div className="p-4 min-h-[300px] focus-within:outline-none prose prose-sm max-w-none">
                <EditorContent editor={editor} />
            </div>

            {/* Info text */}
            <div className="bg-gray-50 p-2 border-t border-gray-300 text-xs text-gray-600">
                Soporta: Texto normal, encabezados, negrita, cursiva, subrayado, listas, citas y
                enlaces.
            </div>

            {/* Media Picker Modal */}
            <MediaPickerModal
                isOpen={isPickerOpen}
                onClose={() => setIsPickerOpen(false)}
                onSelect={handleMediaSelect}
            />
        </div>
    );
};

export default RichTextEditor;
