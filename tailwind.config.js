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
