/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Initialization for ES Users
import {
    Datepicker,
    Input,
    initTE,
} from "tw-elements";

initTE({ Datepicker, Input });


    // Désactiver la validation des champs du formulaire lorsque le bouton "cancel" est cliqué
    //Dans la vue manageProfile.html.twig
//     document.querySelector('button[name="cancel"]').addEventListener('click', function () {
//     Array.from(document.querySelectorAll('.profileForm input, .profileForm select')).forEach(function (element) {
//         element.removeAttribute('required');
//     });
// });


document.addEventListener('DOMContentLoaded', (event) => {
    var width = window.screen.availWidth;
    var height = window.screen.availHeight;

    document.cookie = "screen_width=" + width;
    document.cookie = "screen_height=" + height;
});

// Récupération du bouton d'ouverture du modal
var openButton = document.getElementById('openModal');

// Ajout d'un écouteur d'événements pour ouvrir le modal lorsqu'un clic est détecté
openButton.addEventListener('click', function(event) {
    event.preventDefault(); // Empêche l'action par défaut du bouton
    var dialog = document.getElementById('my_modal_2'); // Récupère le modal par son ID
    dialog.showModal(); // Ouvre le modal
});

// Récupération du bouton de fermeture du modal
var closeButton = document.getElementById('closeModal');

// Ajout d'un écouteur d'événements pour fermer le modal lorsqu'un clic est détecté
closeButton.addEventListener('click', function() {
    var dialog = document.getElementById('my_modal_2'); // Récupère le modal par son ID
    dialog.close(); // Ferme le modal
});

