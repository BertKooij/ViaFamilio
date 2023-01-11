const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            maxWidth: {
                '100': '100%',
            },
            colors: {
                primary: {
                    50: '#E9EEFF',
                    100: '#C7D4FF',
                    200: '#7692F2',
                    300: '#5375ED',
                    400: '#375BD9',
                    500: '#1B42CA',
                    600: '#0A2EAD',
                    700: '#072693',
                    800: '#061E73',
                    900: '#051242',
                }
            },
        }
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
