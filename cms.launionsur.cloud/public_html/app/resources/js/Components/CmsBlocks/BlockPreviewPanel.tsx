import { useRef } from 'react';

type Props = {
    html: string;
    blockName?: string;
};

export default function BlockPreviewPanel({ html, blockName }: Props) {
    const dialogRef = useRef<HTMLDialogElement>(null);

    if (!html) {
        return (
            <div className="flex h-32 items-center justify-center rounded-lg border border-dashed border-border bg-white text-sm text-text-secondary">
                Sin preview
            </div>
        );
    }

    return (
        <>
            {/* Miniatura expandible */}
            <div
                className="relative overflow-hidden rounded-lg border border-border bg-white"
                style={{ minHeight: '200px' }}
            >
                <div
                    style={{
                        transform: 'scale(0.35)',
                        transformOrigin: 'top left',
                        width: '285%',
                        pointerEvents: 'none',
                    }}
                    dangerouslySetInnerHTML={{ __html: html }}
                />
                {/* Degradado inferior para indicar que hay más contenido */}
                <div className="pointer-events-none absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-white/80 to-transparent" />
                <button
                    type="button"
                    onClick={() => dialogRef.current?.showModal()}
                    className="absolute bottom-2 right-2 flex items-center gap-1.5 rounded-md bg-white/90 px-2.5 py-1.5 text-xs font-semibold text-primary shadow-sm backdrop-blur-sm transition hover:bg-white hover:shadow"
                >
                    <svg
                        viewBox="0 0 16 16"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="1.6"
                        className="h-3.5 w-3.5"
                    >
                        <path
                            d="M10 2h4v4M9 7l5-5M6 14H2v-4M7 9l-5 5"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                    Ampliar
                </button>
            </div>

            {/* Modal nativo */}
            <dialog
                ref={dialogRef}
                className="m-auto w-full max-w-screen-lg rounded-2xl p-0 shadow-2xl backdrop:bg-black/50"
                onClick={e => {
                    if (e.target === dialogRef.current) dialogRef.current?.close();
                }}
            >
                <div className="flex items-center justify-between border-b border-border px-5 py-3">
                    <p className="text-sm font-semibold text-primary">
                        {blockName ? `Preview: ${blockName}` : 'Preview del bloque'}
                    </p>
                    <button
                        type="button"
                        onClick={() => dialogRef.current?.close()}
                        className="flex h-8 w-8 items-center justify-center rounded-lg text-text-secondary transition hover:bg-surface hover:text-primary"
                    >
                        <svg viewBox="0 0 16 16" fill="currentColor" className="h-4 w-4">
                            <path d="M4.293 4.293a1 1 0 0 1 1.414 0L8 6.586l2.293-2.293a1 1 0 1 1 1.414 1.414L9.414 8l2.293 2.293a1 1 0 0 1-1.414 1.414L8 9.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L6.586 8 4.293 5.707a1 1 0 0 1 0-1.414Z" />
                        </svg>
                    </button>
                </div>
                <div className="max-h-[80vh] overflow-y-auto">
                    {/* Material Symbols para que los íconos se rendericen en el modal */}
                    <link
                        rel="stylesheet"
                        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
                    />
                    <div dangerouslySetInnerHTML={{ __html: html }} />
                </div>
            </dialog>
        </>
    );
}
