type TemplateDefinition = {
    label: string;
    description: string;
    zones: Record<string, string>;
};

type Props = {
    templates: Record<string, TemplateDefinition>;
    selectedKey: string;
    onSelect: (key: string) => void;
};

function RichTextThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="8" y="10" width="64" height="7" rx="3.5" fill="currentColor" opacity="0.7" />
            <rect x="8" y="23" width="60" height="4" rx="2" fill="currentColor" opacity="0.4" />
            <rect x="8" y="32" width="52" height="4" rx="2" fill="currentColor" opacity="0.4" />
            <rect x="8" y="41" width="44" height="4" rx="2" fill="currentColor" opacity="0.3" />
        </svg>
    );
}

function EditorialSplitThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="6" width="30" height="40" rx="4" fill="currentColor" opacity="0.45" />
            <rect x="16" y="16" width="20" height="28" rx="3" fill="currentColor" opacity="0.25" />
            <rect x="18" y="50" width="8" height="4" rx="2" fill="currentColor" opacity="0.5" />
            <rect x="40" y="10" width="36" height="5" rx="2.5" fill="currentColor" opacity="0.65" />
            <rect x="40" y="21" width="32" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="40" y="28" width="28" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="40" y="35" width="24" height="3" rx="1.5" fill="currentColor" opacity="0.3" />
            <rect x="40" y="44" width="18" height="7" rx="3.5" fill="currentColor" opacity="0.5" />
        </svg>
    );
}

function ValueCardsThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="3" y="8" width="22" height="44" rx="4" fill="currentColor" opacity="0.1" />
            <circle cx="14" cy="22" r="7" fill="currentColor" opacity="0.5" />
            <rect x="6" y="34" width="16" height="3" rx="1.5" fill="currentColor" opacity="0.45" />
            <rect
                x="7"
                y="41"
                width="13"
                height="2.5"
                rx="1.25"
                fill="currentColor"
                opacity="0.3"
            />

            <rect x="29" y="8" width="22" height="44" rx="4" fill="currentColor" opacity="0.1" />
            <circle cx="40" cy="22" r="7" fill="currentColor" opacity="0.5" />
            <rect x="32" y="34" width="16" height="3" rx="1.5" fill="currentColor" opacity="0.45" />
            <rect
                x="33"
                y="41"
                width="13"
                height="2.5"
                rx="1.25"
                fill="currentColor"
                opacity="0.3"
            />

            <rect x="55" y="8" width="22" height="44" rx="4" fill="currentColor" opacity="0.1" />
            <circle cx="66" cy="22" r="7" fill="currentColor" opacity="0.5" />
            <rect x="58" y="34" width="16" height="3" rx="1.5" fill="currentColor" opacity="0.45" />
            <rect
                x="59"
                y="41"
                width="13"
                height="2.5"
                rx="1.25"
                fill="currentColor"
                opacity="0.3"
            />
        </svg>
    );
}

function GenericThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="8" y="8" width="64" height="44" rx="6" fill="currentColor" opacity="0.1" />
            <rect x="20" y="20" width="40" height="5" rx="2.5" fill="currentColor" opacity="0.4" />
            <rect x="24" y="31" width="32" height="3" rx="1.5" fill="currentColor" opacity="0.3" />
            <rect x="28" y="39" width="24" height="3" rx="1.5" fill="currentColor" opacity="0.25" />
        </svg>
    );
}

function StatRowThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.07" />
            {[0, 1, 2, 3].map(i => (
                <g key={i} transform={`translate(${8 + i * 17}, 12)`}>
                    <rect width="13" height="13" rx="3" fill="currentColor" opacity="0.2" />
                    <rect
                        x="1"
                        y="28"
                        width="11"
                        height="5"
                        rx="2"
                        fill="currentColor"
                        opacity="0.5"
                    />
                    <rect
                        x="2"
                        y="35"
                        width="9"
                        height="2.5"
                        rx="1.25"
                        fill="currentColor"
                        opacity="0.3"
                    />
                </g>
            ))}
        </svg>
    );
}

function CtaBannerThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="14" width="72" height="32" rx="6" fill="currentColor" opacity="0.2" />
            <rect x="11" y="22" width="28" height="5" rx="2.5" fill="currentColor" opacity="0.6" />
            <rect x="11" y="30" width="20" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="53" y="22" width="14" height="16" rx="4" fill="currentColor" opacity="0.5" />
        </svg>
    );
}

function HeroSectionThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.15" />
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.1" />
            <rect x="22" y="12" width="36" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="14" y="19" width="52" height="7" rx="3" fill="currentColor" opacity="0.55" />
            <rect x="20" y="30" width="40" height="3" rx="1.5" fill="currentColor" opacity="0.3" />
            <rect x="28" y="38" width="24" height="8" rx="4" fill="currentColor" opacity="0.45" />
        </svg>
    );
}

function ImageGalleryThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.07" />
            {[0, 1, 2].map(i => (
                <g key={i} transform={`translate(${8 + i * 22}, 10)`}>
                    <rect width="18" height="14" rx="3" fill="currentColor" opacity="0.25" />
                    <rect
                        x="2"
                        y="16"
                        width="14"
                        height="2"
                        rx="1"
                        fill="currentColor"
                        opacity="0.2"
                    />
                    <rect width="18" height="14" rx="3" fill="currentColor" opacity="0" y="22" />
                    <rect y="22" width="18" height="14" rx="3" fill="currentColor" opacity="0.2" />
                    <rect
                        x="2"
                        y="38"
                        width="14"
                        height="2"
                        rx="1"
                        fill="currentColor"
                        opacity="0.15"
                    />
                </g>
            ))}
        </svg>
    );
}

function TimelineThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.07" />
            <rect x="22" y="8" width="2" height="44" rx="1" fill="currentColor" opacity="0.25" />
            {[0, 1, 2].map(i => (
                <g key={i} transform={`translate(0, ${i * 14})`}>
                    <circle cx="23" cy="16" r="3" fill="currentColor" opacity="0.5" />
                    <rect
                        x="30"
                        y="12"
                        width="14"
                        height="3"
                        rx="1.5"
                        fill="currentColor"
                        opacity="0.35"
                    />
                    <rect
                        x="30"
                        y="17"
                        width="22"
                        height="2"
                        rx="1"
                        fill="currentColor"
                        opacity="0.25"
                    />
                    <rect
                        x="10"
                        y="12"
                        width="7"
                        height="3"
                        rx="1.5"
                        fill="currentColor"
                        opacity="0.3"
                    />
                </g>
            ))}
        </svg>
    );
}

function QuoteHighlightThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            <rect x="4" y="4" width="72" height="52" rx="5" fill="currentColor" opacity="0.1" />
            <text x="14" y="22" fill="currentColor" opacity="0.4" fontSize="18" fontFamily="serif">
                "
            </text>
            <rect x="14" y="24" width="52" height="3" rx="1.5" fill="currentColor" opacity="0.4" />
            <rect x="18" y="30" width="44" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="22" y="36" width="36" height="3" rx="1.5" fill="currentColor" opacity="0.3" />
            <circle cx="35" cy="48" r="4" fill="currentColor" opacity="0.3" />
            <rect x="41" y="44" width="16" height="3" rx="1.5" fill="currentColor" opacity="0.35" />
            <rect x="41" y="49" width="11" height="2" rx="1" fill="currentColor" opacity="0.25" />
        </svg>
    );
}

function ArticleSectionsThumb() {
    return (
        <svg
            viewBox="0 0 80 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="h-full w-full"
        >
            {/* Section 1: text block */}
            <rect x="6" y="4" width="68" height="10" rx="2" fill="currentColor" opacity="0.12" />
            <rect
                x="9"
                y="6.5"
                width="40"
                height="2.5"
                rx="1.25"
                fill="currentColor"
                opacity="0.55"
            />
            <rect x="9" y="10.5" width="30" height="2" rx="1" fill="currentColor" opacity="0.35" />
            {/* Section 2: image */}
            <rect x="6" y="18" width="68" height="14" rx="2" fill="currentColor" opacity="0.2" />
            <circle cx="18" cy="25" r="4" fill="currentColor" opacity="0.35" />
            <polygon points="14,30 22,22 26,26 30,21 40,30" fill="currentColor" opacity="0.25" />
            {/* Section 3: quote */}
            <rect x="6" y="36" width="4" height="10" rx="2" fill="currentColor" opacity="0.55" />
            <rect
                x="13"
                y="38"
                width="50"
                height="2.5"
                rx="1.25"
                fill="currentColor"
                opacity="0.4"
            />
            <rect x="13" y="42" width="38" height="2" rx="1" fill="currentColor" opacity="0.3" />
            {/* Section 4: separator dots */}
            <circle cx="35" cy="52" r="1.5" fill="currentColor" opacity="0.4" />
            <circle cx="40" cy="52" r="1.5" fill="currentColor" opacity="0.4" />
            <circle cx="45" cy="52" r="1.5" fill="currentColor" opacity="0.4" />
        </svg>
    );
}

const THUMBS: Record<string, () => JSX.Element> = {
    rich_text: RichTextThumb,
    editorial_split_showcase: EditorialSplitThumb,
    value_cards_row: ValueCardsThumb,
    stat_row: StatRowThumb,
    cta_banner: CtaBannerThumb,
    hero_section: HeroSectionThumb,
    image_gallery: ImageGalleryThumb,
    timeline: TimelineThumb,
    quote_highlight: QuoteHighlightThumb,
    article_sections: ArticleSectionsThumb,
};

export default function TemplatePickerPanel({ templates, selectedKey, onSelect }: Props) {
    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {Object.entries(templates).map(([key, template]) => {
                const isSelected = key === selectedKey;
                const ThumbComp = THUMBS[key] ?? GenericThumb;

                return (
                    <button
                        key={key}
                        type="button"
                        onClick={() => onSelect(key)}
                        className={`group relative flex flex-col items-center gap-2 rounded-xl border-2 bg-white p-3 text-left transition-all hover:border-primary hover:shadow-md ${
                            isSelected
                                ? 'border-primary shadow-md shadow-primary/10'
                                : 'border-border'
                        }`}
                    >
                        {isSelected && (
                            <span className="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-white">
                                <svg viewBox="0 0 16 16" fill="currentColor" className="h-3 w-3">
                                    <path d="M13.3 4.3a1 1 0 0 1 0 1.4l-6 6a1 1 0 0 1-1.4 0l-3-3a1 1 0 1 1 1.4-1.4L6.6 9.6l5.3-5.3a1 1 0 0 1 1.4 0Z" />
                                </svg>
                            </span>
                        )}
                        <div
                            className={`h-16 w-full rounded-lg p-2 transition-colors ${
                                isSelected
                                    ? 'bg-primary/5 text-primary'
                                    : 'bg-gray-50 text-gray-400 group-hover:bg-primary/5 group-hover:text-primary'
                            }`}
                        >
                            <ThumbComp />
                        </div>
                        <div className="w-full">
                            <p
                                className={`text-xs font-semibold leading-tight ${
                                    isSelected ? 'text-primary' : 'text-gray-800'
                                }`}
                            >
                                {template.label}
                            </p>
                            <p className="mt-0.5 text-[10px] leading-snug text-text-secondary">
                                {template.description}
                            </p>
                        </div>
                    </button>
                );
            })}
        </div>
    );
}
