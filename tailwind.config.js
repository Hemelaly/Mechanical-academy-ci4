/** @type {import('tailwindcss').Config} */
const flowbite = require('flowbite/plugin');

module.exports = {
  content: [
    './src/**/*.{html,js,jsx,ts,tsx,vue,svelte}',
    './app/Views/**/*.{php,html}',
    './public/**/*.{html,js}',
    './vendor/flowbite/**/*.js'
  ],
  theme: {
    extend: {}
  },
  plugins: [flowbite]
};
