/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.jsx",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Urbanist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        'urbanist': ['Urbanist', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
