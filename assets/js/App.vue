<template>
<div>
    <div v-if="getAuthState" class="row">
        <sidebar :uploads_base_url="uploads_base_url" class="col-md-3"></sidebar>
        <div class="body-con col-md-8 offset-md-3">
            <router-view :uploads_base_url="uploads_base_url" :key="$route.fullPath"></router-view>
        </div>
    </div> 
    <div v-else>
        <div class="row">
            <router-view :uploads_base_url="uploads_base_url" :key="$route.fullPath" class="no-sidebar-body-con offset-md-4 col-md-4"></router-view>
        </div>
    </div>
    <normal-message v-if="showDeniedMessage"></normal-message>
</div>
</template>

<script>
import sidebar from './components/sidebar.vue'
import normalMessage from './components/message/normalMessage'

export default {
    name: 'app',
    data() {
        return{
            'uploads_base_url': this.$uploads_base_url,
        }
    },
    computed: {
        getAuthState() {
            return this.$store.getters.getAuth;
        },
        showDeniedMessage() {
            return this.$store.getters.showDeniedMessage;
        }
    },
    components: {
        'sidebar': sidebar,
        'normal-message': normalMessage,
    },
    created() {
        
    },
}
</script>

<style scoped>

</style>>