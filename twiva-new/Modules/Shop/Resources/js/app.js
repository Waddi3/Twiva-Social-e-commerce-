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
require('./api.js')

let localSite = window.location.origin + '/';

import {Wheel} from './spin-wheel-esm';
// 1. Configure the wheel's properties:
const props = {
    itemBackgroundColors: ['#009CBB', '#FDDCAB', '#A11043',],
    itemLabelFontSizeMax: 40,
    itemLabelColors: ['#fff', '#000', '#fff'],
    borderWidth: 0,
    pointerAngle: 1,
    isInteractive: false,
    // overlayImage: 'images/Ellipse-4.png',
    items: [
        {label: 'Try Again', weight: 1},
        // {label: 'KSH 100', weight: 1},
        {label: 'KSH 200', weight: 1},
        // {label: 'KSH 300', weight: 1},
        {label: 'KSH 400', weight: 1},
        // {label: 'KSH 500', weight: 1},
        {label: 'KSH 600', weight: 1},
        // {label: 'KSH 700', weight: 1},
        {label: 'KSH 800', weight: 1},
        // {label: 'KSH 900', weight: 1},
        {label: 'KSH 1000', weight: 1},
        // {label: 'KSH 1100', weight: 1},
        {label: 'KSH 1200', weight: 1},
        // {label: 'KSH 1300', weight: 1},
        {label: 'KSH 1400', weight: 1},
        // {label: 'KSH 1500', weight: 1},
        {label: 'KSH 1600', weight: 1},
        // {label: 'KSH 1700', weight: 1},
        {label: 'KSH 1800', weight: 1},
        // {label: 'KSH 1900', weight: 1},
        {label: 'KSH 2000', weight: 1},
    ]
}

// 2. Decide where you want it to go:
const container = document.querySelector('.wheel');

// 3. Create the wheel in the container and initialise it with the props:
if(container) {
    window.wheel = new Wheel(container, props);
}

$('.wheel-button').click(function () {
    hideCouponInfo();
    $.ajax({
        type: 'GET',
        url: '/coupon/spin-wheel',
        success: function (data) {
            if (data.tried_today <= data.spin_wheel_times_every_day) {
                wheel.pointerAngle = 165;
                const duration = 4000;
                const revolutions = 10;
                let winningItemIndex = data.winning_item_index;
                // const easing = easing.cubicOut;

                wheel.spinToItem(winningItemIndex, duration, true, revolutions, 1);

                setTimeout(function() {
                    if(winningItemIndex === 0){
                        showCouponInfo("Better luck next time! Keep spinning!");
                    } else {
                        const winningItem = props.items[winningItemIndex];

                        $('#coupon-value').html(winningItem.label);
                        $('#coupon-code').html(data.coupon_code);
                        $('.wheel-button').hide();
                        $('#coupon-container').show();
                        // alert(`Congratulations! You won a ${winningItem.label} discount. Your coupon code is ${data.coupon_code}.`);
                        navigator.clipboard.writeText(data.coupon_code);
                    }
                }, duration); // Delay of 2 seconds before showing the result
            } else {
                showCouponInfo('You have already used your five spins for today. Please try again tomorrow.');

            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 401) {
                showCouponInfo('Please log in to continue.');
                $('#coupon-info-text').show();
            } else {
                showCouponInfo('An unexpected error occurred. Please try again later.');
                $('#coupon-info-text').show();
                // Swal.fire({
                //     title: 'Error',
                //     text: 'An unexpected error occurred. Please try again later.',
                //     icon: 'error',
                //     confirmButtonText: 'OK'
                // });
            }
        }
    });
});

$("#copy-coupon-button").click(function() {
    const clipboard = navigator.clipboard;
    const code = $('#coupon-code').text();

    if (clipboard == undefined) {
        console.log('Cannot copy automatically');
        $('#coupon-code-manually').show();
    } else {
        clipboard.writeText(code);
        $(this).html('Coupon copied!');
    }
})

function showCouponInfo(info) {
    if(info) {
        $('#coupon-info-text').html(info);
    }

    $('#coupon-info-text').show();
}

function hideCouponInfo() {
    $('#coupon-info-text').hide();
}

/*wheel.spin(2);*/
/*const winningItemIndex = await fetchWinningItemIndex();
const duration = 4000;
const easing = easing.cubicOut;
wheel.spinToItem(winningItemIndex, duration, true, 2, 1, easing)*/

$(document).ready(() => {
    $("#products-btn").on('click',function (){
        $("#products_type").val("products")
        $("#products-btn").addClass("active-btn")
        $("#products-btn").removeClass("dis-active-btn")
        $("#services-btn").addClass("dis-active-btn")
        $("#services-btn").removeClass("active-btn")
        $('#products-might-like').html("");

        getProductsYouMightLike()
    })
    $("#services-btn").on('click',function (){
        $("#products_type").val("services")
        $("#services-btn").addClass("active-btn")
        $("#services-btn").removeClass("dis-active-btn")
        $("#products-btn").removeClass("active-btn")
        $("#products-btn").addClass("dis-active-btn")
        $('#products-might-like').html("");

        getProductsYouMightLike()
    })
    $("#subscriber-btn").on('click', () => {
        if ($("#subscriber-email").val()) {
            axios.post('/api/subscribe',
                {email: $("#subscriber-email").val()}
            ).then((data) => {
                $("#subscriber-email").val("")
                toastr.success('You have subscribed!')

            })
                .catch((response) => {
                    $("#subscriber-email").val("")
                    toastr.error('This email already subscribed')

                });
        }


    })

    // Hot deals
    let hotDealsPage = 1;
    getHotDeals();

    $('#hot-deals-view-more').click(function () {
        hotDealsPage++;
        getHotDeals(hotDealsPage);
    });

    function getHotDeals(page = 1) {
        console.log("getHotDeals")
        $.ajax({
            type: 'GET',
            url: localSite + `e-commerce/products/hot-deals?page=${hotDealsPage}`,
            success: function (data) {
                if (data.products && !data.products.length) {
                    $('#hot-deals-view-more').hide()
                }

                if(data.products.length > 0) {
                    data.products.forEach((el) => {
                        $('#hot-deals').append(el.render);
                    });
                }
                else {
                    $('#hot-deals-container').hide();
                }

                feather.replace();

            }
        });

    }


    // Products you might like
    let productsYouMightLike = 1;
    getProductsYouMightLike();

    $('#products-might-like-view-more').click(function () {
        productsYouMightLike++;
        getProductsYouMightLike(productsYouMightLike);
    });

    function getProductsYouMightLike() {
        $.ajax({
            type: 'GET',
            data:{
              product_type: $("#products_type").val(),
              page: productsYouMightLike,
              per_page:3
            },
            url: localSite + `e-commerce/products`,
            success: function (data) {
                if ((data.products && !data.products.length) || !data.has_next_page) {
                    $('#products-might-like-view-more').hide()
                }
                data.products.forEach((el) => {
                    $('#products-might-like').append(el.render);
                });
                feather.replace();
            }
        });
    }
});
