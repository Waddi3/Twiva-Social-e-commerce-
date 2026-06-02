window.$ = window.jQuery = require('jquery')


import clipboard from './components/clipboard';
import dropdown from './components/dropdown';
import toggling from './components/toggling';
import drawer from './components/drawer';
import counter from './components/counter';
import expander from './components/expander';
import wizard from './components/wizard';
import radioExpander from './components/radio-expander';
import lipalater from './components/lipalater';
import mpesa from './components/mpesa';
import confirm from './components/confirm';
import {initializePhone} from './intl-tel-input/main';
window.initializePhone = initializePhone
require('./bootstrap')
require('./variables')
require('./components/products')
require('./intl-tel-input/main')
document.addEventListener('alpine:init', () => {
    Alpine.data('clipboard', () => (clipboard))
    Alpine.data('dropdown', () => (dropdown))
    Alpine.data('toggling', (isInCard, productId) => (toggling(isInCard, productId)))
    Alpine.data('counter', (count, cardID, submit) => (counter(count, cardID, submit)))
    Alpine.data('drawer', () => (drawer))
    Alpine.data('expander', () => (expander))
    Alpine.data('wizard', () => (wizard))
    Alpine.data('radioExpander', () => (radioExpander))
    Alpine.data('lipalater', () => (lipalater))
    Alpine.data('mpesa', () => (mpesa))
    Alpine.data('confirm', () => (confirm))

})
Alpine.start()

const axios = require('axios').default;

