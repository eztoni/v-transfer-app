

import './bootstrap';
/* FLOATING ACTION BUTTON */
import 'mfb/src/mfb';

/* ALPINE */

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask'
Alpine.plugin(mask)
window.Alpine = Alpine;
Alpine.start();


/* THEME CHANGER */
import {themeChange} from "theme-change"
themeChange(false)


Livewire.on('destination_changed', () => {
    window.location.reload();
})

