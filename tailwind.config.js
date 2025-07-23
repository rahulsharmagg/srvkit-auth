/** @type {import('tailwindcss').Config} */
import plugin from 'tailwindcss/plugin';

const themePlugin = function ({ addVariant }) {
  for (const theme of ['dark', 'light', 'blue', 'default']) {
    addVariant(`theme-${theme}`, `&:where([data-theme="${theme}"] &)`);
  }
}

module.exports = {
  content: [
    "./src/Views/*.{php,twig,html}",
    "./src/Cells/*.{php,twig,html}"
  ],
  theme: {
    extend: {},
  },
  plugins: [
    plugin(themePlugin)
  ],
}

