$(document).ready(function () {
    new WOW().init();
    $('.navbar-nav li').click(function () {
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
    });

    const options = {
        loop: true,
        margin: 10,
        navText: ['', ''],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            600: {
                items: 2,
                nav: false
            },
            900: {
                items: 3,
                nav: false
            },
            1200: {
                items: 3,
                nav: true,
                loop: false,
//                margin: 20
            }
        }

    };
    $('.owl-carousel').owlCarousel(options);
    //     scroll
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });

    $('.scrollToTop').click(function () {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    $('body').scrollspy({
        target: '#navbar'
    })
});
