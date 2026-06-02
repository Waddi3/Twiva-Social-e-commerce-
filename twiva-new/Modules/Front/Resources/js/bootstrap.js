import Alpine from 'alpinejs'
import feather from "feather-icons";

window.$ = window.jQuery = require('jquery')

window.axios = require('axios')
window.Alpine = Alpine
window.feather = feather

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

document.addEventListener('alpine:initialized', () => {
    feather.replace()
})
