import { createContext, useContext, useEffect, useState, ReactNode } from 'react';

type DesktopState = 'expanded' | 'collapsed';

type ContextValue = {
    desktopState: DesktopState;
    setDesktopState: (s: DesktopState) => void;
    toggleCollapse: () => void;
    burgerOpen: boolean;
    openBurger: () => void;
    closeBurger: () => void;
};

const SidebarContext = createContext<ContextValue>({
    desktopState: 'expanded',
    setDesktopState: () => {},
    toggleCollapse: () => {},
    burgerOpen: false,
    openBurger: () => {},
    closeBurger: () => {},
});

export function SidebarProvider({ children }: { children: ReactNode }) {
    const [desktopState, setDesktopState] = useState<DesktopState>(() => {
        if (typeof window === 'undefined') return 'expanded';
        const saved = localStorage.getItem('sidebarDesktop') as DesktopState | null;
        return saved ?? 'expanded';
    });

    const [burgerOpen, setBurgerOpen] = useState<boolean>(false);

    useEffect(() => {
        try {
            localStorage.setItem('sidebarDesktop', desktopState);
        } catch {}
    }, [desktopState]);

    function toggleCollapse() {
        setDesktopState(s => (s === 'expanded' ? 'collapsed' : 'expanded'));
    }

    function openBurger() {
        setBurgerOpen(true);
    }

    function closeBurger() {
        setBurgerOpen(false);
    }

    return (
        <SidebarContext.Provider
            value={{
                desktopState,
                setDesktopState,
                toggleCollapse,
                burgerOpen,
                openBurger,
                closeBurger,
            }}
        >
            {children}
        </SidebarContext.Provider>
    );
}

export function useSidebar() {
    return useContext(SidebarContext);
}

export default SidebarContext;
