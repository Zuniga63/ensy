const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  //mode: 'jit',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', 'sans-serif', ...defaultTheme.fontFamily.sans],
      },
      transitionProperty: {
        'height': 'height',
        'spacing': 'margin, padding',
      }
    },
  },

  /*variants: {
      extend: {
          opacity: ['disabled'],
      },
  },*/

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography'),require('@tailwindcss/line-clamp'),],
};
