const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
module.exports = {
    mode: 'jit',
    content: [
        './resources/views/**/*.blade.php',
        './vendor/wireui/wireui/resources/**/*.blade.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/View/**/*.php'
    ],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
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
                primary: colors.blue,
                secondary: colors.slate,
                positive: colors.emerald,
                negative: colors.red,
                warning: colors.amber,
                info: colors.sky
            }
        },
    },

    plugins: [
        require('daisyui'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ],
    // config (optional)
    daisyui: {
        prefix: "ds-",
        themes: [
            {
                'light':{
                    ...require("daisyui/src/colors/themes")["[data-theme=light]"],
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



        ],
    },
};
