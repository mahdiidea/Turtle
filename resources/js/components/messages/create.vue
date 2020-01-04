<template>
    <div class="create border-top" v-if="thread != null">
        <div class="w-100 px-2 py-1 d-flex attachments" style="background: #fafafa" v-if="formObject.attach.length">
            <div v-for="(att, index) in this.formObject.attach" :id="`preview-${index}`"
                 :style="'background-image: url(\'' + preview(att, `preview-${index}`) + '\');'"
                 class="p-1 attach img-thumbnail rounded">
                <div class="upload-progress" v-if="att.uploaded === true">
                    <i class="fa fa-check"></i>
                    complete
                </div>
                <div class="upload-progress" v-else>
                    <i class="fa fa-warning" v-if="att.progress === 'error'"></i>
                    <i class="fa fa-circle-o-notch fa-spin fa-fw" v-else></i>
                    {{ att.progress }}
                </div>
                <label class="content-ellipsis">{{ att.name }}</label>
                <i class="fa upload-remove fa-remove" @click="remove(index)"></i>
            </div>
            <div class="p-1 append img-thumbnail rounded" v-if="formObject.attach.length < 10" @click="attach">
                <i class="fa fa-plus-circle fa-5x"></i>
            </div>
        </div>
        <div class="input-group">
            <input type="file" class="d-none" id="attach" @change="upload" multiple>
            <input type="text" class="form-control rounded-0 border-0" v-model="formObject.body"
                   placeholder="Message ..." v-on:keypress.enter="create">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary border-0 rounded-0" type="button">
                    <i class="fa fa-microphone"></i>
                </button>
                <button class="btn btn-outline-secondary border-0 rounded-0" @click="attach"
                        v-if="formObject.attach.length < 10" type="button">
                    <i class="fa fa-paperclip"></i>
                </button>
                <button class="btn btn-outline-secondary border-0 rounded-0" @click="create" type="button">
                    <i class="fa fa-send"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "create",

        props: ['thread'],

        data: function () {
            return {
                formObject: {
                    body: "",
                    attach: []
                },
                images: ['image/bmp', 'image/gif', 'image/jpeg', 'image/jpeg', 'image/png']
            }
        },

        methods: {
            attach: function () {
                $('#attach').click();
            },
            upload: function (e) {
                if (this.formObject.attach.length >= 10) {
                    Vue.toasted.show("max attachment is 10 file").goAway(5000);
                    return;
                }
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length) return;
                for (let i = 0; i < files.length; i++) {
                    let ff = files[i];
                    ff["uploaded"] = false;
                    ff["progress"] = 0;
                    this.formObject.attach.push(ff);
                    this.queues(this.formObject.attach.length - 1, files[i]);
                }
            },
            remove: function (index) {
                this.formObject.attach.splice(index, 1);
            },
            queues: function (index, file) {
                let config = {
                    headers: {'Content-Type': 'multipart/form-data'},
                    onUploadProgress: function (progressEvent) {
                        this.formObject.attach[index]["progress"] = Math.round((progressEvent.loaded * 100) / progressEvent.total) + '%';
                        this.$forceUpdate();
                    }.bind(this)
                };

                let formData = new FormData();
                formData.append("attach", file);
                axios.post('/attaches', formData, config)
                    .then(({data}) => {
                        this.formObject.attach[index]['attaches'] = data;
                        this.formObject.attach[index]['uploaded'] = true;
                        this.formObject.attach[index]['progress'] = 'done';
                        this.$forceUpdate();
                    })
                    .catch(({response}) => {
                        this.formObject.attach[index]['uploaded'] = false;
                        this.formObject.attach[index]['progress'] = 'error';
                        this.$forceUpdate();
                        switch (response.status) {
                            case 413:
                                Vue.toasted.show("Payload Too Large").goAway(5000);
                                break;
                            case 422:
                                Vue.toasted.show(response.data[0]).goAway(5000);
                                break;
                            case 500:
                                Vue.toasted.show("Internal server error").goAway(5000);
                                break;
                            default:
                                Vue.toasted.show(response.message).goAway(5000);
                                break;
                        }
                    });
            },
            create: function () {
                axios.post(`/threads/${this.thread.id}/messages`, this.formObject)
                    .then(({data}) => {
                        this.formObject = {
                            body: "",
                            attach: []
                        };
                    })
                    .catch(({response}) => {
                        Vue.toasted.show(response.message).goAway(1500);
                    });
            },
            exists: function (array, val) {
                for (let i = 0; i < array.length; i++)
                    if (array[i] === val) return true;
                return false;
            },
            preview: function (src, element) {
                if (this.exists(this.images, src.type)) {
                    let fileReader = new FileReader();
                    fileReader.onload = function (e) {
                        $('#' + element).css("background-image", "url('" + this.result + "')");
                    };
                    fileReader.readAsDataURL(src);
                } else
                    return '/medias/file.svg';
            }
        }
    }
</script>

<style scoped>
    .create {
        /*width: calc(100% - 15px);*/
        width: 100%;
        position: absolute;
        left: 0;
        bottom: 0;
        background: white;
        z-index: 12;
    }

    .append {
        min-width: 160px;
        width: 160px;
        height: 160px;
        overflow: hidden;
        margin: 5px;
        position: relative;
        color: #ddd;
        border: 2px #ddd dashed;
        cursor: pointer;
    }

    .append i {
        position: absolute;
        margin: auto;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .upload-progress {
        border-radius: 5px;
        position: absolute;
        color: white;
        background: rgba(0, 0, 0, 0.50);
        left: 5px;
        top: 5px;
        margin: auto;
        padding: 0 5px;
    }

    .attachments {
        scroll-direction: horizontal;
        overflow-y: hidden;
        overflow-x: scroll;
    }

    .attachments::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0);
    }

    .attachments::-webkit-scrollbar {
        width: 5px;
        height: 10px;
        background-color: rgba(255, 255, 255, 0);
    }

    .attachments::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        background-color: rgba(0, 0, 0, 0.3);
    }

    .attach label {
        text-align: center;
        width: 100%;
        background: rgba(0, 0, 0, 0.50);
        color: white;
        left: 0;
        position: absolute;
        bottom: 0;
        margin: 0;
    }

    .upload-remove {
        width: 20px;
        height: 20px;
        color: white;
        text-align: center;
        position: absolute;
        padding-top: 2px;
        right: 4px;
        cursor: pointer;
        top: 4px;
        border-radius: 50px;
        background: rgba(0, 0, 0, 0.50);
    }

    .attach {
        min-width: 160px;
        width: 160px;
        height: 160px;
        overflow: hidden;
        margin: 5px;
        position: relative;
        background-repeat: no-repeat;
        background-position: center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>