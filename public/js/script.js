// Flip animation on player

$(".front-btn").click(function () {
    $('#player .flip-card-inner').addClass('is-flipped');
})

$(".back-btn").click(function () {
    $('#player .flip-card-inner').removeClass('is-flipped');
})


// Modif du champ adresse mail au click dans le profil user
$(".user-profile-form").hide();

$(".user-profile-data.user-email").click(function (){
    $('.user-profile-data.user-email').hide();
    $(".user-profile-form.user-email").show();
    $('.user-profile-form.user-pseudo').hide();
    $(".user-profile-data.user-pseudo").show();
})

$(".user-profile-data.user-pseudo").click(function (){
    $('.user-profile-data.user-pseudo').hide();
    $(".user-profile-form.user-pseudo").show();
    $('.user-profile-form.user-email').hide();
    $(".user-profile-data.user-email").show();

})

$(".user-profile-cancel").click(function (){
    $('.user-profile-data').show();
    $(".user-profile-form").hide();
})

// GAP audio player

GreenAudioPlayer.init({
    selector: '.gap-audio-player', // inits Green Audio Player on each audio container that has class "player"
    stopOthersOnPlay: true,
    showTooltips : true,
    showDownloadButton : true,
    enableKeystrokes : true
});

// Afficher ou replier la playlist

// $(playlistButtons).each(function (){
//     $(this).click( function (){
//         $('.playlist-display').toggleClass('d-none')
//         if ($('.angle').hasClass('fa-angle-down')) {
//             $('.angle').removeClass('fa-angle-down');
//             $('.angle').addClass('fa-angle-up');
//         } else {
//             $('.angle').removeClass('fa-angle-up');
//             $('.angle').addClass('fa-angle-down');
//         }
//     })
// })
function displayPlaylist(playlistToDisplay){

    $('.playlist-display-' + playlistToDisplay).toggleClass('d-none')
        if ($('.angle-'+playlistToDisplay).hasClass('fa-angle-down')) {
            $('.angle-'+playlistToDisplay).removeClass('fa-angle-down').addClass('fa-angle-up');
        } else {
            $('.angle-'+playlistToDisplay).removeClass('fa-angle-up').addClass('fa-angle-down');
        }
}

let playlistButtons = document.querySelectorAll('.playlist-button');

playlistButtons.forEach((playlistButton) => {

    // Chaque bouton "Playlist" aura un écouteur d'évènement "click"
    playlistButton.addEventListener('click', function(){

        // On récupère le numéro de la playlist à afficher (stocké dans l'attribut data-playlist du bouton cliqué)
        let playlistNumber = this.dataset.playlist;

        // Affichage de la playlist correspondant au numéro récupéré précédemment en le passant en paramètre de notre fonction displayPlaylist()
        displayPlaylist(playlistNumber);
    });
});