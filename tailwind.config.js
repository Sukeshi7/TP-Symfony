/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig', // Chemins vers tes fichiers Twig
    './src/**/*.php',             // Chemins vers ton code PHP si nécessaire
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

