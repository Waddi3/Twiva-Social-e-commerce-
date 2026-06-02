const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    mode: 'jit',
    content: [
        './Resources/**/*.blade.php',
        './Resources/**/**/*.blade.php',
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
                200: '#111827',
                300: '#27272A',
            },
            'primary': {
                50: '#FDE4EB',
                600: '#D92058',
                700: '#C21B54',
                800: '#AD1650',
                900: '#8A0E49', // twiva
                1000: '#7A0D41', // button
                transparent: '#8A0E49B2'
            },
            'gray': {
                25: '#FCFCFD',
                50: '#FBFAFC',
                100: '#F2F4F7',
                150: '#F9FAFB',
                200: '#EFEEF0',
                300: '#E1E0E2',
                400: '#BEBDBF',
                500: '#9F9EA0',
                600: '#667085',
                700: '#344054',
                750: '#626163',
                800: '#434244',
                900: '#101828',
                950: '#222123',
            },
            'orange': {
                500: '#FB6514',
            },
            'error': {
                800: '#912018'
            },
            'yellow': {
                50: '#FDFCE6',
                500: '#efa130',
                800: '#ECA01B',
                transparent: '#ECA01B99'
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
                drawer: '0 4px 8px -2px rgba(16, 24, 40, 0.1), 0 2px 4px -2px rgba(16, 24, 40, 0.06)',
                whatsapp: '0 12px 16px -4px rgba(16, 24, 40, 0.1), 0 4px 6px -2px rgba(16, 24, 40, 0.05)',
                'faq-item': '0 4px 8px -2px rgba(16, 24, 40, 0.1), 0 2px 4px -2px rgba(16, 24, 40, 0.06)'
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
        backgroundImage: {
            'faq-image': "url('/front/images/faq.svg')"
        },
        backgroundPosition: {
            'faq-position': '90% 65%',
            'right-center': 'right center'
        }
    },
    plugins: [],
}
