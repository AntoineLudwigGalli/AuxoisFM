$('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    dots: true,
    autoplay: true,
    autoplayHoverPause: true,
    autoplayTimeout: 2500,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:2
        },
        1000:{
            items:4
        }
    }
})