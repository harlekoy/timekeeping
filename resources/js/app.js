import Editor from 'vue-editor-js'

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Swal = require('sweetalert2')
window.moment = require('moment')

require('./toast')

Vue.use(Editor)

Vue.component('post-editor', require('./components/PostEditor.vue'));
Vue.component('post-card', require('./components/PostCard.vue'));
Vue.component('posts', require('./components/Posts.vue'));
Vue.component('loading', require('./components/Loading.vue'));
Vue.component('empty-state', require('./components/EmptyState.vue'));
Vue.component('attendances', require('./components/Attendances.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
