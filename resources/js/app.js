import Alpine from 'alpinejs';
import $ from 'jquery';
import select2 from 'select2';

// Set up jQuery and Select2
window.$ = window.jQuery = $;
select2();

window.Alpine = Alpine;
Alpine.start();
