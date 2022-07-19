// Fonction permettant d'afficher une image avec une croix de fermeture dans un overlay
function displayCalendar(){


    //---------- Création de l'overlay (une div) : <div class="overlay"></div>
    let overlayElement = document.createElement('div');
    overlayElement.classList.add('overlay');


    //---------- Création du lien du bouton de fermeture (un lien) : <a href="#" class="close">X</a>
    let closeButtonElement = document.createElement('a');
    closeButtonElement.textContent = 'X';
    closeButtonElement.setAttribute('href', '#');
    closeButtonElement.classList.add('close');

    // Ajout du bouton dans l'overlay
    overlayElement.prepend(closeButtonElement);

    // Écouteur d'évènement sur le bouton pour supprimer l'overlay au clic
    closeButtonElement.addEventListener('click', function(){
        removeCalendar();
    });


    //---------- Création du calendrier : <div id="calendar-holder"</div>
    //     </div>
    let calendarElement = document.querySelector('#calendar-holder');
    calendarElement.classList.remove("d-none");

    // Ajout de l'image dans l'overlay
    overlayElement.prepend(calendarElement);


    // Ajout de l'overlay (+ le bouton et le calendrier qui sont déjà dedans) dans le body
    document.querySelector('body').append(overlayElement);
}


// Fonction permettant de supprimer l'overlay affiché (avec le calendrier et le bouton dedans)
function removeCalendar(){
    let calendarElement = document.querySelector('#calendar-holder');
    document.querySelector('body').prepend(calendarElement);
    calendarElement.classList.add("d-none");
    let overlayElement = document.querySelector('.overlay');
    overlayElement.parentElement.removeChild(overlayElement);

}

// Écouteur d'évènement sur la section "grille et podcasts pour afficher l'overlay au clic


    document.querySelector("#podcasts").addEventListener('click', function(){
        if (!document.querySelector(".overlay")){
        displayCalendar();
        }
    });


