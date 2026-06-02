var mix = require("laravel-mix");
var tailwindcss = require("tailwindcss");

const dotenvExpand = require('dotenv-expand');
dotenvExpand(require("dotenv").config({path: "../../.env"/*, debug: true*/}));

require("laravel-mix-merge-manifest");
mix.setPublicPath("../../public").mergeManifest();

mix.js("Resources/js/app.js", "front/js/app.js")
    .sass("Resources/sass/app.scss", "front/css/app.css")
    .js("Resources/js/shop.js", "front/js/shop.js")
    .js("Resources/js/intl-tel-input/utils.js", "front/js/intl-tel-input/utils.js")
    .options({
        postCss: [tailwindcss("./tailwind.config.js")]
    })
if (mix.inProduction())
{
    mix.version();
}
mix.then(() => {
    const fs = require('fs-extra');
    // Copy files after versioning
    fs.copySync('./Resources/images', '../../public/front/images');
    fs.copySync('./Resources/landing/assets', '../../public/front/landing');
});