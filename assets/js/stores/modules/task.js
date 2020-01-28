import axios from "axios";

export default {
    state: {
        loading: true,
        res: {},
        message: {},
        lists: {},
        tasks: {
            tasks: {},
            loading: true,
        },
        isEditing: false,
        currentTask: {
            id: Number,
            isUpdated: false
        }    
    },
    getters: {
        getTasksLoading: state => {
            return state.tasks.loading
        },
        getLoading: state => {
            return state.loading
        },
        getRes: state => {
            return state.res
        },
        message: state => {
            return state.message
        },
        getLists: state => {
            return state.lists
        },
        getIsEditing: state => {
            return state.isEditing
        },
        getTaskId: state => {
            return state.currentTask.id
        },
        getAllTasks: state => {
            return state.tasks
        },
    },
    mutations: {
        SET_TASKS_LOADING: (state, value) => {
            state.tasks.loading = value
        },
        SET_LOADING: (state, value) => {
            state.loading = value
        },
        SET_RES: (state, res) => {
            state.res = res
        },
        SET_TASKS: (state, value) => {
            state.tasks = value
        },
        SET_MESSAGE: (state, message) => {
            state.message = message
        },
        SET_LISTS: (state, lists) => {
            state.lists = lists
        },
        UPDATE_IS_EDITING: (state, value) => {
            state.isEditing = value
        },
        SET_TASK_ID: (state, id) => {
            state.currentTask.id = id
        },
        SET_IS_UPDATED: (state, value) => {
            state.currentTask.isUpdated = value
        },
        CLEAR_MESSAGE: state => {
            state.message = {}
        },
    },
    actions: {
        getTasksByDateDesc: context => {
            axios.get('http://127.0.0.1:8000/api/task')
            .then((response) => {
                context.commit('SET_TASKS_LOADING', false);
                //check if this working fine when the status is 204
                let tasks = (response.status === 204) ? response.text() : response.data;
                context.commit('SET_TASKS', tasks)
            })
            .catch((error) => {
                throw error;
            });
        },
        addTask: (context, formData) => {
            axios({
                url: "http://127.0.0.1:8000/api/task",
                method: 'POST', 
                data: formData,
            })
            .then(res => {
                if(res.status === 200)
                {
                    let data = res.data;
                    context.commit('SET_MESSAGE', data)
                    context.dispatch("pushNotification");
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    let data = error.response.data;
                    context.commit('SET_MESSAGE', data)
                }
                console.log(error.response);
            })

        },
        editTask: (context, formData) => {
            axios({
                url: `http://127.0.0.1:8000/api/task/${context.state.currentTask.id}`,
                method: 'POST', 
                data: formData,
            })
            .then(res => {
                if(res.status === 200)
                {
                    let data = res.data;
                    context.commit('SET_MESSAGE', data)
                    context.commit('UPDATE_IS_EDITING', false)
                    context.commit('SET_IS_UPDATED', true)
                    context.dispatch("getTasksByDateDesc")
                    context.dispatch("pushNotification")
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    let data = error.response.data;
                    context.commit('SET_MESSAGE', data)
                }
                console.log(error.response);
            })

        },
        getAllLists: context => {
            
            axios.get('http://127.0.0.1:8000/api/list')
            .then(res => {
                let listsData = res.data;
                context.commit('SET_LISTS', listsData);
                context.commit('SET_LOADING', false);
            })
            .catch(console.error)
        },
        updateIsEditing: (context, value)  => {            
            context.commit('UPDATE_IS_EDITING', value);
        },
        setTaskId: (context, id)  => {
            context.commit("SET_TASK_ID", id)   
        },
        setLoading: (context, value)  => {
            context.commit("SET_LOADING", value)   
        },
        getTaskById: (context)  => {
            if(context.state.currentTask.id !== context.state.res.id || context.state.currentTask.isUpdated === true)
            {
                axios({
                    url: `http://127.0.0.1:8000/api/task/${context.state.currentTask.id}`,
                    method: 'GET',
                })
                .then(res => {
                    context.commit('SET_RES', res.data);
                    context.commit('SET_LOADING', false);
                    context.commit('SET_IS_UPDATED', false);
                })
                .catch(error => {
                    console.log(error)
                })         
            }
        },
        clearMessage: (context)  => {
            context.commit('CLEAR_MESSAGE')
        },
    },
}