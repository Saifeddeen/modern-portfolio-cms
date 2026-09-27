/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.ts',
        './resources/**/*.vue',
        './node_modules/primevue/**/*.{js,vue,ts}',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                // Soft, low-contrast grays for backgrounds and text
                'graphite': {
                    50: '#f8fafc',  // Main background (Light)
                    100: '#f1f5f9',
                    200: '#e2e8f0', // Borders (Light)
                    300: '#cbd5e1',
                    400: '#94a3b8', // Muted text
                    500: '#64748b', // Secondary text
                    600: '#475569', // Primary text
                    700: '#334155',
                    800: '#1e293b', // Sidebar / Cards (Dark)
                    900: '#0f172a', // Main background (Dark)
                    950: '#020617',
                },
                // Muted, sophisticated Iris for primary actions
                'iris': {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1', // Main Primary
                    600: '#4f46e5', // Hover state
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('tailwindcss-primeui'),
    ],
}