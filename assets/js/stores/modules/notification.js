import axios from "axios";

export default {
    state: {
        tasks: {},
        notifications: Number,
        appPermission: false,
        browserPermission: false,
        showDeniedMessage: false,
        message: 'Please allow the notification in your browser!',
        appPermissionCookieName: "appPermission",
    },
    getters: {
        getTasks: state => {
            return state.tasks;
        },
        getAppPermission: state => {
            return state.appPermission;
        },
        getBrowserPermission: state => {
            return state.browserPermission;
        },
        showDeniedMessage: state => {
            return state.showDeniedMessage;
        },
        getMessage: state => {
            return state.message;
        },
    },
    mutations: {
        UPDATE_BROWSER_PERMISSION: (state, value) => {
            state.browserPermission = value;
        },
        UPDATE_APP_PERMISSION: (state, value) => {
            state.appPermission = value;
        },
        SHOW_DENIED_BROWSER_PERMISSION_MESSAGE: (state) => {
            state.showDeniedMessage = true; 
        },
        HIDE_DENIED_BROWSER_PERMISSION_MESSAGE: (state) => {
            state.showDeniedMessage = false; 
        },
        SET_TIME_OUT: (state, timeout) => {
            state.notifications = timeout; 
        },
        CLEAR_TIME_OUT: (state) => {
            clearTimeout(state.notifications);             
        },
    },
    actions: {
        notify: (context, {id, title}) => {
            new Notification('ToDoApp',{
                body: title,
                icon: 'http://127.0.0.1:8000/uploads/brandIcon/favicon-32x32.png'
            });
            context.dispatch("updateNotifyStatus", id);
        },
        getUnnotifiedTasks: state => {
            return new Promise((resolve, reject) => {
                axios.get('http://127.0.0.1:8000/api/task/unnotifed')
                .then( res => {
                    resolve(res.data);
                    state.tasks = res.data;
                })
                .catch(error => {
                    console.error(error);
                })
            })
        },
        pushNotification: context => {
            if(context.getters.getAppPermission)
            {
                context
                .dispatch("getUnnotifiedTasks")
                .then(res => {
                    
                    context.dispatch('clearNotifications');
                    
                    for (let [k, v] of Object.entries(res)){
                        
                        let now = new Date();
                        let runAt = new Date(v.runAt);
                        let runAfter =  runAt - now;
                        
                        let title = v.title;
                        let id = v.id;

                        let timeout = setTimeout(() => {
                            context.dispatch("notify", {id, title})
                        }, runAfter);
                        context.commit('SET_TIME_OUT', timeout);
                    }

                })
                .catch(error => {
                    console.log(error);
                })
            }
        },
        updateNotifyStatus: (context, id) => {
            axios({
                method: 'patch',
                url: `http://127.0.0.1:8000/api/task/notified/${id}`,
            })
        },
        askPermission: (context) => {
            Notification.requestPermission().then(result => {
                if(result === 'granted')
                {
                    context.commit('UPDATE_BROWSER_PERMISSION', true);   
                }
                else if (result === 'denied')
                {
                    context.commit('UPDATE_BROWSER_PERMISSION', false);
                }
            });
        },
        updateAppPermissionCookie: (context, value) => {
            let key = context.state.appPermissionCookieName;
            context.dispatch('setCookie', {key, value});

            context.dispatch('setAppPermissionToCookie');
        },
        setAppPermissionToCookie: (context) => {
            context
                .dispatch('getCookie', context.state.appPermissionCookieName)
                .then(res => {
                    let value = res;
                    context.commit("UPDATE_APP_PERMISSION", value);
                })
                .catch(error => {
                    console.error(error)
                })
        },
        showDeniedPermissionMessage: (context) => {
            context.commit('SHOW_DENIED_BROWSER_PERMISSION_MESSAGE')
        },
        hideDeniedPermissionMessage: (context) => {
            context.commit('HIDE_DENIED_BROWSER_PERMISSION_MESSAGE')
        },
        clearNotifications: (context) => {
            context.commit('CLEAR_TIME_OUT');
        },
    },
}