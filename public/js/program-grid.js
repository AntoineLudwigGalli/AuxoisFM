
function renderCalendar(){
        let calendarEl = document.getElementById('calendar-holder');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            locale: 'fr',
            defaultView: 'dayGridMonth',
            editable: true,
            eventSources: [
                {
                    url: "/json/shows",
                    method: "POST",
                    extraParams: {
                        filters: JSON.stringify({})
                    },
                    failure: () => {
                        alert("Une erreur est survenue à l'affichage de la grille des programmes. Merci de réessayer.");
                    },
                },
            ],
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'rrule' ], // https://fullcalendar.io/docs/plugin-index
            timeZone: 'local',
        });

        $.getJSON("/json/shows", function(data) {

            for (let i = 0; i < data.length; i++) {

                const startDate = new Date(data[i].startDate.date);
                const showTime = new Date(data[i].showTime.date);
                const showDuration = new Date(data[i].showDuration.date);

                // let showStartingMoment = startDate.getFullYear() + "-" + (startDate.getMonth()+1).toString().padStart(2, '0') + "-" + startDate.getDate().toString().padStart(2, '0') + " "  + showTime.getHours().toString().padStart(2, '0') + ":" + showTime.getMinutes().toString().padStart(2, '0');
                // const showStartingMoment = new Date(startDate.getTime() + showTime.getTime());
                const showStartingMoment = new Date(startDate).setHours(startDate.getHours() + showTime.getHours());
                const showEndingMoment = new Date(startDate).setHours(startDate.getHours() + showTime.getHours() + showDuration.getHours());
                console.log(showStartingMoment);
                console.log(showEndingMoment);


                calendar.addEvent({
                    // title: 'Metalesia',
                    // start: '2022-07-22 20:00',
                    title: data[i].name,
                    start: showStartingMoment,
                    end: showEndingMoment,
                })
            }
        })
        calendar.render();
    }
// Fonction permettant d'afficher une image avec une croix de fermeture dans un overlay
function displayCalendar(){


    //---------- Création de l'overlay (une div) : <div class="overlay"></div>
    let overlayElement = document.createElement('div');
    overlayElement.classList.add('overlay');
    overlayElement.classList.add('animate__animated');
    overlayElement.classList.add('animate__fadeInBottomLeft');



    //---------- Création du lien du bouton de fermeture (un lien) : <a href="#" class="close">X</a>
    let closeButtonElement = document.createElement('a');
    closeButtonElement.textContent = 'X';
    closeButtonElement.setAttribute('href', '#');
    closeButtonElement.classList.add('close');

    //---------- Création du lien du bouton de switch avec la liste des émissions et les derniers podcasts : <button" class="switchToShowList">Liste des Emissions</button>
    let switchShowButtonElement = document.createElement('button');
    switchShowButtonElement.textContent = 'Liste des émissions';
    switchShowButtonElement.classList.add('switchToShowList', 'btn', 'btn-light');


    // Ajout des boutons dans l'overlay
    overlayElement.prepend(closeButtonElement);
    overlayElement.prepend(switchShowButtonElement);


    // Écouteur d'évènement sur le bouton pour supprimer l'overlay au clic
    closeButtonElement.addEventListener('click', function(){
        removeCalendar();
    });

    //---------- Création du calendrier : <div id="calendar-holder"</div>
    //     </div>
    let calendarElement = document.querySelector('#calendar-holder');
    calendarElement.classList.remove("d-none");

    // Ajout du calendrier dans l'overlay
    overlayElement.prepend(calendarElement);

    //---------- Création de la liste des émissions : <div id="show-list"</div>
    //     </div>
    let showListElement = document.querySelector('#show-list');
    overlayElement.prepend(showListElement);


// Écouteur d'évènement sur le bouton Liste des émissions dans l'overlay
   switchShowButtonElement.addEventListener('click', function(){
       if (!calendarElement.classList.contains("d-none")){
           calendarElement.classList.add('d-none');
           showListElement.classList.remove("d-none");
           switchShowButtonElement.textContent = 'Grille des programmes';
       } else {
           calendarElement.classList.remove('d-none');
           showListElement.classList.add("d-none");
           switchShowButtonElement.textContent = 'Liste des émissions';
       }
    });


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


// Écouteur d'évènement sur la section "grille et podcasts pour afficher l'overlay au clic et charger le calendrier ensuite
document.querySelector("#podcasts").addEventListener('click', function(){
    if (!document.querySelector(".overlay")){
        displayCalendar();
        renderCalendar();
    }
});




