<template>
<div>
    <h1 class="mt-5">Tasks</h1>
    <div>
        <router-link to="/task/add" class="btn btn-outline-primary mb-3 offset-md-10">Add +</router-link>
        <hr>
        <div v-if="loading">
            <p class="text-center"><font-awesome-icon class="fa-pulse fa-3x" :icon="['fas', 'spinner']" /></p>
        </div>
        <div v-else-if="res.length" class="">
            <div :key="k" v-for="(i,k) in res" class="task-con mb-3">
                <div class="task-checkbox">
                    <input type="checkbox" name="is_done" :value="i.id">
                </div>
                <div class="task-data-con">
                    <p class="font-weight-bold task-title">{{ i.title }}</p>
                    <p class="">{{ i.note }}</p>
                    <p class="">{{ i.priority }}</p>
                    <p class="">{{ i.runAt }}</p>
                    <hr>
                    <!-- toLocaleString(): convert the date given to the Date object to the local timezone -->
                    <p class="">{{ new Date(i.createdAt).toLocaleString() }}</p>
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
            res: {},
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
                this.res = (response.status === 204) ? response.text() : response.data;
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