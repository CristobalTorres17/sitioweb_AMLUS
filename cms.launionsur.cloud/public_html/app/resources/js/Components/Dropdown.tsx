import { Transition } from '@headlessui/react';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import {
    createContext,
    Dispatch,
    PropsWithChildren,
    SetStateAction,
    useContext,
    useState,
    useRef,
    useEffect,
    useId,
} from 'react';

const DropDownContext = createContext<{
    open: boolean;
    setOpen: Dispatch<SetStateAction<boolean>>;
    toggleOpen: () => void;
    id: string;
}>({
    open: false,
    setOpen: () => {},
    toggleOpen: () => {},
    id: '',
});

const Dropdown = ({ children }: PropsWithChildren) => {
    const [open, setOpen] = useState(false);
    const id = useId();

    const toggleOpen = () => {
        setOpen(previousState => !previousState);
    };

    return (
        <DropDownContext.Provider value={{ open, setOpen, toggleOpen, id }}>
            <div className="relative">{children}</div>
        </DropDownContext.Provider>
    );
};

const Trigger = ({ children }: PropsWithChildren) => {
    const { open, setOpen, toggleOpen, id } = useContext(DropDownContext);

    function onKeyDown(e: React.KeyboardEvent) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleOpen();
        }
        if (e.key === 'Escape') {
            setOpen(false);
        }
    }

    return (
        <>
            <button
                id={`dropdown-${id}-trigger`}
                type="button"
                aria-haspopup="true"
                aria-expanded={open}
                aria-controls={`dropdown-${id}`}
                onClick={toggleOpen}
                onKeyDown={onKeyDown}
                className="inline-flex items-center"
            >
                {children}
            </button>

            {open && <div className="fixed inset-0 z-40" onClick={() => setOpen(false)} />}
        </>
    );
};

const Content = ({
    align = 'right',
    width = '48',
    contentClasses = 'py-1 bg-white',
    children,
}: PropsWithChildren<{
    align?: 'left' | 'right';
    width?: '48';
    contentClasses?: string;
}>) => {
    const { open, setOpen, id } = useContext(DropDownContext);
    const containerRef = useRef<HTMLDivElement | null>(null);

    let alignmentClasses = 'origin-top';

    if (align === 'left') {
        alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (align === 'right') {
        alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
    }

    let widthClasses = '';

    if (width === '48') {
        widthClasses = 'w-48';
    }

    useEffect(() => {
        if (!open) return;

        const container = containerRef.current;
        if (!container) return;

        const focusableSelector =
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
        const focusable = Array.from(
            container.querySelectorAll<HTMLElement>(focusableSelector),
        ).filter(el => el.offsetParent !== null);

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (first) first.focus();

        function onKeyDown(e: KeyboardEvent) {
            if (e.key === 'Escape') {
                setOpen(false);
                return;
            }

            if (e.key === 'Tab') {
                if (focusable.length === 0) {
                    e.preventDefault();
                    return;
                }
                const active = document.activeElement as HTMLElement | null;
                if (!active) return;

                if (e.shiftKey) {
                    if (active === first) {
                        e.preventDefault();
                        last?.focus();
                    }
                } else {
                    if (active === last) {
                        e.preventDefault();
                        first?.focus();
                    }
                }
            }
        }

        document.addEventListener('keydown', onKeyDown);
        return () => document.removeEventListener('keydown', onKeyDown);
    }, [open, setOpen]);

    return (
        <>
            <Transition
                show={open}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 scale-95"
                enterTo="opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="opacity-100 scale-100"
                leaveTo="opacity-0 scale-95"
            >
                <div
                    id={`dropdown-${id}`}
                    ref={containerRef}
                    role="menu"
                    aria-labelledby={`dropdown-${id}-trigger`}
                    className={`absolute z-50 mt-2 rounded-md shadow-lg ${alignmentClasses} ${widthClasses}`}
                    onClick={() => setOpen(false)}
                >
                    <div
                        className={`rounded-md ring-1 ring-black ring-opacity-5 ` + contentClasses}
                    >
                        {children}
                    </div>
                </div>
            </Transition>
        </>
    );
};

const DropdownLink = ({ className = '', children, ...props }: InertiaLinkProps) => {
    return (
        <Link
            {...props}
            className={
                'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none ' +
                className
            }
        >
            {children}
        </Link>
    );
};

Dropdown.Trigger = Trigger;
Dropdown.Content = Content;
Dropdown.Link = DropdownLink;

export default Dropdown;
