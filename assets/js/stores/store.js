import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import notification from './modules/notification'
import cookie from './modules/cookie'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth: auth,
        notification: notification,
        cookie: cookie,
    }
})