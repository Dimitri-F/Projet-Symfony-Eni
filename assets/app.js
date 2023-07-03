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

