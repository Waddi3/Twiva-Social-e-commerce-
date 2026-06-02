import feather from "feather-icons";
import axios from "axios";

window.$ = window.jQuery = require('jquery')
let localSite = window.location.origin + '/';
let subCategoryId = $('.sub-category.selected').attr('data-id');

function selectFilterItem($this) {

    let $ul = $this.closest('ul');
    $ul.siblings('.filter-data').val($this.attr('data-value'));

    $ul.find('.filter-item').find('> div').removeClass('border-primary-600 bg-primary-50').addClass('border-gray-300 bg-gray-50');
    $this.find('> div').addClass('border-primary-600 bg-primary-50');

    $ul.find('svg').addClass('hidden');
    $this.find('svg').removeClass('hidden');
}

$('#products-load-more').click(function () {
    searchProducts();

});

// before
// commented in favor of new API for fetching products
// Sub category click
// $('.sub-category').click(function () {
//     $('.sub-category').removeClass('text-primary-600 bg-primary-50 selected').addClass('text-gray-600');
//     $(this).removeClass('text-gray-600').addClass('text-primary-600 bg-primary-50 selected');
//     $('#category-title').text($(this).text());
//     searchProducts();
// });

// Filter item
$('.filter-item').click(function () {
    selectFilterItem($(this));
    searchProducts();
});

$('#reset').click(function () {
    history.pushState(null, "", location.href.split("?")[0]);
    $('#sub-categories-container').attr('data-category', null)
    $('.sub-category.selected').attr('data-id', null)
    $("#searchInput").val('')
    // Deals
    selectFilterItem($('#deals-container .filter-item:first'));
    // Rating
    selectFilterItem($('#rating-container .filter-item:first'));
    // Sub Categories
    $('.sub-category').removeClass('text-primary-600 bg-primary-50 selected').addClass('text-gray-600');
    let $shopAll = $('.sub-category[data-id=0]');
    $shopAll.removeClass('text-gray-600').addClass('text-primary-600 bg-primary-50 selected');

    const subCategoriesContainer = document.getElementById('sub-categories-container');
    subCategoriesContainer.scrollTo({top: 0, behavior: 'smooth'});
    let subCategoryId = $('.sub-category.selected').attr('data-id');
    if (subCategoryId) {
        $('#category-title').text($shopAll.text());
    }
    searchProducts()
});
$("#searchInput").keyup(() => {
    searchProducts();
})

function searchProducts() {
    axios.post(localSite + 'products/search',
        {
            keyWord: $("#searchInput").val(),
            query: $('#query').val(),
            deals: $('#deals').val(),
            rating: $('#rating').val(),
            category_id: $('#sub-categories-container').attr('data-category'),
            subcategory_id: $('.sub-category.selected').attr('data-id'),
            min_price: $('#min-range-input').val(),
            max_price: $('#max-range-input').val(),
        }).then((data) => {
        $("#productSection").fadeOut()
        setTimeout(() => {
            $("#productSection").html(data.data.products).fadeIn()
            feather.replace()
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#searchInput").offset().top
            }, 500);
        }, 500)
    }).catch((response) => {
    })
}

export function indexProducts() {
    axios.post(localSite + 'products/search', {keyWord: ''},
    ).then((data) => {
        setTimeout(() => {
            $("#productSection").html(data.data.products)
            feather.replace()
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#searchInput").offset().top
            }, 500);
        }, 500)
    }).catch((response) => {
    })
}
