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

    initMasks();
});

function digitsOnly(input) {
    return input.replace(/\D/g, '');
}

function maskBi(input) {
    const clean = input.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 14);
    let result = '';
    for (let i = 0; i < clean.length; i++) {
        const ch = clean[i];
        if (i <= 8) {
            if (/[0-9]/.test(ch)) result += ch;
        } else if (i <= 10) {
            if (/[A-Z]/.test(ch)) result += ch;
        } else {
            if (/[0-9]/.test(ch)) result += ch;
        }
    }
    return result;
}

function maskPhone(input) {
    let d = input.replace(/\D/g, '');
    if (d.startsWith('244')) d = d.slice(3); // remove prefixo de país se ja veio
    d = d.slice(0, 9);
    if (d.length === 0) return '';
    if (d.length <= 3) return '+244 ' + d;
    if (d.length <= 6) return '+244 ' + d.slice(0, 3) + ' ' + d.slice(3);
    return '+244 ' + d.slice(0, 3) + ' ' + d.slice(3, 6) + ' ' + d.slice(6);
}

function initMasks() {
    $('[data-mask="bi"]').on('input', function () {
        this.value = maskBi(this.value);
    });

    $('[data-mask="phone"]').on('input', function () {
        this.value = maskPhone(this.value);
    });

    $('[data-mask="numeric"]').on('input', function () {
        this.value = digitsOnly(this.value);
    });

    // Field focus states (add icon highlight)
    $('.field input, .field select').on('focus', function () {
        $(this).closest('.field').addClass('is-focused');
    }).on('blur', function () {
        $(this).closest('.field').removeClass('is-focused');
    });
}

Alpine.start();
