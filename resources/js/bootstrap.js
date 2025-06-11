import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import 'bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;
