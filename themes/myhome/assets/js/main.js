/**
 * Created by pmallek on 2016-08-18.
 */

window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.preloadAfterLoad = true;

var $ = jQuery.noConflict();

(function ($) {
    if ($('html').attr('dir') === 'rtl') {
        function vc_rtl_fix() {
            var $elements = jQuery('[data-vc-full-width="true"]');
            jQuery.each($elements, function () {
                var $el = jQuery(this);
                $el.css('right', $el.css('left')).css('left', '');
            });
        }

        jQuery(document).on('vc-full-width-row', function () {
            vc_rtl_fix();
        });

        vc_rtl_fix();
    }
    // post card height
    var offset = 0
    var min = 0
    var height = 0
    var cards = $(".mh-post-grid__inner").not(".owl-carousel .mh-post-grid__inner")
    cards.each(function (i, card) {
        var currentOffset = $(card).offset().top
        if (currentOffset !== offset && i > 0) {
            for (min; min < i; min++) {
                $(cards[min]).css('height', height + 'px')
            }
            min = i
            height = 0
            offset = currentOffset
        } else if (i === 0) {
            offset = currentOffset
        }

        var cardHeight = $(card).height()
        if (cardHeight > height) {
            height = cardHeight
        }
        if (i + 1 === cards.length) {
            for (min; min <= i; min++) {
                $(cards[min]).css('height', height + 'px')
            }
        }
    })

    if ($('#comment_post_ID').length && $(".mh-post").length) {
        $('#comment_post_ID').val($(".mh-post").data('id'));
    }

    $( '.mh-navbar li a[href^="#"]' ).on( "click", function (e) {
        e.preventDefault();
    });

    var mhMobileMenu = true;

    $( ".mh-navbar__toggle" ).click(function () {
        if (mhMobileMenu ) {
            $( ".mh-navbar__menu" ).show();
            $( ".mh-navbar__search" ).show();
            mhMobileMenu = false;
        } else {
            $( ".mh-navbar__menu" ).hide();
            $( ".mh-navbar__search" ).hide();
            mhMobileMenu = true;
        }
    });

    var $smoothScrollOffset = 90;
    $('body').scrollspy({
        offset: $smoothScrollOffset + 30
    });

    $("a.smooth").on("click", function (e) {
        if (location.pathname.replace(/^\//, "") === this.pathname.replace(/^\//, "") && location.hostname === this.hostname) {
            e.preventDefault();
            var target = $(this.hash);
            if (this.hash) {
                target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
                if (target.length) {
                    $("html,body").animate({
                        scrollTop: target.offset().top - $smoothScrollOffset
                    }, 1000);
                    return false;
                } else {
                    window.location.href = this.hash;
                }
            } else {
                $("html,body").animate({
                    scrollTop: 0
                });
            }
        }
    });

    $( ".mh-navbar__menu li.page_item_has_children > a" ).on( "click", function ( e ) {
        e.preventDefault();
    });

    $( ".mh-navbar li" ).hover(function () {
        $(this).find( "ul:first" ).fadeIn( "fast" );
    }, function () {
        $(this).find( "ul" ).hide();
    });

    if ($(".mh-accordion").length) {
        $(".mh-accordion").accordion({
            animate: 300,
            autoHeight: false,
            heightStyle: "content",
            collapsible: true,
            active: false
        });
    }

    // Select Picker
    $(".selectpicker").selectpicker({
        style: '',
        dropupAuto: false
    });

    // Magnific Popup
    $( ".mh-popup" ).magnificPopup({
        type: "image",
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,
        mainClass: "mfp-no-margins mfp-with-zoom",
        image: {
            tError: "<a href=\" % url % \">The image #%curr%</a> could not be loaded.",
            verticalFit: true
        }
    });

    $(".mh-popup-group").magnificPopup({
        delegate: "a.mh-popup-group__element",
        type: "image",
        tLoading: "Loading image #%curr%...",
        mainClass: "mfp-no-margins mfp-with-zoom",
        fixedContentPos: false,
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: "<a href=\" % url % \">The image #%curr%</a> could not be loaded.",
            verticalFit: true
        }
    });

    $(".owl-carousel").each(function () {
        startCarousel($(this));
    });
})(jQuery);

function startCarousel(carousel) {
    var dots, nav, mediaSmall, mediaMedium, mediaBig, mediaHuge, autoPlay;

    dots = !carousel.hasClass("owl-carousel--no-dots");
    nav = carousel.hasClass("owl-carousel--nav");
    autoPlay = !carousel.hasClass("owl-carousel--no-auto-play");

    if (carousel.hasClass("owl-carousel--visible-1")) {
        mediaSmall = 1
        mediaMedium = 1
        mediaBig = 1
        mediaHuge = 1
    } else if (carousel.hasClass("owl-carousel--visible-2")) {
        mediaSmall = 1
        mediaMedium = 2
        mediaBig = 2
        mediaHuge = 2
    } else if (carousel.hasClass("owl-carousel--visible-3")) {
        mediaSmall = 1
        mediaMedium = 2
        mediaBig = 2
        mediaHuge = 3
    } else if (carousel.hasClass("owl-carousel--visible-4")) {
        mediaSmall = 1
        mediaMedium = 2
        mediaBig = 2
        mediaHuge = 4
    } else if (carousel.hasClass("owl-carousel--visible-5")) {
        mediaSmall = 1
        mediaMedium = 2
        mediaBig = 3
        mediaHuge = 5
    }

    carousel.on('initialized.owl.carousel', function () {
        var elements = [
            '.mh-agent__content',
            '.mh-testimonial__text',
            '.mh-post-grid__inner',
            '.mh-estate-vertical__content',
            '.mh-compare__column__content__top'
        ];
        $.each(elements, function (i, element) {
            var height = 0;
            var results = $(this).find(element);
            $.each(results, function (i, result) {
                var resultHeight = $(result).height();
                if (resultHeight > height) {
                    height = resultHeight;
                }
            }.bind(this));
            results.css("height", height + "px");
        }.bind(this));
    });

    var responsive
    if (carousel.hasClass("mh-clients")
        && (carousel.hasClass("owl-carousel--visible-3") || carousel.hasClass("owl-carousel--visible-4")
            || carousel.hasClass("owl-carousel--visible-5"))) {
        responsive = {
            0: {
                items: 2
            },
            768: {
                items: 3
            },
            1024: {
                items: mediaHuge - 1
            },
            1200: {
                items: mediaHuge
            }
        }
    } else {
        responsive = {
            0: {
                items: mediaSmall
            },
            768: {
                items: mediaMedium
            },
            1024: {
                items: mediaBig
            },
            1200: {
                items: mediaHuge
            }
        }
    }

    carousel.owlCarousel({
        rtl: $('html').attr('dir') === 'rtl',
        loop: true,
        margin: 12,
        dots: dots,
        autoplay: autoPlay,
        nav: nav,
        navText: [
            '<i class="fa fa-angle-left" aria-hidden="true"></i>',
            '<i class="fa fa-angle-right" aria-hidden="true"></i>'
        ],
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        responsive: responsive
    });
}

$(window).on("load", function () {
    if ($(".compose-mode").length) {
        setInterval(function () {
            if ($(".myhome-rev_slider").length > $(".myhome-rev_slider-vc").length) {
                $(".myhome-rev_slider:not(.myhome-rev_slider-vc)").each(function () {
                    $(this).addClass("myhome-rev_slider-vc");
                    $(this).show().revolution();
                });
            }

            if ($(".owl-carousel").length > $(".owl-carousel-vc").length) {
                $(".owl-carousel:not(.owl-carousel-vc)").each(function () {
                    $(this).addClass("owl-carousel-vc");
                    startCarousel($(this));
                });
            }
        }, 1000);
    }
});