import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class', '[data-theme="dark"]'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            colors: {
                /* Legacy brand aliases (RGB triplets kept in CSS vars) */
                'laus-1': 'rgb(var(--laus-1) / <alpha-value>)',
                'laus-2': 'rgb(var(--laus-2) / <alpha-value>)',
                'laus-3': 'rgb(var(--laus-3) / <alpha-value>)',
                'laus-4': 'rgb(var(--laus-4) / <alpha-value>)',
                'laus-5': 'rgb(var(--laus-5) / <alpha-value>)',

                /* Semantic colors mapped to CSS variables */
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                'primary-alt': 'rgb(var(--color-primary-alt) / <alpha-value>)',
                'primary-hover': 'rgb(var(--color-primary-hover) / <alpha-value>)',
                'primary-tint': 'rgb(var(--color-primary-tint) / <alpha-value>)',

                gold: 'rgb(var(--color-gold) / <alpha-value>)',
                'gold-hover': 'rgb(var(--color-gold-hover) / <alpha-value>)',
                'gold-tint': 'rgb(var(--color-gold-tint) / <alpha-value>)',
                beige: 'rgb(var(--color-beige) / <alpha-value>)',
                brown: 'rgb(var(--color-brown) / <alpha-value>)',

                bg: 'rgb(var(--color-bg) / <alpha-value>)',
                'bg-secondary': 'rgb(var(--color-bg-secondary) / <alpha-value>)',
                surface: 'rgb(var(--color-surface) / <alpha-value>)',

                border: 'rgb(var(--color-border) / <alpha-value>)',
                'border-strong': 'rgb(var(--color-border-strong) / <alpha-value>)',

                'text-primary': 'rgb(var(--color-text-primary) / <alpha-value>)',
                'text-secondary': 'rgb(var(--color-text-secondary) / <alpha-value>)',
                'text-muted': 'rgb(var(--color-text-muted) / <alpha-value>)',
                'text-inverse': 'rgb(var(--color-text-inverse) / <alpha-value>)',

                success: 'rgb(var(--color-success) / <alpha-value>)',
                'success-bg': 'rgb(var(--color-success-bg) / <alpha-value>)',
                'success-border': 'rgb(var(--color-success-border) / <alpha-value>)',

                warning: 'rgb(var(--color-warning) / <alpha-value>)',
                'warning-bg': 'rgb(var(--color-warning-bg) / <alpha-value>)',
                'warning-border': 'rgb(var(--color-warning-border) / <alpha-value>)',

                error: 'rgb(var(--color-error) / <alpha-value>)',
                'error-bg': 'rgb(var(--color-error-bg) / <alpha-value>)',
                'error-border': 'rgb(var(--color-error-border) / <alpha-value>)',

                info: 'rgb(var(--color-info) / <alpha-value>)',
                'info-bg': 'rgb(var(--color-info-bg) / <alpha-value>)',
                'info-border': 'rgb(var(--color-info-border) / <alpha-value>)',
            },
            textColor: {
                primary: 'rgb(var(--color-text-primary) / <alpha-value>)',
                secondary: 'rgb(var(--color-text-secondary) / <alpha-value>)',
                muted: 'rgb(var(--color-text-muted) / <alpha-value>)',
                inverse: 'rgb(var(--color-text-inverse) / <alpha-value>)',
            },
            backgroundColor: {
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                gold: 'rgb(var(--color-gold) / <alpha-value>)',
                surface: 'rgb(var(--color-surface) / <alpha-value>)',
                'success-bg': 'rgb(var(--color-success-bg) / <alpha-value>)',
                'error-bg': 'rgb(var(--color-error-bg) / <alpha-value>)',
            },
            borderColor: {
                primary: 'rgb(var(--color-border) / <alpha-value>)',
                strong: 'rgb(var(--color-border-strong) / <alpha-value>)',
                success: 'rgb(var(--color-success-border) / <alpha-value>)',
                error: 'rgb(var(--color-error-border) / <alpha-value>)',
            },
            ringColor: {
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                gold: 'rgb(var(--color-gold) / <alpha-value>)',
                error: 'rgb(var(--color-error) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
