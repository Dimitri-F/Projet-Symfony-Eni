/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    './node_modules/tw-elements/dist/js/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui"),require("tw-elements/dist/plugin.cjs")],
  darkMode: "class",
  daisyui: {
    themes: ["light", "dark", "cupcake"],
  },
}
