import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // "Flora" storefront typography — warm editorial sans-serif.
                karla: ['Karla', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Design tokens lifted from the "Flora" Shopify theme palette.
                ink: '#2D2510',
                cream: {
                    DEFAULT: '#E5DDC8',
                    50: '#FBF9F4',
                    100: '#F5F1E7',
                    200: '#E5DDC8',
                    300: '#D9CDAE',
                },
                mist: '#F5F5F5',
                sage: {
                    DEFAULT: '#A5C8A3',
                    50: '#F1F6F0',
                    100: '#DDEBDC',
                    200: '#A5C8A3',
                    600: '#6E9A6C',
                    700: '#587B57',
                },
                clay: {
                    DEFAULT: '#C59A85',
                    50: '#FBF4F1',
                    100: '#EFDCD2',
                    200: '#C59A85',
                    600: '#A9765F',
                    700: '#8B5F4C',
                },
            },
        },
    },

    plugins: [forms],
};
