import $ from 'jquery';
import select2 from "select2";

import flatpckr from 'flatpickr';
window.flatpckr = flatpckr

window.$ = $;
window.select2 = select2;

/* IODINE */
import { Iodine } from '@kingshott/iodine';

new Iodine();

// import {WOW} from 'wowjs'
// let wow = new WOW();
// wow.init()
// document.addEventListener("DOMContentLoaded", () => {
//     Livewire.hook('message.processed', (message, component) => {
//         wow.sync()
//     })
//     Livewire.hook('message.received', (message, component) => {
//         wow.sync()
//     })
// });
