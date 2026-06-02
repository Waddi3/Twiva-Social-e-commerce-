function _owlOne() {
    $('.owl-one').owlCarousel({
        loop: true,
        margin: 10,
        responsiveClass: true,
        autoplay: true,
        autoplayTimeout: 3000,
        responsive: {
            0: {
                items: 1,
                loop: true,
            },
            600: {
                items: 1,
                loop: true,
            },
            1000: {
                items: 1,
                loop: true,
            }
        }
    })
}

function _latest_Product_Carousel() {
    $('.owl-default').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        responsiveClass: true,
        autoplay: false,
        responsive: {
            0: {
                items: 1,
                loop: false,
            },
            600: {
                items: 1,
                loop: false,
            },
            1000: {
                items: 1,
                loop: false,
            }
        }
    })
}

function _owlTwo() {
    $('.owl-two').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        dots: false,
        responsiveClass: true,
        autoplay: false,
        autoplayTimeout: 2000,
        responsive: {
            0: {
                items: 1,
                loop: true,
            },
            600: {
                items: 1,
                loop: true,
            },
            1000: {
                items: 2,
                loop: true,
            }
        }
    })
}

function _owlThree() {
    $('.owl-three').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        dots: true,
        responsiveClass: true,
        autoplay: false,
        autoplayTimeout: 2000,
        responsive: {
            0: {
                items: 1,
                loop: true,
            },
            800: {
                items: 2,
                loop: true,
            },
            1000: {
                items: 2,
                loop: true,
            },
            1300: {
                items: 3,
                loop: true,
            }
        }
    })
}

function _owlFive() {
    $('.owl-five').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        loop: false,
        dots: false,
        responsiveClass: true,
        autoplay: false,
        autoplayTimeout: 2000,
        responsive: {
            0: {
                items: 2,
            },
            800: {
                items: 3,
            },
            1000: {
                items: 3,
            },
            1300: {
                items: 5,
            }
        }
    })
}


function sumoSelect() {
    $(document).ready(function() {
        window.asd = $('.SlectBox').SumoSelect({ csvDispCount: 3, selectAll: true, captionFormatAllSelected: "Yeah, OK, so everything." });
        window.test = $('.testsel').SumoSelect({ okCancelInMulti: true, captionFormatAllSelected: "Yeah, OK, so everything." });

        window.testSelAll = $('.testSelAll').SumoSelect({ okCancelInMulti: true, selectAll: true });

        window.testSelAll2 = $('.testSelAll2').SumoSelect({ selectAll: true });

        window.testSelAlld = $('.SlectBox-grp').SumoSelect({ okCancelInMulti: true, selectAll: true, isClickAwayOk: true });

        window.Search = $('.search-box').SumoSelect({ csvDispCount: 3, search: true, searchText: 'Enter here.' });
        window.sb = $('.SlectBox-grp-src').SumoSelect({ csvDispCount: 3, search: true, searchText: 'Enter here.', selectAll: true });
        window.searchSelAll = $('.search-box-sel-all').SumoSelect({ csvDispCount: 3, selectAll: true, search: true, searchText: 'Enter here.', okCancelInMulti: true });
        window.searchSelAll = $('.search-box-open-up').SumoSelect({ csvDispCount: 3, selectAll: true, search: false, searchText: 'Enter here.', up: true });
        window.Search = $('.search-box-custom-fn').SumoSelect({
            csvDispCount: 3,
            search: true,
            searchText: 'Enter here.',
            searchFn: function(haystack, needle) {
                var re = RegExp('^' + needle.replace(/([^\w\d])/gi, '\\$1'), 'i');
                return !haystack.match(re);
            }
        });

        window.groups_eg_g = $('.groups_eg_g').SumoSelect({ selectAll: true, search: true });


        $('.SlectBox').on('sumo:opened', function(o) {
            console.log("dropdown opened", o)
        });

    });
}


function visibleSearch() {
    document.querySelector('header .bottom_header .search_panel .wrapper_search').classList.toggle('search_mobile');
    document.querySelector('header .bottom_header .search_panel a i').classList.toggle('fa-times');
}



function activePanel() {
    document.querySelector('header .bottom_header .search_panel .wrapper_search .search_box .search_panel').classList.toggle('hide');
}


/* For Product Preview Tabs  */

function toggleTab($this) {
    var _slug = '_view';
    document.querySelectorAll('.product_preview .right_panel .descriptoin_panel .tabs a').forEach(function(el) {
        el.classList.remove('active');
    })
    document.querySelectorAll('.product_preview .right_panel .descriptoin_panel .tab-description >div').forEach(function(el) {
        el.style.display = "none";
    })
    $this.classList.add('active');
    document.getElementById($this.id + _slug).style.display = "block";

}

/* BEGIN Login Signup Panel */
function loginRegisterTabs($this) {
    var _slug = '_view';
    document.querySelectorAll('.modal-dialog .modal-content .modal-body .login_tabs ul li a').forEach(function(el) {
        el.classList.remove('active');
    })
    document.querySelectorAll('.modal-dialog .modal-content .modal-body .wrapper_panels >div').forEach(function(el) {
        el.style.display = "none";
    })
    $this.classList.add('active');
    document.getElementById($this.id + _slug).style.display = "block";
}


/* BEGIN Seller Tabs */
function sellerTabs($this) {
    var _slug = '_view';
    document.querySelectorAll('.seller .seller_overview .seller_tabs a').forEach(function(el) {
        el.classList.remove('active');
    })
    document.querySelectorAll('.seller .seller_overview .seller_description >div').forEach(function(el) {
        el.style.display = "none";
    })
    $this.classList.add('active');
    document.getElementById($this.id + _slug).style.display = "block";
}

/* For Product Preview Chnage Image  */
function changeImage($this) {
    var temp_src = $this.querySelector('img').getAttribute('src');
    document.querySelectorAll('.product_preview .right_panel .wrapper-product_details .product_image .related_images a').forEach(function(el) {
        el.classList.remove('active');
    })
    $this.classList.add('active');
    document.querySelector('.product_preview .right_panel .wrapper-product_details .product_image .image img').setAttribute('src', temp_src);
}



// BEGIN For Otp Input

$('.digit-group').find('input').each(function() {
    $(this).attr('maxlength', 1);
    $(this).on('keyup', function(e) {
        var parent = $($(this).parent());

        if (e.keyCode === 8 || e.keyCode === 37) {
            var prev = parent.find('input#' + $(this).data('previous'));

            if (prev.length) {
                $(prev).select();
            }
        } else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (
                e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
            var next = parent.find('input#' + $(this).data('next'));

            if (next.length) {
                $(next).select();
            } else {
                if (parent.data('autosubmit')) {
                    parent.submit();
                }
            }
        }
    });
});

/* BEGIN Toggle CAtegory For MObile */
function toggleCategoryMobile() {
    document.querySelector('header .mobile_header .bottom-m_header .left_grid .m-all_category').classList.toggle('active');
}

function toggleProfileForMobile() {
    document.querySelector('header .mobile_header .bottom-m_header .right_grid .profile_section').classList.toggle('active');
}


/* BEGIN Of Services TOggle Class */
$('.points').click(function() {
    if ($(this).hasClass('active')) {
        $(this).removeClass('active')
    } else {
        $(this).addClass('active')
    }
})

/* BEGIN Toggle Filter For Filters */
function toggleFilter() {
    document.querySelector('.search .filters .wrapper_filter').classList.toggle('active');
}


$(function() {

    var parent = document.querySelector(".price_range");
    if (!parent) return;

    var
        rangeS = parent.querySelectorAll("input[type=range]"),
        numberS = parent.querySelectorAll("input[type=number]");

    rangeS.forEach(function(el) {
        el.oninput = function() {
            var slide1 = parseFloat(rangeS[0].value),
                slide2 = parseFloat(rangeS[1].value);

            if (slide1 > slide2) {
                [slide1, slide2] = [slide2, slide1];
                // var tmp = slide2;
                // slide2 = slide1;
                // slide1 = tmp;
            }

            numberS[0].value = slide1;
            numberS[1].value = slide2;
        }
    });

    numberS.forEach(function(el) {
        el.oninput = function() {
            var number1 = parseFloat(numberS[0].value),
                number2 = parseFloat(numberS[1].value);

            if (number1 > number2) {
                var tmp = number1;
                numberS[0].value = number2;
                numberS[1].value = tmp;
            }

            rangeS[0].value = number1;
            rangeS[1].value = number2;

        }
    });

});