import _ from 'lodash';
import axios from 'axios';

window.axios = axios;
window._ = _;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
