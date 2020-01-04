/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import Datetime from 'vue-datetime'
import axios from 'axios'
// You need a specific loader for CSS files

// font awesome
import { library } from '@fortawesome/fontawesome-svg-core'
// font awesome icon names
import { faList, faTasks, faHome, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'


// Components
import 'vue-datetime/dist/vue-datetime.css'
import home from './components/home.vue'
import category from './components/category.vue'
import categoryAdd from './components/categoryAdd.vue'
import list from './components/list.vue'
import listAdd from './components/listAdd.vue'
import task from './components/task.vue'
import taskAdd from './components/taskAdd.vue'
import register from './components/register.vue'
import login from './components/login.vue'

// store
import store from './stores/store'

Vue.use(VueRouter)
Vue.use(Datetime)

//font awesome config
library.add(faList, faTasks, faHome, faSpinner)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.config.productionTip = false

Vue.prototype.$site_base_url = 'http://127.0.0.1:8000/'
Vue.prototype.$uploads_base_url = 'http://127.0.0.1:8000/uploads'

const routes = [
  { path: '/test', name: 'home', component: home },
  { path: '/category', name: 'category', component: category },
  { path: '/category/add', name: 'categoryAdd', component: categoryAdd },
  { path: '/list', name: 'list', component: list },
  { path: '/list/add', name: 'listAdd', component: listAdd },
  { path: '/task', name: 'task', component: task },
  { path: '/task/add', name: 'taskAdd', component: taskAdd },
  { path: '/register', name: 'register', component: register },
  { path: '/login', name: 'login', component: login },
]

let isRefreshing = false;
let subscribers = [];

axios.interceptors.response.use(
  response => {
    return response;
  },
  err => {
    const {
      config,
      response: { status, data }
    } = err;

    const originalRequest = config;

    if (data.message === "Missing JWT token") {
      router.push({ name: 'login' });
      return Promise.reject(false);
    }

    if (originalRequest.url.includes("login_check")) {
      return Promise.reject(err);
    }

    if (status === 401 && data.message === 'Expired JWT token') {
      if (!isRefreshing) {
        isRefreshing = true;
        store
          .dispatch("refreshToken")
          .then(({ status }) => {
            if (status === 200 || status == 204) {
              isRefreshing = false;
            }
            subscribers = [];
          })
          .catch(error => {
            console.error(error);
          });
      }

      const requestSubscribers = new Promise(resolve => {
        subscribeTokenRefresh(() => {
          resolve(axios(originalRequest));
        });
      });

      onRefreshed();

      return requestSubscribers;
    }
  }
);

function subscribeTokenRefresh(cb) {
  subscribers.push(cb);
}

function onRefreshed() {
  subscribers.map(cb => cb());
}

subscribers = [];

const router = new VueRouter({
    routes,
    mode: 'history',
})

// start guard -->

router.beforeEach((to, from, next) => {

  store.dispatch('checkAuth')
  .then(response => {

    if(response.data.isAuthenticated)
    {
      if (to.name === 'login' || to.name === 'register')
      {
        next({name: 'task'});
      }
      else
      {
        next();
      }
    }
    else
    {
      if(to.name === 'login' || to.name === 'register')
      {
        next();
      }
      else
      {
        next({name: 'login'});
      }
    }

  })
  .catch(error => {
      console.error(error);
  });
  
})

// end guard -->

// Push nutifications start

const notification = async () => {
  await store.dispatch("askPermission");
  await store.dispatch("setAppPermissionToCookie");
  store.dispatch("pushNotification");
}

notification();



// Push nutifications end


new Vue({
    props: {
      
    },
    components: { App },
    render: h => h(App),
    store,
    router
}).$mount('#app')