<template>
<div>
    <h1 class="mt-5">Home</h1>
    <div class="mt-5">
        <p :key="k" v-for="(i,k) in data">{{ i }}</p>
    </div>
</div>
</template>

<script type="text/javascript">

import axios from 'axios'

export default {
    name: 'home',
    data: function(){
        return{
            data: {}
        }
    },
    methods: {
        getData: function(){
            axios.get('http://127.0.0.1:8000/api/test')
            .then(res => this.data = res.data)
            // console.log(this.data);
        }
    },
    created() {
        this.getData()  
    },
}
</script>

<style scoped>

</style>>