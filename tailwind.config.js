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
    safelist: [
        'text-red-500', 'bg-blue-100', 'p-4', 'font-bold' // Übernahme der Klassen für E-Mails
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
    plugins: [forms],
};
