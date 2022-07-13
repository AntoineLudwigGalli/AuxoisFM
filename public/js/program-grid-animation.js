// Fonction permettant "d'ouvrir la vue"
function openView(){

    // Suppression de l'ancien écouteur d'évènement click pour éviter leur accumulation
    $('.target').off('click');

    // Animation sur petit texte (le masquer)
    $('.target .tiny').fadeOut();

    $("body").addClass("blue-background");
    $(".target").removeClass("hvr-bounce-to-top");
    $(".target").addClass("overlayed-podcast");

    // Animations sur target (déplacement + agrandissement)
    $('.target').animate({

        'height' : '100vh',
        'width' : "100vw",

    }, 750, function(){
        $('#header').attr("style", "display: none !important");
        $('#more-news').removeClass("d-flex");
        $('#more-news').hide();
        $('#menu').hide();
        $('#news').fadeOut();
        $('.owl-carousel').fadeOut();
        $('#player').hide();
        $('body').append($(".target"));
        // Animation d'apparition du grand texte
        $('.target .normal').fadeIn();

        // Mise en place d'un écouteur d'évènement permettant au click de fermer la vue
        $('.target').click(function(){

            closeView();

        });

    });

}

// Fonction permettant de "fermer la vue"
function closeView(){

    // Suppression de l'ancien écouteur d'évènement click pour éviter leur accumulation
    $('.target').off('click');

    // Animation sur le grand texte (le masquer)
    $('.target .normal').hide();
    $('#header').fadeIn();
    $('#more-news').addClass("d-flex");
    $('#more-news').fadeIn();
    $('#menu').addClass("d-flex");
    $('#menu').fadeIn();
    $('#news').fadeIn();
    $('.owl-carousel').fadeIn();
    $('#player').fadeIn();
    $('#menu').prepend($(".target"));

    $(".target").addClass("hvr-bounce-to-top");
    $(".target").removeClass("overlayed-podcast");

    // Animations sur target (deplacement + agrandissement)
    $('.target').animate({

        'height' : '45vh',
        'width' : "34%",

    }, 750, function(){
        $("body").removeClass("blue-background");
        // Animation d'apparition du petit texte
        $('.target .tiny').fadeIn();

        // Mise en place d'un écouteur d'évènement permettant au click d'ouvrir la vue
        $('.target').click(function(){

            openView();

        });

    });

}


// Si la div target est cliquée, on ouvre la vue en appelant la fonction openView()
$('.target').click(function(){

    openView();

});