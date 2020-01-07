<template>
<div>
    <h1 class="mt-5">Tasks</h1>
    <div>
        <router-link to="/task/add" class="btn btn-outline-primary mb-3 offset-md-10">Add +</router-link>
        <hr>
        <div v-if="loading">
            <p class="text-center"><font-awesome-icon class="fa-pulse fa-3x" :icon="['fas', 'spinner']" /></p>
        </div>
        <div v-else-if="tasks.length" class="">
            <div :key="k" v-for="(task,k) in tasks" class="task-con mb-3">
                <div class="task-checkbox">
                    <input type="checkbox" name="is_done" :value="task.id">
                </div>
                <div class="task-data-con">
                    <p v-if="task.priority === 4" class="priority alert alert-danger">VERY HIGH</p>
                    <p v-if="task.priority === 3" class="priority alert alert-warning">HIGH</p>
                    <p v-if="task.priority === 2" class="priority alert alert-success">MEDIUM</p>
                    <p v-if="task.priority === 1" class="priority alert alert-primary">NORMAL</p>
                    <p class="font-weight-bold task-title">{{ task.title }}</p>
                    <p class="note">{{ task.note }}</p>
                    <hr>
                    <p class="list-title">{{ task.list.title }}</p>
                    <p class="run-at"><font-awesome-icon class="fa-lg" :icon="['fas', 'clock']" /> {{ new Date(task.runAt).toDateString() + ', ' + new Date(task.runAt).toLocaleTimeString() }}</p>
                    <!-- toLocaleString(): convert the date given to the Date object to the local timezone -->
                    <!-- <p class="">{{ new Date(task.createdAt).toLocaleString() }}</p> -->
                </div>
            </div>
        </div>
        <div v-else>
            <p class="alert alert-danger text-center">No tasks found!</p>
        </div>
    </div>  
</div>
</template>

<script type="text/javascript">

import axios from 'axios'

export default {
    name: 'task',
    props:['uploads_base_url'],
    data: function(){
        return{
            tasks: {},
            loading: true,
        }
    },
    methods: {
        getData: function(){
            let ParsedResponse;

            axios.get('http://127.0.0.1:8000/api/task')
            .then((response) => {
                this.loading = false;
                //check if this working fine when the status is 204
                this.tasks = (response.status === 204) ? response.text() : response.data;
            })
            .catch((error) => {
                throw error;
            });
        }
    },
    created() {
        this.getData()  
    },
}
</script>

<style scoped>

</style>>