<template>
<div>
    <h1 class="mt-5">Register</h1>
    <p class="success-string"><strong>{{ res.message }}</strong></p>
    <form action="http://127.0.0.1:8000/api/register" method="post" ref="registerForm" id="registerForm" v-on:submit.prevent="register()" enctype="multipart/form-data">
        <div class="mt-5">
            <div class="form-group">
                <input type="text" class="form-control" name='username' placeholder="username">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name='email' placeholder="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name='password' placeholder="password">
            </div>
            <div class="form-group">
                <input type="file" name='image'>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-outline-primary col-md-12" value="register">
            </div>
        </div>  
    </form>  
    <div class="mt-3">
        Already have an account?
        <router-link to="/login">Login</router-link>
    </div>
    <div>
        <p class="logo"><img :src="uploads_base_url + '/brandIcon/favicon-32x32.png'" alt="ToDoApp">ToDoApp</p>
    </div>
</div>
</template>

<script type="text/javascript">

import axios from 'axios'

export default {
    name: 'register',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {},
        }
    },
    methods: {
        register(){
            const form = document.getElementById('registerForm');

            let formData = new FormData(form);
            
            axios({
                url: this.$refs.registerForm.action,
                method: 'POST', 
                data: formData,
            })
            .then(res => {
                if(res.status === 200)
                {
                    this.$router.push({name: 'task'})
                }
            })
            .catch(console.error)
        },
        
    },
    created() {

    },
}
</script>

<style scoped>

.success-string{
    color: lawngreen;
}

</style>>