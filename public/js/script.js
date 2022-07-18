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