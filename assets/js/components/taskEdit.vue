<template>
<div class="task-edit col-md-8 offset-md-3">
    <h1 class="mt-5 task-edit-head">Edit Task</h1>
    <font-awesome-icon @click="close" class="icon-pointer float-right fa-2x" :icon="['fas', 'times']" />
    <span class="success_message" v-show="message.success">{{ message.success }}</span>
    <loading v-if="loading"></loading>
    <form v-else :action="`http://127.0.0.1:8000/api/task/${id}`" method="put" ref="taskForm" id="taskForm" v-on:submit.prevent="editTask()">
        <div class="mt-5 col-md-12 task-add-con">
            <div class="form-group row mt-2">
                <label for="title" class="label col-md-2">title</label>
                <div class="col-md-10">
                    <input type="text" name="title" class="form-control" id="title" ref="task_title" placeholder="title" :value="res.title">
                    <p class="validation-error" v-show="message.title">{{ message.title }}</p>
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="note" class="label col-md-2">note</label>
                <div class="col-md-10">
                    <input type="text" name="note" class="form-control" id="note" ref="task_note"  placeholder="note" :value="res.note">
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="list" class="label col-md-2">list</label>
                <div class="col-md-10">
                    <select name="list" class="form-control" id="note" ref="task_note">
                        <option :selected="res.list.id === i.id ? 'selected' : ''" :key="k" v-for="(i, k) in lists" :value="i.id">{{ i.title }}</option>
                    </select>
                    <p class="validation-error" v-show="message.list">{{ message.list }}</p>
                </div>
            </div>
            <div class="form-group row mt-2 task-add-priority-con">
                <label for="priority" class="label col-md-2">priority</label>
                <div class="col-md-10">
                    <div class="form-check form-check-inline">
                        <input type="radio" value="1" name="priority" class="form-control" id="priority" ref="task_priority" :checked="res.priority === 1 ? 'checked' : ''">
                        <label class="form-check-label" for="exampleRadios1">low</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="2" name="priority" class="form-control" id="priority" ref="task_priority" :checked="res.priority === 2 ? 'checked' : ''">
                        <label class="form-check-label" for="exampleRadios1">medium</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="3" name="priority" class="form-control" id="priority" ref="task_priority" :checked="res.priority === 3 ? 'checked' : ''">
                        <label class="form-check-label" for="exampleRadios1">high</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" value="4" name="priority" class="form-control" id="priority" ref="task_priority" :checked="res.priority === 4 ? 'checked' : ''">
                        <label class="form-check-label" for="exampleRadios1">very high</label>
                    </div>
                    <p class="validation-error" v-show="message.priority">{{ message.priority }}</p>
                </div>
            </div>
            <div class="form-group row mt-2">
                <label for="run_at" class="label col-md-2">run at</label>
                <div class="col-md-10">
                    <datetime type="datetime" name="run_at" id="run_at" ref="task_run_at" input-class="form-control" v-model="datetime12" use12-hour></datetime>
                    <p class="validation-error" v-show="message.run_at">{{ message.run_at }}</p>
                </div>
            </div>
            <input type="hidden" name="_method" value="PUT"/>
            <div class="form-group mt-2">
                <input class="btn btn-outline-success col-md-10 offset-md-2" type="submit" value="Edit">
            </div>
        </div>
    </form>
</div>
</template>

<script type="text/javascript">

import loading from './loading';
import axios from 'axios'
import { Datetime } from 'vue-datetime';

export default {
    name: 'taskAdd',
    props:['uploads_base_url'],
    data: function(){
        return{

        }
    },
    computed: {
        loading(){
            return this.$store.getters.getLoading
        },
        res(){
            return this.$store.getters.getRes
        },
        message(){
            return this.$store.getters.message
        },
        lists(){
            return this.$store.getters.getLists
        },
        id(){
            return this.$store.getters.getTaskId
        },
    },
    methods: {
        getAllLists: function(){
            this.$store.dispatch('getAllLists');
        },
        editTask: function(){
            const form = document.getElementById('taskForm');

            let formData = new FormData(form);

            this.$store.dispatch('editTask', formData);

        },
        close: function(){
            this.$store.dispatch('updateIsEditing', false);
        },
    },
    components: {
        datetime: Datetime,
        loading: loading,
    },
    created() {
        this.getAllLists();
        this.$store.dispatch('clearMessage');
    },
}
</script>

<style scoped>

.form-check-inline{
    margin-right: 30px;
}

.task-edit{
    background-color: white;
    padding: 40px 70px 25px 60px;
    margin-top: 25px;
    border-radius: 20px;
    z-index: 200;
}

.task-edit-head{
    display: inline;
}

.icon-pointer{
    cursor: pointer;
}

</style>>