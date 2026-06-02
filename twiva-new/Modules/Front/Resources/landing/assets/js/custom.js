$(document).ready(function(){
    $('.navbar-toggler').click(function(){
        $('#collapsibleNavbar').toggle();
    });



    $('.main-tab-content .nav-link').click(function(){
        $('.main-tab-content .nav-link').removeClass('active');
        $(this).addClass('active');
    });


    $('.accordion .card').click(function(){
        if(!$(this).find('.collapse').hasClass('show')){
            $('.accordion .card .card-header').removeClass('active');
            $(this).find('.card-header').addClass('active');
        }else{
            $(this).find('.card-header').removeClass('active');
        }
    });


    // Horizontal Menu Scrolling //
    $(function() {
        var print = function(msg) {
          alert(msg);
        };
 
        var setInvisible = function(elem) {
          elem.css('visibility', 'hidden');
        };
        var setVisible = function(elem) {
          elem.css('visibility', 'visible');
        };
 
        var elem = $("#elem");
        var items = elem.children();
 
        // Inserting Buttons
        elem.prepend('<div id="right-button" style="visibility: hidden;"><img src="./left-arr.svg" alt=""></div>');
        elem.append('  <div id="left-button"><img src="./right-arr.svg" alt=""></div>');
 
        // Inserting Inner
        items.wrapAll('<div id="inner" />');
 
        // Inserting Outer
        // debugger;
        elem.find('#inner').wrap('<div id="outer"/>');
 
        var outer = $('#outer');
 
        var updateUI = function() {
          var maxWidth = outer.outerWidth(true);
          var actualWidth = 0;
          $.each($('#inner >'), function(i, item) {
            actualWidth += $(item).outerWidth(true);
          });
 
          if (actualWidth <= maxWidth) {
            setVisible($('#left-button'));
          }
        };
        updateUI();
 
 
 
        $('#right-button').click(function() {
            var leftPos = outer.scrollLeft();
            outer.animate({
                scrollLeft: leftPos - 200
            }, 300, function() {
                // debugger;
                if ($('#outer').scrollLeft() <= 0) {
                setInvisible($('#right-button'));
                }
            });
            
        });
    
        $('#left-button').click(function() {
            setVisible($('#right-button'));
            var leftPos = outer.scrollLeft();
            outer.animate({
                scrollLeft: leftPos + 200
            }, 300);
        });
    
        $(window).resize(function() {
            updateUI();
            });
        });
 
    
    $('#influencer-carousel').on('slide.bs.carousel', function (e) {

  
        var $e = $(e.relatedTarget);
        var idx = $e.index();
        var itemsPerSlide = 4;
        var totalItems = $('.carousel-item').length;
        
        if (idx >= totalItems-(itemsPerSlide-1)) {
            var it = itemsPerSlide - (totalItems - idx);
            for (var i=0; i<it; i++) {
                // append slides to end
                if (e.direction=="left") {
                    $('.carousel-item').eq(i).appendTo('.carousel-inner');
                }
                else {
                    $('.carousel-item').eq(0).appendTo('.carousel-inner');
                }
            }
        }
    });

    $('#influencer-carousel').carousel({ 
        interval: 2000
    });

});

// Image Zoom Modal Start //

    // Open the Modal
    function openModal() {
        document.getElementById("imgModal").style.display = "block";
    }
    
    // Close the Modal
    function closeModal(){
        document.getElementById("imgModal").style.display = "none";
    }
    
    var slideIndex = 1;
    showSlides(slideIndex);
    
    // Next/previous controls
    function plusSlides(n) {
        showSlides((slideIndex += n));
    }
    
    // Thumbnail image controls
    function currentSlide(n) {
        showSlides((slideIndex = n));
    }
    
    function showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("demo");
        if (n > slides.length) {
        slideIndex = 1;
        }
        if (n < 1) {
        slideIndex = slides.length;
        }
        for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "block";
    }

    
// Image Zoom Modal End //



