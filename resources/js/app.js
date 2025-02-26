import './bootstrap';
//import Alpine from 'alpinejs';
//window.Alpine = Alpine;
//Alpine.start();


//import 'lightbox2/dist/js/lightbox-plus-jquery';
//import 'lightbox2/dist/css/lightbox.css';


import $ from 'jquery';
window.$ = window.jQuery = $;

import 'lightbox2/dist/css/lightbox.css';
import lightbox2 from 'lightbox2';

lightbox2.option({
    resizeDuration: 200,
    wrapAround: true
});
