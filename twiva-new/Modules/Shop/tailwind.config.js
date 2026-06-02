const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './Resources/**/*.blade.php',
        './Resources/**/**/*.blade.php',
        './Resources/**/**/**/*.blade.php',
        './Resources/views/components/checkout/**/*.blade.php',
        './Resources/views/checkout/**/*.blade.php',
        './Resources/views/components/modal/login.blade.php',
        '../../app/View/**/*.php',
        "../Buyer/Resources/views/**/*.php",
        "../Order/Resources/views/**/*.php",
    ],
    safelist: [
        {
            pattern: /./, // the "." means "everything"
        },
    ],

    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            'white': '#ffffff',
            'black': {
                100: '#000000',
                200: '#111827'
            },
            'primary': {
                25: '#FFF1F3',
                50: '#FDE4EB',
                400: '#ED4272',
                600: '#D92058',
                700: '#C21B54',
                800: '#AD1650',
                900: '#8A0E49', // twiva
                1000: '#7A0D41', // button
            },
            'gray': {
                25: '#FCFCFD',
                50: '#FBFAFC',
                100: '#F2F4F7',
                125: '#F6F5F7',
                150: '#F9FAFB',
                200: '#EFEEF0',
                300: '#E1E0E2',
                350: '#D0D5DD',
                400: '#BEBDBF',
                450: '#9F9EA0',
                500: '#667085',
                600: '#767577',
                700: '#344054',
                750: '#626163',
                800: '#434244',
                850: '#222123',
                900: '#101828'
            },
            'orange': {
                500: '#FB6514',
            },
            'error': {
                800: '#912018',
                850: '#9C2A10'
            },
            'success': {
                800: '#176300',
                500: '#12B76A'
            },
            'green': {
                50: '#f0fdf4',
                100: '#dcfce7',
                200: '#bbf7d0',
                300: '#86efac',
                400: '#4ade80',
                500: '#22c55e',
                600: '#16a34a',
                700: '#15803d',
                800: '#166534',
                900: '#14532d',
            },
            'blue': {
                600: '#1570EF'
            },
            "warning": {
                50: '#FDFCE6',
                500: '#efa130',
                800: '#ECA01B'
            },
        },
        extend: {
            fontFamily: {
                sans: ['SourceSansPro', ...defaultTheme.fontFamily.sans]
            },
            boxShadow: {
                navbar: '0 1px 3px rgba(16, 24, 40, 0.1), 0 1px 2px rgba(16, 24, 40, 0.06)',
                dropdown: '0 4px 8px -2px rgba(16, 24, 40, 0.1), 0 2px 4px -2px rgba(16, 24, 40, 0.06)',
                drawer: '0 4px 8px -2px rgba(16, 24, 40, 0.1), 0 2px 4px -2px rgba(16, 24, 40, 0.06)'
            },
            dropShadow: {
                card: '0 1px 4px rgba(0, 0, 0, 0.25)',
                btn: '0 4px 4px rgba(0, 0, 0, 0.25)'
            },
            translate: {
                'full-inverse': '-100%'
            },
            inset: {
                'unset': 'unset',
            }
        },
        screens: {
            xs: '475px',
            ...defaultTheme.screens,
            'xs-down': {max: '639px'},
            'sm-down': {max: '767px'},
            'md-down': {max: '1023px'},
            'lg-down': {max: '1279px'},
            'xl-down': {max: '1535px'},
            mobile: {max: '767px'},
            tablet: {min: '768px', max: '1023px'},
            desktop: {min: '1024px', max: '1279px'},
            'xl-display': {min: '1280px', max: '1535px'}
        },
    },
    plugins: [],
}
