import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/emails/**/*.blade.php',
        './app/Helpers/*.php'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'inner-top': 'inset 0 10px 10px -10px rgba(0, 0, 0, 0.5)',
                'inner-bottom': 'inset 0 -10px 10px -10px rgba(0, 0, 0, 0.5)',
            },
            colors: {
                'ewe-gruen': '#CDD503', // Deine genaue Farbdefinition
                'ewe-ltgruen': '#e3e692', // Deine genaue Farbdefinition
            },
        },
    },

    safelist: [
        'text-red-500',
        'bg-blue-100',
        'p-4',
        'font-bold',
        'hover:bg-pink-100',
        'hover:bg-green-500',
        'hover:bg-yellow-100',
        'hover:bg-blue-500',
        'hover:bg-orange-500',
        'hover:bg-gray-100',
        'hover:bg-gray-500',
        'bg-ewe-gruen',
        'bg-blue-500',
        'bg-pink-500',
        'bg-orange-500',
        'bg-green-500',
    ],
    plugins: [forms],
};
