<template>
<div>
    <h1 class="mt-5">Add Task</h1>
    <span class="success_message" v-show="success.success">{{ success.success }}</span>
    <form action="http://127.0.0.1:8000/api/task" method="post" ref="taskForm" id="taskForm" v-on:submit.prevent="addTask()">
        <div class="mt-5 col-md-5">
            <div class="form-group mt-2 ml-1">
                <label for="title" class="label">title</label>
                <input type="text" name="title" class="form-control" id="title" ref="task_title">
                <p class="validation-error" v-show="success.title_field">{{ success.title_field }}</p>
            </div>
            <div class="form-group mt-2 ml-1">
                <label for="note" class="label">note</label>
                <input type="text" name="note" class="form-control" id="note" ref="task_note">
                <p class="validation-error" v-show="success.note_field">{{ success.note_field }}</p>
            </div>
            <div class="form-group mt-2 ml-1">
                <label for="list" class="label">list</label>
                <select name="list" class="form-control" id="note" ref="task_note">
                    <option :key="k" v-for="(i, k) in lists" :value="i.id">{{ i.title }}</option>
                </select>
            </div>
            <div class="form-group mt-2 ml-1">
                <label for="priority" class="label">priority</label>
                <input type="radio" value="4" name="priority" class="form-control" id="priority" ref="task_priority">very high
                <input type="radio" value="3" name="priority" class="form-control" id="priority" ref="task_priority">high
                <input type="radio" value="2" name="priority" class="form-control" id="priority" ref="task_priority">medium
                <input type="radio" value="1" name="priority" class="form-control" id="priority" ref="task_priority">low
            </div>
            <div class="form-group mt-2 ml-1">
                <label for="run_at" class="label">run at</label>
                <datetime type="datetime" name="run_at" id="run_at" ref="task_run_at" v-model="datetime12" use12-hour></datetime>
                <p class="validation-error" v-show="success.run_at_field">{{ success.run_at_field }}</p>
            </div>
            <div class="form-group mt-2 float-right">
                <input class="btn btn-outline-success ml-2" type="submit" value="Add">
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
            success: {},
            lists: {},
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
                this.success = res.data
                this.$store.dispatch("pushNotification");
            })
            .catch(console.error)

        },
        getAllLists: function(){
            
            axios.get('http://127.0.0.1:8000/api/list')
            .then(res => this.lists = res.data)
            .catch(console.error)
        }
    },
    created() {
        this.getAllLists()  
    },
}
</script>

<style scoped>

</style>>