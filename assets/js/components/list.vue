<template>
<div>
    <h1 class="mt-5">Lists</h1>
    <div>
        <router-link to="/list/add" class="btn btn-outline-primary mb-3 offset-md-10">Add +</router-link>
        <hr>
        <loading v-if="loading"></loading>
        <div v-else-if="res.length" class="row offset-md-2">
            <div :key="k" v-for="(i,k) in res" class="col-md-3 con mb-3 ml-2">
                <p class="font-weight-bold text-center">{{ i.title }}</p>
            </div>
        </div>
        <div v-else>
            <p class="alert alert-danger text-center">No lists found!</p>
        </div>
    </div>  
</div>
</template>

<script type="text/javascript">

import axios from 'axios'
import loading from './loading'

export default {
    name: 'list',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {},
            loading: true,
        }
    },
    components: {
        loading: loading
    },
    methods: {
        getData: function(){
            let ParsedResponse;

            axios.get('http://127.0.0.1:8000/api/list')
            .then((response) => {
                this.loading = false;
                //check if this working fine when the status is 204
                this.res = (response.status === 204) ? response.text() : response.data;

            })
            .catch((error) => {
                // throw error;
                console.log(error);
            });
        }
    },
    created () {
        this.getData()  
    },
}
</script>

<style scoped>

</style>>