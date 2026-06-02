const mix = require('laravel-mix');
const tailwindcss = require("tailwindcss");

const dotenvExpand = require('dotenv-expand');
dotenvExpand(require("dotenv").config({path: "../../.env"/*, debug: true*/}));

require("laravel-mix-merge-manifest");
mix.setPublicPath("../../public").mergeManifest();

mix.js("Resources/js/app.js", "shop/js/app.js")
    .js("Resources/js/delivery-app.js", "shop/js/delivery-app.js")
    .sass("Resources/sass/app.scss", "shop/css/app.css")
    .options({
        postCss: [tailwindcss("./tailwind.config.js")]
    })
    .copyDirectory("./Resources/images", "../../public/shop/images")
    // .copyDirectory("./Resources/js/intl-tel-input", "../../public/shop/js/intl-tel-input")
    // .copyDirectory("./Resources/css/intl-tel-input", "../../public/shop/css/intl-tel-input")
    // .version();
mix.browserSync({
    proxy: 'aziz-business.twiva.test',
    host: 'aziz-business.twiva.test',
    notify: false,
    reloadDelay: 0,
    files: [
        "./Resources/**/*.scss",
        "./Resources/**/*.js",
        "./Resources/views/**/*.php",
        "../Buyer/Resources/views/**/*.php",
        "../Order/Resources/views/**/*.php",
        "./Resources/js/**/*.js",
        "./Resources/sass/**/*.scss",
    ]
});
