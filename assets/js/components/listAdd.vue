<template>
<div>
    <h1 class="mt-5">Add List</h1>
    <span class="success_message" v-show="res.success">{{ res.success }}</span>
    <form action="http://127.0.0.1:8000/api/list" method="post" enctype="multipart/form-data" ref="listForm" id="listForm" v-on:submit.prevent="getData()">
        <div class="form-row mt-5">
            <div class="col-4">
                <input type="text" name="title" placeholder="Title" class="form-control" id="listTitle" ref="listTitle">
                <p class="validation-error" v-show="res.title">{{ res.title }}</p>
            </div>
            <div class="col-2">
                <input class="btn btn-outline-success" type="submit" value="Add">
            </div>
        </div>
    </form>
    <table class="table mt-3" v-if="res.list != NULL">
        <tr>
            <th>id</th>
            <th>title</th>
        </tr>
        <tr>
            <td>{{ res.list.id }}</td>
            <td class="font-weight-bold">{{ res.list.title }}</td>
        </tr>
    </table>
</div>
</template>

<script type="text/javascript">

import axios from 'axios'

export default {
    name: 'listAdd',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {}
        }
    },
    methods: {
        getData: function(){
            const form = document.getElementById('listForm');

            let formData = new FormData(form);

            axios({
                url: this.$refs.listForm.action,
                method: 'POST', 
                data: formData,
            })
            .then(res => {
                if(res.status === 200)
                {
                    this.res = res.data
                }
            })
            .catch(error => {
                if(error.response.status === 400)
                {
                    this.res = error.response.data
                }
                console.log(error.response);
            })

        }
    }
}
</script>

<style scoped>

</style>>