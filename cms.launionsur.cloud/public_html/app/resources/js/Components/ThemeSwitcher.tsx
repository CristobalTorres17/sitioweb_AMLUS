import { useEffect, useState } from 'react';

export default function ThemeSwitcher() {
    const [theme, setTheme] = useState<string>(() => {
        try {
            return (
                localStorage.getItem('theme') ||
                document.documentElement.getAttribute('data-theme') ||
                'dark'
            );
        } catch {
            return 'dark';
        }
    });

    useEffect(() => {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        try {
            localStorage.setItem('theme', theme);
        } catch {}
    }, [theme]);

    return (
        <button
            type="button"
            onClick={() => setTheme(t => (t === 'dark' ? 'light' : 'dark'))}
            className="rounded-md border border-transparent bg-white/5 px-3 py-2 text-sm text-gray-700 hover:bg-white/10 dark:text-white"
            aria-label="Toggle theme"
        >
            {theme === 'dark' ? '🌙' : '☀️'}
        </button>
    );
}
