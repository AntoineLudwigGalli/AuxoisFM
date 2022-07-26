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
        768:{
            items:2
        },
        992:{
            items:4
        }
    }
})