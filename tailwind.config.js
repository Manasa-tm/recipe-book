import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',

        // Blade templates
        './resources/views/**/*.blade.php',

        // Laravel resources
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './resources/**/*.blade.php',

        // Optional if you use React
        './resources/**/*.jsx',
        './resources/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
    ],

    // Temporary safelist for debugging
    safelist: [
        'rounded-2xl',
        'rounded-xl',
        'bg-white',
        'shadow-lg',
        'p-6',
        'text-gray-900',
    ],
};