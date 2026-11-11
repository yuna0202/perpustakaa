import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Segoe UI', ...defaultTheme.fontFamily.sans],
                display: ['Georgia', 'Times New Roman', 'serif'],
            },
            colors: {
                forest: {50: '#f0f8f4', 100: '#e0eee7', 200: '#c1dece', 600: '#258055', 700: '#166344', 800: '#194f3a', 900: '#183f31', 950: '#11281f'},
                midnight: '#0b111b',
                ice: '#a8ddfb',
                cream: '#fafafa',
                ink: '#18181b',
            },
        },
    },
    plugins: [],
};
