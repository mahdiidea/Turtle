<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 my-2" v-for="obj in posts">
                <contents-object v-bind:object="obj"></contents-object>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "index",

        data: function () {
            return {
                posts: []
            }
        },

        created: function () {
            this.fetch();
        },

        methods: {
            fetch: function () {
                axios.get('posts')
                    .then(({data}) => this.posts = data.data);
            }
        }
    }
</script>

<style scoped>

</style>