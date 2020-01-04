<template>
    <div class="row d-block">
        <div class="media p-1 m-1" @click="access(att, index)" v-for="(att, index) in input">
            <div class="file">
                <img src="/medias/file_white.svg" width="50" height="50" :alt="att.name">
                <label class="dl-progress" v-if="att.prs !== undefined">
                    <i class="fa fa-check" v-if="att.prs === 100"></i>
                    {{ att.prs === 100 ? '' : (att.prs + '%') }}
                </label>
            </div>
            <div class="d-block text-left file-description">
                <label class="w-100 m-0 content-ellipsis">{{ att.name }}</label>
                <small class="content-ellipsis">{{ toSize(att.size, 1) }}</small>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "attaches",

        data: function () {
            return {
                type: null,
                images: ['image/bmp', 'image/gif', 'image/jpeg', 'image/jpeg', 'image/png']
            }
        },

        props: ['input'],

        created: function () {
            this.type = this.medias();
        },

        methods: {
            access: function (dl, index) {
                let vm = this;
                axios.get("/" + dl.uri, {
                    responseType: 'arraybuffer',
                    onDownloadProgress: function (progressEvent) {
                        this.input[index].prs = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        this.$forceUpdate();
                    }.bind(vm)
                }).then(response => {
                    let blob = new Blob([response.data], {type: dl.mime});
                    let url = window.URL.createObjectURL(blob);
                    window.open(url);
                });
            },
            medias: function () {
                for (let i = 0; i < this.input.length; i++) {
                    if (this.exists(this.images, this.input[i].mime))
                        return false
                }
                return true;
            },
            exists: function (array, val) {
                for (let i = 0; i < array.length; i++)
                    if (array[i] === val) return true;
                return false;
            },
            toSize: function formatBytes(bytes, decimals = 0) {
                if (bytes === 0) return '0 Bytes';
                let k = 1024, dm = decimals <= 0 ? 0 : decimals || 2,
                    sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                    i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }
        }
    }
</script>

<style scoped>
    .file {
        background: #78c67f;
        border-radius: 50%;
        margin: 0 5px;
        position: relative;
    }

    .file-description {
        line-height: 16px;
        height: 50px !important;
        padding: 7px;
        overflow: hidden;
    }

    .send small {
        color: #669748;
    }

    .send label {
        color: #669748;
        font-weight: bold;
    }

    .media {
        cursor: pointer !important;
    }

    .dl-progress {
        position: absolute;
        background: rgba(0, 0, 0, 0.50);
        border-radius: 5px;
        color: white !important;
        font-size: 11px;
        padding: 1px 4px;
        top: 0;
        left: 0;
    }
</style>