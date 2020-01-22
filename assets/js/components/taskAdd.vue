<template>
<div>
    <h1 class="mt-5">Add Task</h1>
    <span class="success_message" v-show="res.success">{{ res.success }}</span>
    <div v-if="loading">
        <p class="text-center"><font-awesome-icon class="fa-pulse fa-3x" :icon="['fas', 'spinner']" /></p>
    </div>
    <form v-else action="http://127.0.0.1:8000/api/task" method="post" ref="taskForm" id="taskForm" v-on:submit.prevent="addTask()">
        <div class="mt-5 col-md-10 task-add-con">
            <div class="form-group row mt-2">
                <label for="title" class="label col-md-2">title</label>
                <div class="col-md-10">
                    <input type="text" name="title" class="form-control" id="title" ref="task_title" placeholder="title">
                    <p class="validation-error" v-show="res.title">{{ res.title }}</p>
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="note" class="label col-md-2">note</label>
                <div class="col-md-10">
                    <input type="text" name="note" class="form-control" id="note" ref="task_note"  placeholder="note">
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="list" class="label col-md-2">list</label>
                <div class="col-md-10">
                    <select name="list" class="form-control" id="note" ref="task_note">
                        <option :key="k" v-for="(i, k) in lists" :value="i.id">{{ i.title }}</option>
                    </select>
                    <p class="validation-error" v-show="res.list">{{ res.list }}</p>
                </div>
            </div>
            <div class="form-group row mt-2 task-add-priority-con">
                <label for="priority" class="label col-md-2">priority</label>
                <div class="col-md-10">
                    <div class="form-check form-check-inline">
                        <input type="radio" value="1" name="priority" class="form-control" id="priority" ref="task_priority" checked>
                        <label class="form-check-label" for="exampleRadios1">low</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="2" name="priority" class="form-control" id="priority" ref="task_priority">
                        <label class="form-check-label" for="exampleRadios1">medium</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="3" name="priority" class="form-control" id="priority" ref="task_priority">
                        <label class="form-check-label" for="exampleRadios1">high</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="4" name="priority" class="form-control" id="priority" ref="task_priority">
                        <label class="form-check-label" for="exampleRadios1">very high</label>
                    </div>
                    <p class="validation-error" v-show="res.priority">{{ res.priority }}</p>
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="run_at" class="label col-md-2">run at</label>
                <div class="col-md-10">
                    <datetime type="datetime" name="run_at" id="run_at" ref="task_run_at" input-class="form-control" v-model="datetime12" use12-hour></datetime>
                    <p class="validation-error" v-show="res.run_at">{{ res.run_at }}</p>
                </div>
            </div>
            <div class="form-group mt-2">
                <input class="btn btn-outline-success col-md-10 offset-md-2" type="submit" value="Add">
            </div>
        </div>
    </form>
</div>
</template>

<script type="text/javascript">

import { Datetime } from 'vue-datetime';
import axios from 'axios'

export default {
    name: 'taskAdd',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {},
            lists: {},
            loading: true,
        }
    },
    components: {
        datetime: Datetime
    },
    methods: {
        addTask: function(){
            const form = document.getElementById('taskForm');

            let formData = new FormData(form);

            axios({
                url: this.$refs.taskForm.action,
                method: 'POST', 
                data: formData,
            })
            .then(res => {
                if(res.status === 200)
                {
                    this.res = res.data
                    this.$store.dispatch("pushNotification");
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    this.res = error.response.data;
                }
                console.log(error);
            })

        },
        getAllLists: function(){
            
            axios.get('http://127.0.0.1:8000/api/list')
            .then(res => {
                this.loading = false;
                this.lists = res.data;
            })
            .catch(console.error)
        }
    },
    created() {
        this.getAllLists()  
    },
}
</script>

<style scoped>

.form-check-inline{
    margin-right: 30px;
}

</style>>