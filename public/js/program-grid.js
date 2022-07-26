function renderCalendar(){
        let calendarEl = document.getElementById('calendar-holder');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            lazyFetching: true,
            locale: 'fr',
            defaultView: 'timeGridWeek',
            handleWindowResize: true,
            height: 'auto',
            contentHeight: 'auto',
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
            eventClick: function(info) {
                info.jsEvent.preventDefault(); // don't let the browser navigate

                if (info.event.url) {
                    window.open(info.event.url);
                }
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'rrule' ], // https://fullcalendar.io/docs/plugin-index
            timeZone: 'local',
        });

        //On récupère le tableau json des émissions pour afficher les infos dans le calendrier
        $.getJSON("/json/shows", function(data) {

            for (let i = 0; i < data.length; i++) {

                let startDate = new Date(data[i].startDate.date);
                let showTime = new Date(data[i].showTime.date);
                let showDuration = new Date(data[i].showDuration.date);
                // On formate les dates issues de la bdd pour alimenter le calendrier
                let showStartingMoment = new Date(startDate)
                showStartingMoment.setHours(startDate.getHours() + showTime.getHours());
                showStartingMoment.setMinutes(startDate.getHours() + showTime.getMinutes());
                let showEndingMoment = new Date(startDate);
                showEndingMoment.setHours(startDate.getHours() + showTime.getHours() + showDuration.getHours());
                showEndingMoment.setMinutes(startDate.getMinutes() + showTime.getMinutes() + showDuration.getMinutes());


                if (data[i].timeInterval != 0){
                    for (let j = 0; j < 52; j++) {
                        
                        if (j != 0) {
                            showStartingMoment = new Date(showStartingMoment.setDate(showStartingMoment.getDate()+data[i].timeInterval));
                        }

                        calendar.addEvent({
                            title: data[i].name,
                            start: showStartingMoment,
                            end: showEndingMoment,
                            url: data[i].webpageLink,
                            overlap: false,
                        }) ;
                    }

                    } else {

                    let startTimeTimestamp = new Date(showTime.getTime());
                    let showDurationTimestamp = new Date(showDuration.getTime());
                    let endTimeTimestamp = new Date(startTimeTimestamp);
                    endTimeTimestamp.setHours(startTimeTimestamp.getHours() + showDurationTimestamp.getHours());
                    endTimeTimestamp.setMinutes(startTimeTimestamp.getMinutes() + showDurationTimestamp.getMinutes());

                    let startTime = ("0" + startTimeTimestamp.getHours()).slice(-2) + ":" + ("0" + startTimeTimestamp.getMinutes()).slice(-2);
                    let endTime = ("0" + endTimeTimestamp.getHours()).slice(-2) + ":" + ("0" + endTimeTimestamp.getMinutes()).slice(-2);

                    calendar.addEvent({
                        title: data[i].name,
                        daysOfWeek: data[i].broadcastDay, // these recurrent events move separately
                        startTime: startTime,
                        endTime: endTime,
                        url: data[i].webpageLink,
                        overlap: false,
                    }) ;
                }
            }

        });

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
    $('#calendar-holder div').remove();

}

// Écouteur d'évènement sur la section "grille et podcasts pour afficher l'overlay au clic et charger le calendrier ensuite
document.querySelector("#podcasts").addEventListener('click', function(){
    if (!document.querySelector(".overlay")){
        displayCalendar();
        renderCalendar();
    }
});




