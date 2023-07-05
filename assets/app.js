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

document.addEventListener('DOMContentLoaded', (event) => {
    var width = window.screen.availWidth;
    var height = window.screen.availHeight;

    document.cookie = "screen_width=" + width;
    document.cookie = "screen_height=" + height;
});


/////////////////////////////// POPUP POUR SUPPRIMER UN USER

// Récupération des boutons d'ouverture et de fermeture des modaux
var openButtons = document.querySelectorAll('[id^="openModal_"]');
var closeButtons = document.querySelectorAll('[id^="closeModal_"]');
var backdropCloseButtons = document.querySelectorAll('[id^="backdropClose_"]');

// Pour chaque bouton d'ouverture, ajouter un écouteur d'événements
openButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Empêche l'action par défaut du bouton
        var id = button.id.split('_')[1]; // Récupère l'ID du participant
        var dialog = document.getElementById('my_modal_' + id); // Récupère le modal correspondant
        dialog.showModal(); // Ouvre le modal
    });
});

// Pour chaque bouton de fermeture, ajouter un écouteur d'événements
closeButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        var id = button.id.split('_')[1]; // Récupère l'ID du participant
        var dialog = document.getElementById('my_modal_' + id); // Récupère le modal correspondant
        dialog.close(); // Ferme le modal
    });
});

// Pour chaque bouton de fermeture sur le fond, ajouter un écouteur d'événements
backdropCloseButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        var id = button.id.split('_')[1]; // Récupère l'ID du participant
        var dialog = document.getElementById('my_modal_' + id); // Récupère le modal correspondant
        dialog.close(); // Ferme le modal
    });
});

var deleteButtons = document.querySelectorAll('.btn-error');

deleteButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        var userId = this.getAttribute('data-id');
        var deleteInput = document.getElementById('delete');
        deleteInput.value = userId;
    });
});