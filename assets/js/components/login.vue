<template>
<div>
    <h1 class="mt-5">Login</h1>
    <form action="http://127.0.0.1:8000/api/login_check" method="post" ref="loginForm" id="loginForm" v-on:submit.prevent="login()">
        <div class="mt-5">
            <div class="form-group">
                <input type="text" class="form-control" name='email' placeholder="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name='password' placeholder="password">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary col-md-12" value="login">
            </div>
        </div>  
    </form>  
    <div class="hr-theme-slash-2">
        <div class="hr-line"></div>
        <div class="hr-icon">or</div>
        <div class="hr-line"></div>
    </div>
    <a href="http://127.0.0.1:8000/login/google" class="btn btn-outline-primary col-md-12">Login with Google</a>
    <div class="mt-3">
        Don't have an account?
        <router-link to="/register">Register</router-link>
    </div>
    <div>
        <p class="logo"><img :src="uploads_base_url + '/brandIcon/favicon-32x32.png'" alt="ToDoApp">ToDoApp</p>
    </div>
</div>
</template>

<script type="text/javascript">

import axios from 'axios'

export default {
    name: 'login',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {},
        }
    },
    computed: {
        getAuthState() {
            return this.$store.getters.getAuth;
        }
    },
    methods: {
        login(){
            const form = document.getElementById('loginForm');

            let formData = new FormData(form);

            axios({
                url: this.$refs.loginForm.action,
                method: 'POST', 
                data: JSON.stringify(Object.fromEntries(formData)),
            })
            .then(res => {
                if(res)
                {
                    this.$router.push({name: 'task'});
                }
            })
            .catch(console.error)
        }
    },
    created() {
        
    },
}
</script>

<style scoped>

.success-string{
    color: lawngreen;
}

.hr-theme-slash-2 {
  display: flex;
}  

.hr-line {
    width: 100%;
    position: relative;
    margin: 15px;
    border-bottom: 1px solid #ddd;
}

.hr-icon {
    position: relative;
    top: 3px;
    color: gray;
}

/* ,
    watch: {
        getAuthState (currentState) {
            if(currentState === true)
            {
                this.$router.push({ name: 'task' })
            }
        }
    }, */

</style>