window.$ = window.jQuery = require('jquery')
require('jquery-ui');
require('jquery-ui/ui/widgets/slider');
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
require('./variables')
require('./components/products')
require('./intl-tel-input/main')
document.addEventListener('alpine:init', () => {
    Alpine.data('clipboard', () => (clipboard))
    Alpine.data('dropdown', () => (dropdown))
    Alpine.data('toggling', (isInCard, productId, userId) => (toggling(isInCard, productId, userId)))
    Alpine.data('counter', (count, cardID, submit, userType) => (counter(count, cardID, submit, userType)))
    Alpine.data('drawer', () => (drawer))
    Alpine.data('expander', () => (expander))
    Alpine.data('wizard', () => (wizard))
    Alpine.data('radioExpander', () => (radioExpander))
    Alpine.data('lipalater', () => (lipalater))
    Alpine.data('mpesa', () => (mpesa))
    Alpine.data('confirm', () => (confirm))

})
Alpine.start()

//const axios = require('axios').default;
require('./api.js')

// Init price range
let localSite = window.location.origin + '/';
let minPrice = Math.trunc($('#min-range-input').val());
let maxPrice = Math.trunc($('#max-range-input').val());
if( minPrice == maxPrice ) {
    maxPrice += 50;
    if( (minPrice - 50) > 0 ) {
        maxPrice -= 50;
    }
}
const $minRange = $('#min-range');
const $maxRange = $('#max-range');
$minRange.text(minPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');
$maxRange.text(maxPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');

// Query
const query = $('#query').val();

window.isSliderChangingByTheCode = false;

let $price_range = $('#price-range');
$price_range.slider({
    range: true,
    min: minPrice,
    max: maxPrice,
    values: [minPrice, maxPrice],
    slide: function (event, ui) {
        let commaSeparatedPrice = ui.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH';
        if (ui.handleIndex == 0) {
            $minRange.text(commaSeparatedPrice);
        } else {
            $maxRange.text(commaSeparatedPrice);
        }
    },
    change: function (event, ui) {
        if (ui.handleIndex == 0) {
            $('#min-range-input').val(ui.value);
        } else {
            $('#max-range-input').val(ui.value);
        }
        
        if(!window.isSliderChangingByTheCode) {
            getProducts(true);
        }
    }
});

// Title
if (query) {
    $('#sub-title').html(`Search for <i>${query}</i>`);
}

// Open Subcategory for selected Category after load
$(document).ready(function () {
    let $subCategory = $('.sub-category.selected');
    if ($subCategory.length && $subCategory.attr('data-id') != 0) {
        $('.category').addClass('hidden').removeClass('selected text-primary-600 bg-primary-50');
        $('#category-title').text($subCategory.text());
        const $category = $('.sub-category.selected').closest('.sub-category-list').siblings('.category');
        $category.addClass('selected text-primary-600 bg-primary-50');
        $('#categories-title').text($category.text());
        $('.sub-category.selected').closest('.sub-category-list').removeClass('hidden');
        $('#back-to-category span').text($category.text());
        $('#back-to-category a').removeClass('hidden');
        $('#up').removeClass('hidden');
    }
    // Or open category
    else {
        let $selectedCategory = $('.category.selected');
        if ($selectedCategory.length && $selectedCategory.attr('data-id') != 0) {
            $('.category').addClass('hidden');
            $selectedCategory.siblings('.sub-category-list').removeClass('hidden');
            $('#back-to-category').removeClass('hidden');
            $('#categories-title, #category-title').text($selectedCategory.text());
            $('#up').removeClass('hidden');
        }
    }
});


// Products
let productsPage = 1;
getProducts(true);

function getProducts(replace = false) {

    if (replace) {
        productsPage = 1;
        $('#shop-products .product-item').remove();
    }

    $('#products-load-more').addClass('hidden');
    $('#loading').removeClass('hidden');

    let deals = $('#deals').val();
    let rating = $('#rating').val();
    const $selectedCategory = $('.category.selected');
    let category_id = $selectedCategory.length ? $selectedCategory.attr('data-id') : 0;
    let subcategory_id = $('.sub-category.selected').attr('data-id');
    let min_price = $('#min-range-input').val();
    let max_price = $('#max-range-input').val();

    let queryString = `page=${productsPage}&per_page=3`;
    queryString += deals == 'all' ? '' : `&deals=${deals}`;
    queryString += rating == 'all' ? '' : `&rating=${rating}`;
    queryString += category_id == '0' || subcategory_id ? '' : `&category_id=${category_id}`;
    queryString += subcategory_id == '0' || subcategory_id == null ? '' : `&subcategory_id=${subcategory_id}`;
    queryString += query == '' || query == null ? '' : `&query=${query}`;
    queryString += `&min_price=${min_price}`;
    queryString += `&max_price=${max_price}`;

    $.ajax({
        type: 'GET',
        url: localSite + `e-commerce/products?${queryString}`,
        success: function (data) {
            $('#products-count').html(data.counts);
            data.products.forEach((el) => {
                $('#shop-products').prepend(el.render);
            });
            feather.replace()
            productsPage++;
            if (data.has_next_page) {
                $('#products-load-more').removeClass('hidden');
            }
            $('#loading').addClass('hidden');
        },
        error: function (data) {
            $('#loading').addClass('hidden');
        }
    });
}

$('#products-load-more').click(function () {
    getProducts();
});

// Filter item
$('.filter-item').click(function () {
    selectFilterItem($(this));
    productsPage = 1;
    getProducts(true);
    feather.replace()
});

$('#reset').click(function () {

    const selectedSubCategoryId = parseInt($('#selected-sub-category').val());
    const $selectedSubCategory = $(`.sub-category[data-id=${selectedSubCategoryId}]`);

    const selectedCategoryId = $('#selected-category').val();
    const $selectedCategory = $(`.category[data-id=${selectedCategoryId}]`);

    // Sub Category selected ?
    if( selectedSubCategoryId ) {
        $('.sub-category').removeClass('selected text-primary-600 bg-primary-50').addClass('text-gray-600');
        $selectedSubCategory.addClass('selected text-primary-600 bg-primary-50').removeClass('text-gray-600');
        $('#category-title').text($selectedSubCategory.text());
    }
    else {
        // Hide up button
        $('#up').addClass('hidden');
        // Hide back button
        $('#back-to-category > a').addClass('hidden');
        // Category selected ?
        // Shop All
        if( parseInt(selectedCategoryId) == 0 ) {
            $('#back-to-category a').addClass('hidden');
            $('.sub-category.selected').removeClass('selected text-primary-600 bg-primary-50').addClass('text-gray-600');
            $('.sub-category-list').addClass('hidden');
            $('#categories-title').text('Categories');
            $('#category-title').text('Shop');
            $('.category').removeClass('selected text-primary-600 bg-primary-50 hidden');
            $selectedCategory.addClass('selected text-primary-600 bg-primary-50').removeClass('text-gray-600');
        }
        else {
            const title = $('.category.selected').text();
            $('#categories-title, #category-title').text(title);
            $('.sub-category.selected').removeClass('selected text-primary-600 bg-primary-50').addClass('text-gray-600');
        }

        // Deals
        selectFilterItem($('#deals-container .filter-item:first'));
        // Rating
        selectFilterItem($('#rating-container .filter-item:first'));

        // Price Range
        let minPrice = parseInt($selectedCategory.attr('data-min') ? $selectedCategory.attr('data-min') : 0);
        let maxPrice = parseInt($selectedCategory.attr('data-max') ? $selectedCategory.attr('data-max') : 0);
        if( minPrice == maxPrice ) {
            maxPrice += 50;
            if( (minPrice - 50) > 0 ) {
                maxPrice -= 50;
            }
        }
        const $minRange = $('#min-range');
        const $maxRange = $('#max-range');
        $minRange.text(minPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');
        $maxRange.text(maxPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');

        $('#min-range-input').val(minPrice);
        $('#price-range').slider("option", "min", minPrice);
        $('#max-range-input').val(maxPrice);
        $('#price-range').slider("option", "max", maxPrice);

        window.isSliderChangingByTheCode = true;
        $( "#price-range" ).slider( "values", [ minPrice, maxPrice ] );
        window.isSliderChangingByTheCode = false;
    }

    productsPage = 1;
    getProducts(true);
});

// Select category
$('.category').click(function () {
    let $subCategories = $(this).siblings('.sub-category-list');
    $('#up').removeClass('hidden');
    if ($subCategories.length) {
        $('.category').removeClass('text-primary-600 bg-primary-50 selected').addClass('text-gray-600 hidden');
        $subCategories.removeClass('hidden');
        $(this).addClass('text-primary-600 bg-primary-50 selected');
        $('#back-to-category').removeClass('hidden');
        $('#categories-title, #category-title').text($(this).text());

        // Price Range
        let minPrice = parseInt($(this).attr('data-min'));
        let maxPrice = parseInt($(this).attr('data-max'));

        if( minPrice == maxPrice ) {
            maxPrice += 50;
            if( (minPrice - 50) > 0 ) {
                maxPrice -= 50;
            }
        }

        $minRange.text(minPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');
        $maxRange.text(maxPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' KSH');

        $('#min-range-input').val(minPrice);
        $('#price-range').slider("option", "min", minPrice);
        $('#max-range-input').val(maxPrice);
        $('#price-range').slider("option", "max", maxPrice);

        $( "#price-range" ).slider( "values", [ minPrice, maxPrice ] );

    } else {
        $('.category').removeClass('selected');
        $(this).addClass('selected');
    }
    getProducts(true);
});

// Sub category click
$('.sub-category').click(function () {
    if($(this).hasClass('selected')) {
        return;
    }

    $('.sub-category').removeClass('text-primary-600 bg-primary-50 selected').addClass('text-gray-600');
    $(this).removeClass('text-gray-600').addClass('text-primary-600 bg-primary-50 selected');
    $('#category-title').text($(this).text());
    $('#back-to-category span').text($('.category.selected').text());
    $('#back-to-category a').removeClass('hidden');
    $('#up').removeClass('hidden');
    getProducts(true);
});

// Back to Category
$('#back-to-category').click(function () {

    $('#back-to-category a').addClass('hidden');
    $('.sub-category.selected').removeClass('selected text-primary-600 bg-primary-50').addClass('text-gray-600');
    const title = $('.category.selected').text();
    $('#category-title').text(title);
    $('#sub-category-select').val(0);
    $('#sub-category-select').trigger('change.select2')
    $('#select2-sub-category-select-container').text('Shop All');

    getProducts(true);
});

$('#up').click(function() {
    const $selectedCategory = $('.category.selected');
    if( $('.sub-category.selected').length ) {
        $('#back-to-category').trigger('click');
    }
    else if( $selectedCategory.length ) {
        $('.sub-category-list').addClass('hidden');
        $('.category').removeClass('hidden').addClass('text-gray-600');
        $selectedCategory.removeClass('selected text-primary-600 bg-primary-50').addClass('text-gray-600');
        const $shouldSelect = $('.category[data-id=0]');
        $shouldSelect.addClass('selected text-primary-600 bg-primary-50').removeClass('text-gray-600');

        $('#category-title').text($shouldSelect.text());
        $('#sub-category-select').val(0);
        $('#sub-category-select').trigger('change.select2')
        $('#select2-sub-category-select-container').text('Shop');
        $('#categories-title').text('Categories');
        $(this).addClass('hidden');
        getProducts(true);
    }
});

function selectFilterItem($this) {

    let $ul = $this.closest('ul');
    $ul.siblings('.filter-data').val($this.attr('data-value'));

    $ul.find('.filter-item').find('> div').removeClass('border-primary-600 bg-primary-50').addClass('border-gray-300 bg-gray-50');
    $this.find('> div').addClass('border-primary-600 bg-primary-50');

    $ul.find('svg').addClass('hidden');
    $this.find('svg').removeClass('hidden');
    feather.replace()
}

feather.replace()
