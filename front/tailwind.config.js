/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        parkinsans: ['Parkinsans', 'sans-serif'],
      },
    },
  },
  daisyui: {
    themes: [
      {
        light: {
          primary: '#8CBA51',
          secondary: '#424242',
          accent: '#FBBF24',
          neutral: '#424242',
          'base-100': '#FFFFFF',
          'base-200': '#E8E8E8',
          info: '#DEFF8B',
          success: '#779b33',
          warning: '#FBBF24',
          error: '#EF4444',
    
        },
      },
      {
        dark: {
          primary: '#8CBA51',
          secondary: '#F5F5F5',
          accent: '#FBBF24',
          neutral: '#EDEDED',
          'base-100': '#212121',
          'base-200': '#141414',
          info: '#DEFF8B',
          success: '#779b33',
          warning: '#FBBF24',
          error: '#EF4444',
        },
      },
    ],
  },
  plugins: [
    require('daisyui'),
    function ({ addUtilities }) {
      const shadows = {
        '.text-shadow-sm': {
          textShadow: '1px 1px 2px rgba(156,153,156,1)',
        },
        '.text-shadow': {
          textShadow: '2px 2px 4px rgba(156,153,156,1)',
        },
        '.text-shadow-lg': {
          textShadow: '3px 3px 5px rgba(156,153,156,1)',
        },
      };
      addUtilities(shadows, ['responsive', 'hover']);
    },
  ],
};
