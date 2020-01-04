<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-white p-0">
                <div class="row">
                    <div class="col-4 pr-0 border-right">
                        <div class="input-group h-100">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary border-0 bg-white text-dark rounded-0"
                                        @click="toggleMenu" type="button">
                                    <i id="toggleIcon" class="fa fa-bars"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control rounded-0 border-0 h-100" v-model="threadQuery"
                                   placeholder="Search">
                        </div>
                    </div>
                    <div :class="thread == null ? 'col-8 p-2 chat-head invisible' : 'chat-head col-8 p-2'">
                        <h5 class="m-0">{{ thread == null ? "" : thread.name }}</h5>
                        <span>
                            {{
                            (thread != null && thread.last_seen != null)
                            ? fromNow(thread.last_seen)
                            : "last seen recently"
                            }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 messenger-body">
                <div class="row h-100">
                    <div id="scroll-2" class="col-4 pr-0 border-right h-100">
                        <div id="toggleMenu" class="position-absolute menu" style="display: none">
                            <div class="list-group">
                                <button type="button" @click="toggleGroup"
                                        class="list-group-item list-group-item-action rounded-0">
                                    <i class="fa fa-group"></i> New group
                                </button>
                                <button type="button" class="list-group-item list-group-item-action"
                                        @click="toggleContact">
                                    <i class="fa fa-user"></i> Contacts
                                </button>
                                <button type="button" class="list-group-item list-group-item-action">
                                    <i class="fa fa-cog"></i> Settings
                                </button>
                            </div>
                        </div>
                        <threads v-on:selectThread="thread=$event" :thread="thread" :auth_id="auth_id"
                                 :search="threadQuery"></threads>
                        <div class="thread-dialog-background" @click="toggleMenu"></div>
                    </div>
                    <div class="col-8 pl-0 h-100">
                        <messages :auth_id="auth_id" :thread="thread"></messages>
                    </div>
                </div>
            </div>
        </div>
        <contacts v-on:selectThread="thread=$event"></contacts>
        <create-group v-on:selectThread="thread=$event"></create-group>
    </div>
</template>

<script>
    import Contacts from "./contacts";
    import Threads from "./threads";
    import Messages from "./messages";
    import CreateGroup from "./createGroup";

    export default {
        name: "index",

        components: {CreateGroup, Messages, Threads, Contacts},

        props: ['auth_id'],

        data: function () {
            return {
                thread: null,
                threadQuery: ''
            }
        },

        mounted: function () {
            $('.create').on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                console.log("1");
            }).on('dragover dragenter', function () {
                console.log("2");
            }).on('dragleave dragend drop', function () {
                console.log("3");
            }).on('drop', function (e) {
                // droppedFiles = e.originalEvent.dataTransfer.files;
                console.log("4");
            });
        },

        methods: {
            toggleMenu: function () {
                $('#toggleIcon').toggleClass("fa-bars").toggleClass("fa-remove");
                $('.thread-dialog-background').fadeToggle();
                $('#toggleMenu').slideToggle();
                $('#scroll-2').scrollTop(0)
                    .toggleClass('overflow-hidden');
            },
            toggleContact: function () {
                $('#contactsModal').modal('toggle');
                this.toggleMenu();
            },
            toggleGroup: function () {
                $('#createGroupModal').modal('toggle');
                this.toggleMenu();
            }
        }
    }
</script>

<style scoped>
    .menu {
        top: -1px;
        left: 14px;
        width: calc(100% - 13px);
    }

    .messenger-body {
        height: 512px !important;
    }

    .thread-dialog-background {
        position: absolute;
        top: -1px;
        left: 14px;
        display: none;
        width: calc(100% - 13px);
        height: calc(100% + 1px);
        background-color: rgba(0, 0, 0, 0.31);
    }

    #toggleMenu {
        z-index: 99;
    }

    #scroll-2 {
        overflow-y: scroll;
        overflow-x: hidden;
    }

    #scroll-2::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0);
    }

    #scroll-2::-webkit-scrollbar {
        width: 5px;
        background-color: rgba(255, 255, 255, 0);
    }

    #scroll-2::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        background-color: rgba(0, 0, 0, 0.3);
    }

    .chat-head {
        min-height: 60px;
    }
</style>