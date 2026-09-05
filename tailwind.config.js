import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                // Generalized Theme Colors
                primary: {
                    DEFAULT: '#4f46e5', // Indigo-600
                    hover: '#4338ca',   // Indigo-700
                },
                surface: {
                    DEFAULT: '#f8fafc', // Slate-50 (Light Mode)
                    dark: '#0f172a',    // Slate-900 (Dark Mode)
                },
                sidebar: {
                    DEFAULT: '#1e293b', // Slate-800
                    hover: '#334155',   // Slate-700
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
