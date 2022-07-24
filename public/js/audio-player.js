

TweenMax.set('#ripple-circle circle', {scale:0.5, transformOrigin:'50% 50%'});
TweenMax.fromTo('#svg-line', 2, {x:-2.5}, {x:2.5,repeat:-1,ease:Elastic.easeInOut, yoyo:true});
TweenMax.fromTo('.loading-image', 2, {scale:1, autoAlpha:1}, {scale:0.75, autoAlpha:0.5, transformOrigin:'50% 50%',ease:Bounce.easeIn,yoyo:true, repeat:-1});
TweenMax.to('#inner-circ', 2, {rotation:360, transformOrigin:'50% 50%', repeat:-1});
TweenMax.to('#outer-circ', 4, {rotation:-360, transformOrigin:'50% 50%', repeat:-1});

$('.audiosvg').hover(function(){
    hoverCircle = $(this).find('#hover-circle .st4')
    $(hoverCircle).css({'fill': '#7691BA'});
    TweenMax.to('.play-text', 0.35, {autoAlpha: 1, y:-70, transformOrigin:'50% 50%', ease:Back.easeOut});
}, function(){
    $(hoverCircle).css({'fill': '#486CA3'});
    TweenMax.to('.play-text', 0.35, {autoAlpha: 0, y:0, transformOrigin:'50% 50%', ease:Back.easeIn});
});

$('.audiosvg').click(function(){
    var spinDisc = $(this).find('#svg-audio');
    var rippleCircle = $(this).find('#ripple-circle circle');
    var activeBox = $(this).prev();
    TweenMax.set(spinDisc, {rotation:0, transformOrigin:'50% 50%'});
    TweenMax.to(spinDisc, 2, {rotation:360, transformOrigin:'50% 50%',repeat:-1,ease:Linear.easeNone});
    TweenMax.staggerTo(rippleCircle, 2.1, {scale:20, transformOrigin:'50% 50%', autoAlpha: 0, repeat:-1, ease:Linear.easeNone},0.7);
    $(activeBox).show();
    TweenMax.set('.play-text', {autoAlpha: 0, y:0, transformOrigin:'50% 50%'});
    TweenMax.set('.pause-text', {autoAlpha: 0, y:0, transformOrigin:'50% 50%'});
});

$('.active-box').click(function(){
    $(this).hide();
    TweenMax.killTweensOf($(this).next().find('#svg-audio'));
    TweenMax.killTweensOf($(this).next().find('#ripple-circle circle'));
    TweenMax.set('#ripple-circle circle', {scale:0.5, transformOrigin:'50% 50%'});
    TweenMax.set('.play-text', {autoAlpha: 0, y:0, transformOrigin:'50% 50%'});
    TweenMax.set('.pause-text', {autoAlpha: 0, y:0, transformOrigin:'50% 50%'});
});

$('.active-box').hover(function(){
    hoverCircle = $(this).parent().find('#hover-circle .st4')
    $(hoverCircle).css({'fill': '#7691BA'});
    TweenMax.to('.pause-text', 0.35, {autoAlpha: 1, y:-70, transformOrigin:'50% 50%', ease:Back.easeOut});
}, function(){
    $(hoverCircle).css({'fill': '#486CA3'});
    TweenMax.to('.pause-text', 0.35, {autoAlpha: 0, y:0, transformOrigin:'50% 50%', ease:Back.easeIn});
});

let audioMp3 = new Audio();
audioMp3.src = "http://auxoisfm.ice.infomaniak.ch/auxoisfm-128";


function playAudio(){
    audioMp3.play();
}

function pauseAudio(){
    audioMp3.pause();
}

$('.circles').click(function(){
    TweenMax.to('.socialmedia-overlay', 1, {css:{'top':0},ease:Bounce.easeOut});
    TweenMax.to('.fa-angle-up', 0.5, {autoAlpha: 1, ease:Power2.easeOut,delay:1});
    TweenMax.staggerFrom('.socialmedia-content div', 0.5, {scale:0.1, transformOrigin:'50% 50%', autoAlpha: 0, ease:Back.easeOut, delay:0.75},0.5);

})
$('.fa-angle-up').click(function(){
    TweenMax.to('.socialmedia-overlay', 0.5, {css:{'top':'-100%'},ease:Power2.easeInOut});
    TweenMax.to(this, 0.5, {autoAlpha: 0, ease:Power2.easeOut});
})