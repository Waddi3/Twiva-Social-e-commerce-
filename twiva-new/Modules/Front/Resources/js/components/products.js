import feather from "feather-icons";
import axios from "axios";

window.$ = window.jQuery = require('jquery')
let localSite = window.location.origin + '/';

$("#searchInput").keyup(() => {
    var query = $("#searchInput").val();
    console.log("query is " + query);
    searchProducts(query);
})

function searchProducts(query) {
    axios.post(localSite + 'products/search',
        {
            keyWord: query,
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