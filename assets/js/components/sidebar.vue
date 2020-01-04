<template>
<div class="">
    <div class="sidebar">
        <div class="sidebar-account">
            <img :src="uploads_base_url + '/images.jpe'" alt="image">
            <div class="sidebar-profile-links">
                <router-link to="/" class="font-weight-bolder">{{ user.username }}</router-link>
                <a href="http://127.0.0.1:8000/api/logout" v-on:click.stop.prevent="logout()">Logout</a>
            </div>
        </div>
        <ul class="sidebar-menu">        
            <li><router-link to="/test"><font-awesome-icon class="sidebar-icon fa-lg" :icon="['fas', 'home']" />Home</router-link></li>
            <li><router-link to="/list"><font-awesome-icon class="sidebar-icon fa-lg" :icon="['fas', 'list']" />Lists</router-link></li>
            <li><router-link to="/task"><font-awesome-icon class="sidebar-icon fa-lg" :icon="['fas', 'tasks']" />Tasks</router-link></li>
        </ul>
        <div class="mt-3 sidebar-notification">
            <div v-if="appPermission">
                Notification <button class="btn btn-primary" @click="toggleNotificationBtn" id="notificationBtn">Active</button>
            </div>
            <div v-else>
                Notification <button class="btn btn-secondary" @click="toggleNotificationBtn" id="notificationBtn">Inactive</button>
            </div>
        </div>
        <div>
            <p class="sidebar-logo"><img :src="uploads_base_url + '/brandIcon/favicon-32x32.png'" alt="ToDoApp">ToDoApp</p>
        </div>
    </div>
</div>
</template>

<script type="text/javascript">

export default {
    name: 'sidebar',
    props:['uploads_base_url'],
    data: function(){
        return{
            user: {},
        }
    },
    computed: {
        appPermission() {
            return this.$store.getters.getAppPermission;
        },
        browserPermission() {
            return this.$store.getters.getBrowserPermission;
        },
    },
    methods: {
       logout() {
           this.$store.dispatch('logout')
           .then(res => {
               if(res.status === 200 && res.data.message === 'logged out successfully')
               {
                   this.$router.push({name: 'login'})
               }
           })
           .catch(error => {
               console.log(error);
           });
       },
       getCurrentUser() {
           this.$store.dispatch('getCurrentUser')
           .then(res => {
                this.user = res.data;
           })
           .catch(error => {
               console.log(error);
           });
       },
       async toggleNotificationBtn() {
            let notificationBtn = document.getElementById('notificationBtn');
            
            if(!this.browserPermission)
            {
                this.$store.dispatch('showDeniedPermissionMessage');
                setTimeout(() => {
                    this.$store.dispatch('hideDeniedPermissionMessage');
                }, 5000)
            }
            else
            {
                if(notificationBtn.innerHTML === 'Active')
                {
                    notificationBtn.innerHTML =  'Inactive'
                    this.$store.dispatch('updateAppPermissionCookie', false)

                }
                else if(notificationBtn.innerHTML === 'Inactive')
                {
                    notificationBtn.innerHTML =  'Active';
                    await this.$store.dispatch('updateAppPermissionCookie', true);
                    this.$store.dispatch('pushNotification');
                }
    
                notificationBtn.classList.toggle('btn-primary');
                notificationBtn.classList.toggle('btn-secondary');
            }


       },
    },
    created() {
        this.getCurrentUser();
    },
}
</script>

<style scoped>

</style>>