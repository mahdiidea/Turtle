<template>
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2">
            <div class="card">
                <div class="card-body p-0">
                    <textarea class="form-control border-0" rows="3" v-model="formObject.content"
                              placeholder="whats happen ?"></textarea>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-link" @click="trigger">
                        <i class="fa fa-picture-o"></i>
                    </button>
                    <input id="picture" class="d-none" type="file" @change="upload">
                    <button class="btn btn-sm float-right btn-secondary" @click="create">
                        <i class="fa fa-circle-o-notch fa-spin fa-fw" v-if="status === 'sending'"></i>
                        <i class="fa fa-send" v-else></i>
                        Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "create",

        data: function () {
            return {
                formObject: {
                    content: "",
                    picture: null
                },
                status: null,

            }
        },

        methods: {
            create: function () {
                this.status = 'sending';

                let config = {
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: function (progressEvent) {
                        this.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        this.$forceUpdate();
                    }.bind(this)
                };

                let form = new FormData();

                if (this.formObject.picture != null)
                    form.append("picture", this.formObject.picture);
                form.append("body", this.formObject.content);

                axios.post('posts', form, config)
                    .then(({data}) => {
                        this.status = 'sent';
                        this.formObject = {
                            content: "",
                            picture: null
                        };
                    })
                    .catch(({response}) => {
                        this.status = 'failed';
                        Vue.toasted.show(response.data[0]).goAway(1500);
                    })

            },
            upload: function (e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) return;
                this.formObject.picture = files[0];
            },
            trigger: function () {
                $('#picture').click()
            }
        }
    }
</script>

<style scoped>

</style>