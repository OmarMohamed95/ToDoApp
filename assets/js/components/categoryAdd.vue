<template>
<div>
    <h1 class="mt-5">Add Category</h1>
    <span class="success_message" v-show="res.success">{{ res.success }}</span>
    <form action="http://symrest.test/api/category" method="post" enctype="multipart/form-data" ref="catForm" id="catForm" v-on:submit.prevent="getData()">
        <div class="mt-5 col-md-5">
            <div class="form-group mt-2 ml-1">
                <label name="name" class="label">name</label>
                <input type="text" name="name" class="form-control" id="name" ref="cat_name">
                <p class="validation-error" v-show="res.name_field">{{ res.name_field }}</p>
            </div>
            <div class="form-group mt-2 ml-1">
                <label name="image" class="label">image</label>
                <input type="file" name="image" class="form-control" id="image" ref="cat_image">
            </div>
            <div class="form-group mt-2 float-right">
                <input class="btn btn-outline-success ml-2" type="submit" value="Add">
            </div>
        </div>
    </form>
    <table class="table" v-if="res.category != NULL">
        <tr>
            <th>id</th>
            <th>name</th>
            <th>image</th>
        </tr>
        <tr>
            <td>{{ res.category.id }}</td>
            <td>{{ res.category.name }}</td>
            <td v-if="res.category.image != NULL"><img :src="uploads_base_url + '/category/' + res.category.image" alt="image" class="rounded float-left mr-2 imageStyle"></td>
            <td v-else><img :src="uploads_base_url + '/noImageAvailable.jpg'" alt="image" class="rounded float-left mr-2 imageStyle"></td>
        </tr>
    </table>
</div>
</template>

<script type="text/javascript">
export default {
    name: 'categoryAdd',
    props:['uploads_base_url'],
    data: function(){
        return{
            res: {}
        }
    },
    methods: {
        getData: function(){
            const form = document.getElementById('catForm');
            const name = document.getElementById('name');
            const image = document.getElementById('image');
            // const name = this.$refs.cat_name.value;
            // const image = this.$refs.cat_image.value;

            let formData = new FormData();
            formData.append('name', name.value);
            if(image.files[0])
            {
                formData.append('image', image.files[0]);
            }

            fetch(this.$refs.catForm.action, {
                method: 'POST', // or 'PUT'
                body: formData, // data can be `string` or {object}!
            })
            .then(response => response.json())
            .then(body => this.res = body)
            .catch(console.error)

            // for(let val of formData.values())
            // console.log(Array.from(formData.values()));
            
            // for(let val of formData.values())
            console.log(this.res);
        }
    },
    // mounted() {
    //     this.getData()  
    // },
}
</script>

<style scoped>

</style>>