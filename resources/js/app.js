import './bootstrap';

// Falls du Alpine.js brauchst, kannst du es einkommentieren
// import Alpine from 'alpinejs';
// window.Alpine = Alpine;
// Alpine.start();

import $ from 'jquery';
window.$ = window.jQuery = $;

// Lightbox2 importieren (CSS und JS)
import 'lightbox2/dist/css/lightbox.css';
import lightbox from 'lightbox2';

// Lightbox für den globalen Zugriff verfügbar machen
window.lightbox = lightbox;

// Lightbox-Optionen setzen
lightbox.option({
    resizeDuration: 200,
    wrapAround: true,
    albumLabel: "Bild %1 von %2",
    fadeDuration: 300,
    disableStorage: true,  // Falls es den `Access to storage` Fehler gibt
});


