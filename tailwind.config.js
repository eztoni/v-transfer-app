const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
module.exports = {
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Open Sans', ...defaultTheme.fontFamily.sans],

            },
            colors: {
                green: colors.emerald,
                yellow: colors.amber,
                purple: colors.violet,
            }
        },
    },

    plugins: [require('@tailwindcss/forms'), require('daisyui'), require('@tailwindcss/typography')],
    // config (optional)
    daisyui: {

        themes: [
            {
                'valamar':{
                    'primary' : '#136bac',
                    'primary-focus' : '#214392',
                    'primary-content' : '#ffffff',
                    'secondary' : '#118acb',
                    'secondary-focus' : '#214392',
                    'secondary-content' : '#ffffff',
                    'accent' : '#27bdbe',
                    'accent-focus' : '#0db14b',
                    'accent-content' : '#ffffff',
                    'neutral' : '#707070',
                    'neutral-focus' : '#2a2e37',
                    'neutral-content' : '#ffffff',
                    'base-100' : '#ffffff',
                    'base-200' : '#f6f6f6',
                    'base-300' : '#d1d5db',
                    'base-content' : '#333',
                    'info' : '#118acb',
                    'success' : '#0db14b',
                    'warning' : '#eca518',
                    'error' : '#e33244',

                }
                },
            'light',

            'emerald', // first one will be the default theme
            'dark',
            'forest',
            'synthwave',
            'cupcake', // first one will be the default theme
            'bumblebee', // first one will be the default theme
            'corporate',
            'retro',
            'cyberpunk',
            'valentine', // first one will be the default theme
            'halloween', // first one will be the default theme
            'garden',
            'aqua',
            'lofi',
            'pastel', // first one will be the default theme
            'fantasy', // first one will be the default theme
            'wireframe',
            'black',
            'luxury',
            'dracula',

        ],
    },
};
