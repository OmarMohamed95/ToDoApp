import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import notification from './modules/notification'
import cookie from './modules/cookie'
import task from './modules/task'

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth: auth,
        notification: notification,
        cookie: cookie,
        task: task,
    }
})