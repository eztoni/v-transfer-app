const defaultTheme = require('tailwindcss/defaultTheme');

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
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('daisyui'), require('@tailwindcss/typography')],
    // config (optional)
    daisyui: {

        themes: [
            {
                'valamar':{
                    'primary' : '#00a8cd',
                    'primary-focus' : '#00718a',
                    'primary-content' : '#ffffff',
                    'secondary' : '#0042CB',
                    'secondary-focus' : '#00339D',
                    'secondary-content' : '#ffffff',
                    'accent' : '#00CB89',
                    'accent-focus' : '#00A26D',
                    'accent-content' : '#ffffff',
                    'neutral' : '#3d4451',
                    'neutral-focus' : '#2a2e37',
                    'neutral-content' : '#ffffff',
                    'base-100' : '#ffffff',
                    'base-200' : '#f6f6f6',
                    'base-300' : '#d1d5db',
                    'base-content' : '#333',
                    'info' : '#00a8cd',
                    'success' : '#00CB89',
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
