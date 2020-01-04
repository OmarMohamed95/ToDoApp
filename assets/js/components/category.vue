<template>
<div>
    <h1 class="mt-5">Categories</h1>
    <div class="mt-5">
        <router-link to="/category/add" class="btn btn-outline-primary mb-3 offset-md-11">Add +</router-link>
        <hr>
        <div :key="k" v-for="(i,k) in data" class="con mb-3">
            <p class="inline-display">{{ i.name }}</p>
            <img v-if="i.image != NULL" :src="uploads_base_url + '/category/' + i.image" alt="image" class="rounded float-left mr-2 imageStyle">
            <img v-else :src="uploads_base_url + '/noImageAvailable.jpg'" alt="image" class="rounded float-left mr-2 imageStyle">
        </div>
    </div>  
</div>
</template>

<script type="text/javascript">
export default {
    name: 'category',
    props:['uploads_base_url'],
    data: function(){
        return{
            data: {}
        }
    },
    methods: {
        getData: function(){
            fetch('http://127.0.0.1:8000/api/category')
            .then(response => response.json())
            .then(body => this.data = body)
            console.log(JSON.stringify(this.data));

        }
    },
    created() {
        this.getData()  
    },
}
</script>

<style scoped>

</style>>