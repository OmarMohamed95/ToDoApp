<template>
  <div class="normal-message">
      <p>{{ message }}</p>
  </div>
</template>

<script>
export default {
    name: 'normalMessage',
    data: () => {
        return {
            
        }
    },
    computed: {
        message () {
            return this.$store.getters.getMessage;
        }
    }
}
</script>

<style>

.normal-message{
    position: fixed;
    bottom: 15px;
    right: 45px;
    background: #20212B;
    color: white;
    padding: 15px 20px 3px 20px;
    border-radius: 5px;
}

</style>