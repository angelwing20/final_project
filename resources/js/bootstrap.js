import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import 'bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'jquery-validation';

import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;

import notifier from "notifier-js";
window.notifier = notifier;

import Swal from "sweetalert2";
window.Swal = Swal;

import Chart from 'chart.js/auto';
window.Chart = Chart;
