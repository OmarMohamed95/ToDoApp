<template>
<div>
    <h1 class="mt-5">Tasks</h1>
    <div>
        <select name="sort" class="sort-droplist mt-5 col-md-4" v-model="sortKey" @change="sortLink($event)">
            <option value hidden>SORT BY</option>
            <option value="run_at/desc">RUN AT: NEW TO OLD</option>
            <option value="run_at/asc">RUN AT: OLD TO NEW</option>
            <option value="priority/desc">PRIORITY: HIGH TO LOW</option>
            <option value="priority/asc">PRIORITY: LOW TO HIGH</option>
        </select>
        <router-link to="/task/add" class="btn btn-outline-primary col-md-2 offset-md-5" style="border-radius: 20px">Add +</router-link>
        <hr>
        <div v-show="tasks.length" class="" id="all-tasks">
            <div :key="k" v-for="(task,k) in tasks">
                <div v-if="$route.params.sort === 'priority'">
                    <div v-if="k === 0">
                        <p v-if="task.priority === 4" class="tasks-group-title mt-5">VERY HIGH</p>
                        <p v-if="task.priority === 3" class="tasks-group-title mt-5">HIGH</p>
                        <p v-if="task.priority === 2" class="tasks-group-title mt-5">MEDIUM</p>
                        <p v-if="task.priority === 1" class="tasks-group-title mt-5">Low</p>
                    </div>
                    <div v-else-if="k > 0">
                        <div v-if="tasks[k-1].priority != task.priority">
                            <p v-if="task.priority === 4" class="tasks-group-title mt-5">VERY HIGH</p>
                            <p v-if="task.priority === 3" class="tasks-group-title mt-5">HIGH</p>
                            <p v-if="task.priority === 2" class="tasks-group-title mt-5">MEDIUM</p>
                            <p v-if="task.priority === 1" class="tasks-group-title mt-5">Low</p>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div v-if="k === 0">
                        <p class="tasks-group-title mt-5">{{ new Date(task.runAt).toDateString() }}</p>
                    </div>
                    <div v-else-if="k > 0">
                        <p class="tasks-group-title mt-5" v-if="new Date(tasks[k-1].runAt).toDateString() != new Date(task.runAt).toDateString()">{{ new Date(task.runAt).toDateString() }}</p>
                    </div>
                </div>
                <div class="task-con mb-3">
                    <div class="task-checkbox">
                        <font-awesome-icon class="icon-blue-pointer fa-lg" :icon="['fas', 'check-circle']" />
                    </div>
                    <div class="task-data-con">
                        <font-awesome-icon v-on:click="showEdit(task.id)" class="icon-blue-pointer float-right fa-lg" :icon="['fas', 'pen']" />
                        <p class="font-weight-bold task-title">{{ task.title }}</p>
                        <p class="note">{{ task.note }}</p>
                        <hr>
                        <p v-if="task.priority === 4" class="priority">VERY HIGH</p>
                        <p v-if="task.priority === 3" class="priority">HIGH</p>
                        <p v-if="task.priority === 2" class="priority">MEDIUM</p>
                        <p v-if="task.priority === 1" class="priority">Low</p>
                        <p class="list-title"><font-awesome-icon class="sidebar-icon fa-x1" :icon="['fas', 'list']" />{{ task.list.title }}</p>
                        <p class="run-at ml-3"><font-awesome-icon class="fa-lg" :icon="['fas', 'clock']" /> {{ new Date(task.runAt).toDateString() + ', ' + new Date(task.runAt).toLocaleTimeString() }}</p>
                        <!-- toLocaleString(): convert the date given to the Date object to the local timezone -->
                        <!-- <p class="">{{ new Date(task.createdAt).toLocaleString() }}</p> -->
                    </div>
                </div>                    
            </div>
            <loading v-if="loading"></loading>
            <div>
                <button @click="upgradePage" class="btn btn-outline-primary mt-3 mb-5 col-md-12">Show More</button>
            </div>
        </div>
        <div v-if="tasks.length === 0">
            <p class="alert alert-danger text-center">No tasks found!</p>
        </div>
    </div>  
    <div v-if="isEditing" @click="close($event)" id="transparent-background" class="transparent-background">
        <task-edit></task-edit>
    </div>
</div>
</template>

<script type="text/javascript">

import axios from 'axios'
import loading from './loading'
import taskEdit from './taskEdit'

export default {
    name: 'task',
    props:['uploads_base_url'],
    data: function(){
        return{
            sortKey: "",
            tasksSortBy: ['run_at', 'priority'],
            tasksOrder: ['desc', 'asc'],
            defaultSort: 'run_at',
            defaultOrder: 'desc',
        }
    },
    components: {
        loading: loading,
        'task-edit': taskEdit,
    },
    methods: {
        getTasks: function() {
            this.$store.dispatch('setTasksLoading', true)

            let sort = this.$route.params.sort;
            let order = this.$route.params.order;

            if(this.tasksSortBy.includes(sort) && this.tasksOrder.includes(order))
            {
                this.$store.dispatch('getTasks', {sort, order})
            }
            else
            {
                sessionStorage.removeItem('tasks-sorting-id')
                this.$store.dispatch('getTasks', {sort: this.defaultSort, order: this.defaultOrder})
            }
        },
        sortLink: function (event) {
            this.$store.dispatch('resetPageToOne');
            this.$store.dispatch('clearTasks');
            this.setSelectedInSession();
            this.$router.push(`/task/${event.target.value}`)
        },
        setSelectedInSession: function(){
            sessionStorage.setItem('tasks-sorting-id', event.target.value)
        },
        selectOption: function () {
            let value = sessionStorage.getItem('tasks-sorting-id');
            document.querySelector(`[value='${value}']`).selected = true;
        },
        disableOption: function () {
            let value = sessionStorage.getItem('tasks-sorting-id');
            document.querySelector(`[value='${value}']`).disabled = true;
        },
        showEdit: function (id) {
            this.$store.dispatch("setTaskId", id)
            this.$store.dispatch("updateIsEditing", true)
        },
        close: function (event) {
            if(event.target.id === 'transparent-background')
            {
                this.$store.dispatch("updateIsEditing", false)
            }
        },
        upgradePage: function () {
            let sort = this.$route.params.sort;
            let order = this.$route.params.order;

            this.$store.dispatch('setTasksLoading', true);
            this.$store.dispatch("upgradePage")
            this.$store.dispatch('getTasks', {sort: sort, order: order})
        }
    },
    computed: {
        tasks(){
            return this.$store.getters.getAllTasks
        },
        isEditing(){
            return this.$store.getters.getIsEditing
        },
        loading(){
            return this.$store.getters.getTasksLoading
        },
    },
    created() {
        this.getTasks();
    },
    updated() {
        this.selectOption();
        this.disableOption();
    }
}
</script>

<style scoped>

.icon-blue-pointer{
    color: #007bff;
    cursor: pointer;
}

.icon-blue-pointer:hover{
    color: #0056b3;
    cursor: pointer;
}

.transparent-background{
    background-color: rgba(0,0,0,0.5);
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    z-index: 100;
}

</style>