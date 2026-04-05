/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                gold: {
                    50: '#fef9e7',
                    100: '#fdefc3',
                    200: '#fbdf9b',
                    300: '#f9ce73',
                    400: '#f7be4b',
                    500: '#f5ae23',
                    600: '#e69a00',
                    700: '#c47b00',
                    800: '#a25c00',
                    900: '#803d00',
                },
                cosmic: {
                    50: '#f0f0ff',
                    100: '#e0e0ff',
                    200: '#c0c0ff',
                    300: '#a0a0ff',
                    400: '#8080ff',
                    500: '#6060ff',
                    600: '#4040cc',
                    700: '#202099',
                    800: '#101066',
                    900: '#080833',
                },
            },
            fontFamily: {
                serif: ['Times New Roman', 'serif'],
            },
            animation: {
                'golden-pulse': 'goldenPulse 2s infinite',
                'spiral-float': 'spiralFloat 3s ease-in-out infinite',
                'shimmer': 'shimmer 2s infinite',
            },
            backgroundImage: {
                'golden-gradient': 'linear-gradient(135deg, #FFD700, #D4AF37, #B8860B)',
                'cosmic-gold': 'radial-gradient(circle at center, rgba(212,175,55,0.1), transparent)',
            },
        },
    },
    plugins: [],
}
