require('./bootstrap');
/* FLOATING ACTION BUTTON */
require('mfb/src/mfb');

/* ALPINE */
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

/* THEME CHANGER */
import {themeChange} from "theme-change"
themeChange(false)

import './toastr'




