import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Warna dinamis dari status_color (amber, blue, emerald, red, slate)
    // Tailwind JIT tidak bisa mendeteksi class dari "bg-{{ $status_color }}-50"
    safelist: [
        // Pola: {color}-{shade} untuk bg, text, border (light & dark mode)
        ...['amber', 'blue', 'emerald', 'red', 'slate'].flatMap(color => [
            `bg-${color}-50`, `bg-${color}-100`, `bg-${color}-200`,
            `bg-${color}-500`, `bg-${color}-500/5`, `bg-${color}-500/10`, `bg-${color}-500/20`,
            `text-${color}-300`, `text-${color}-400`, `text-${color}-500`,
            `text-${color}-600`, `text-${color}-700`, `text-${color}-800`,
            `border-${color}-200`, `border-${color}-500/10`, `border-${color}-500/20`,
            `border-${color}-800`, `border-${color}-800/30`,
        ]),
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    50: '#eef2f7',
                    100: '#d5deeb',
                    200: '#b0c1d9',
                    300: '#8aa4c7',
                    400: '#6587b4',
                    500: '#3f6a9f',
                    600: '#2d5485',
                    700: '#1e3a5f',
                    800: '#152647',
                    900: '#0f1d36',
                    950: '#081120',
                },
                sakura: {
                    50: '#fef1f7',
                    100: '#fde6f1',
                    200: '#fccee4',
                    300: '#faa7cf',
                    400: '#f571b0',
                    500: '#e91e8c',
                    600: '#d41a7d',
                    700: '#b01464',
                    800: '#8f1252',
                    900: '#781447',
                    950: '#480527',
                },
            },
        },
    },

    plugins: [forms],
};
