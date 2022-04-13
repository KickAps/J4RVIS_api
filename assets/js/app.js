/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';

import 'bootstrap';

export function showLoader() {
    document.querySelector("div#loader").removeAttribute('hidden');
}

export function hideLoader() {
    document.querySelector("div#loader").setAttribute('hidden', 'hidden');
}