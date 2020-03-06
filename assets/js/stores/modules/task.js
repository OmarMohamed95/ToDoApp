import Vue from "vue";
import axios from "axios";

export default {
    state: {
        taskAdd: {
            loading: true,
            message: {},
        },
        taskEdit: {
            task: {},
            loading: true,
            message: {},
        },
        allTasks: [],
        tasks: {
            loading: true,
            pageNumber: 1,
        },
        currentTask: {
            id: Number,
            isUpdated: false
        },    
        lists: {},
        isEditing: false,
    },
    getters: {
        getTasksLoading: state => {
            return state.tasks.loading
        },
        getTaskAddLoading: state => {
            return state.taskAdd.loading
        },
        getTaskEditLoading: state => {
            return state.taskEdit.loading
        },
        getTaskAddMessage: state => {
            return state.taskAdd.message
        },
        getTaskEditMessage: state => {
            return state.taskEdit.message
        },
        getIsEditing: state => {
            return state.isEditing
        },
        getTaskId: state => {
            return state.currentTask.id
        },
        getLists: state => {
            return state.lists
        },
        getEditTask: state => {
            return state.taskEdit.task
        },
        getAllTasks: state => {
            return state.allTasks
        },
    },
    mutations: {
        SET_TASKS_LOADING: (state, value) => {
            state.tasks.loading = value
        },
        SET_TASK_ADD_LOADING: (state, value) => {
            state.taskAdd.loading = value
        },
        SET_TASK_EDIT_LOADING: (state, value) => {
            state.taskEdit.loading = value
        },
        SET_EDIT_TASK: (state, res) => {
            state.taskEdit.task = res
        },
        SET_TASKS: (state, tasks) => {
            // Vue.set(state.tasks, 'tasks', value)
            for (let task of tasks) {
                state.allTasks.push(task);
            }
            // console.log(typeof state.tasks.allTasks);
        },
        SET_TASK_ADD_MESSAGE: (state, message) => {
            state.taskAdd.message = message
        },
        SET_TASK_EDIT_MESSAGE: (state, message) => {
            state.taskEdit.message = message
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
        CLEAR_TASK_EDIT_MESSAGE: state => {
            state.taskEdit.message = {}
        },
        CLEAR_TASK_ADD_MESSAGE: state => {
            state.taskAdd.message = {}
        },
        UPGRADE_PAGE: state => {
            state.tasks.pageNumber++
        },
        RESET_PAGE: state => {
            state.tasks.pageNumber = 1
        },
        CLEAR_TASKS: state => {
            state.allTasks = []
        },
    },
    actions: {
        getTasks: (context, payload) => {
            let pageNum = context.state.tasks.pageNumber;
            axios.get('http://127.0.0.1:8000/api/tasks', {
                params: {
                    page: pageNum,
                    sort: payload.sort,
                    order: payload.order,
                }
            })
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
                    context.commit('SET_TASK_ADD_MESSAGE', data)
                    context.dispatch("pushNotification");
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    let data = error.response.data;
                    context.commit('SET_TASK_ADD_MESSAGE', data)
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
                    context.commit('SET_TASK_EDIT_MESSAGE', data)
                    context.commit('UPDATE_IS_EDITING', false)
                    context.commit('SET_IS_UPDATED', true)
                    context.dispatch("getTasks")
                    context.dispatch("pushNotification")
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    let data = error.response.data;
                    context.commit('SET_TASK_EDIT_MESSAGE', data)
                }
                console.log(error.response);
            })

        },
        getAllLists: context => {
            return axios.get('http://127.0.0.1:8000/api/list')
            .then(res => {
                let listsData = res.data;
                context.commit('SET_LISTS', listsData);
            })
            .catch(console.error)
        },
        updateIsEditing: (context, value)  => {            
            context.commit('UPDATE_IS_EDITING', value);
        },
        setTaskId: (context, id)  => {
            context.commit("SET_TASK_ID", id)   
        },
        setTasksLoading: (context, value)  => {
            context.commit("SET_TASKS_LOADING", value)
        },
        setTaskEditLoading: (context, value)  => {
            context.commit("SET_TASK_EDIT_LOADING", value)
        },
        setTaskAddLoading: (context, value)  => {
            context.commit("SET_TASK_ADD_LOADING", value)
        },
        getTaskById: (context)  => {
            if(context.state.currentTask.id !== context.state.taskEdit.task.id || context.state.currentTask.isUpdated === true)
            {
                return axios({
                    url: `http://127.0.0.1:8000/api/task/${context.state.currentTask.id}`,
                    method: 'GET',
                })
                .then(res => {
                    context.commit('SET_EDIT_TASK', res.data);
                    context.commit('SET_IS_UPDATED', false);
                })
                .catch(error => {
                    console.log(error)
                })         
            }
        },
        clearTaskAddMessage: context => {
            context.commit('CLEAR_TASK_ADD_MESSAGE')
        },
        clearTaskEditMessage: context => {
            context.commit('CLEAR_TASK_EDIT_MESSAGE')
        },
        upgradePage: context => {
            context.commit('UPGRADE_PAGE')
        },
        resetPageToOne: context => {
            context.commit('RESET_PAGE')
        },
        clearTasks: context => {
            context.commit('CLEAR_TASKS')
        },
    },
}