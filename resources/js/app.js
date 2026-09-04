import Alpine from 'alpinejs';
import $ from 'jquery';
import select2 from 'select2';
import { createIcons, icons } from 'lucide';

// Set up jQuery and Select2
window.$ = window.jQuery = $;
select2();

window.Alpine = Alpine;

// Initialize Lucide Icons globally
window.createIcons = createIcons;
window.lucideIcons = icons;

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });

    $('select:not(.no-select2)').each(function () {
        const $el = $(this);
        $el.select2({
            width: '100%',
            language: 'pt_BR',
            theme: 'default',
        });
    });
});

Alpine.start();
