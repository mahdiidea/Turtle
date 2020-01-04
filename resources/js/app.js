/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

import Toasted from 'vue-toasted';

Vue.use(Toasted);

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('activities', require('./components/activities').default);
Vue.component('contents-index', require('./components/contents/index').default);
Vue.component('contents-object', require('./components/contents/object').default);
Vue.component('contents-create', require('./components/contents/create').default);

Vue.component('messages-index', require('./components/messages/index').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.mixin({
    methods: {
        fromNow: function (datetime) {
            let moment = require('moment');
            return moment(datetime, "YYYY-MM-DD HH:mm:ss").fromNow()
        },
        resize: function (path, width = null, height = null, quality = null, crop = null) {
            let address;
            if (width == null && height == null) {
                address = "/" + path;
            } else {
                address = "/timthumb.php?src=" + path;
                address += (width != null ? "&w=" + width : "");
                address += ((height != null && height > 0) ? "&h=" + height : "");
                address += (crop != null ? "&zc=" + crop : "&zc=1");
                address += (quality != null ? "&q=" + quality + "&s=1" : "&q=95&s=1");
            }
            return address;
        },
    }
});


const app = new Vue({
    el: '#app'
});
