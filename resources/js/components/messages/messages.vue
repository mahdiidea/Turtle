<template>
    <div class="chats-body">
        <div class="form-group chats" id="chats">
            <div class="root-message" v-for="(msg, index) in orderedMessages">
                <div class="w-100" v-if="isDateChange(msg, orderedMessages[index - 1])">
                    <div class="date-flag">
                        {{ stringDate(msg.created_at) }}
                    </div>
                </div>
                <div :class="msg.user_id === auth_id ? 'message send' : 'message rec'" :id="'msg-' + msg.id">
                    <div class="attaches" v-if="msg.attaches.length">
                        <attaches :input="msg.attaches"></attaches>
                    </div>
                    <span class="mx-2">{{ msg.body }}</span>
                    <div class="float-right text-black-50 ml-1">
                        <small>{{ times(msg.created_at) }}</small>
                        <small class="float-right" v-if="msg.user_id === auth_id">
                            <img src="/medias/check-green.svg" alt="" width="20" height="20" v-if="msg.seen == null">
                            <img src="/medias/check-green.svg" alt="" width="20" height="20" v-else-if="msg.seen === 0">
                            <img src="/medias/d-check-green.svg" alt="" width="20" height="20"
                                 v-else-if="msg.seen === 1">
                        </small>
                    </div>
                </div>
            </div>
            <create :thread="thread"></create>
            <div class="form-group position-absolute w-100 h-100 bg-light" style="display: none" id="drag">
                <div class="w-100 h-auto bg-success p-2 text-white">
                    <h4 class="m-0">Upload File ...</h4>
                </div>
                <div class="m-4 dashed">
                    <h4>Drag file here</h4>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Create from "./create";
    import Attaches from "./attaches";

    export default {
        name: "messages",

        components: {Attaches, Create},

        props: ['auth_id', 'thread'],

        data: function () {
            return {
                messages: [],
                last_url: null,
                scroller: false,
                scrollHolder: null,
            }
        },

        watch: {
            thread: function (newVal, oldVal) {
                if (oldVal != null) {
                    if (newVal.id === oldVal.id) return;
                    Echo.leave(`threads.${oldVal.id}`);
                }
                Echo.private(`threads.${newVal.id}`).listen('MessageCreatedEvent', (e) => {
                    this.messages.push(e.message);
                    this.scroller = true;
                    if (e.message.user_id !== this.auth_id)
                        this.markAsRead();
                });

                let vm = this;
                $('#chats').slideUp(500, function () {
                    vm.messages = [];
                    vm.fetch();
                });
            }
        },

        computed: {
            orderedMessages: function () {
                return _.orderBy(this.messages, 'created_at');
            }
        },

        updated: function () {
            if (this.scroller) {
                let message = document.getElementById('chats');
                message.scrollTop = message.scrollHeight;
                if (message.scrollHeight > 512)
                    this.scroller = false;
                $('#chats').slideDown(500);
            }
            if (this.scrollHolder != null) {
                try {
                    let chats = document.getElementById('chats');
                    let scrollTop = $(window).scrollTop(),
                        elementOffset = $('#msg-' + this.scrollHolder).offset().top;
                    chats.scrollTop = (elementOffset - scrollTop) - 150;
                } catch (e) {
                }
            }
        },

        mounted: function () {
            let vm = this;
            $('#chats').scroll(function () {
                if ($(this).scrollTop() === 0) {
                    if (vm.last_url != null) {
                        vm.fetch(vm.last_url);
                    }
                }
            });
            //
            // let drag = $('#drag');
            //
            // $('.chats-body').on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
            //     e.preventDefault();
            //     e.stopPropagation();
            // }).on('dragenter', function () {
            //     drag.slideDown(500);
            //     console.log("slideDown");
            //     this.drag = true;
            // }).on('dragleave', function () {
            //     drag.slideUp(500);
            //     console.log("slideUp");
            //     this.drag = false;
            // }).on('drop', function (e) {
            //     // // droppedFiles = e.originalEvent.dataTransfer.files;
            //     // console.log("4");
            // });
        },

        methods: {
            markAsRead: function () {
                axios.post(`threads/${this.thread.id}/messages/markAsRead`);
            },
            fetch: function (page = null) {
                if (page == null) {
                    page = `/threads/${this.thread.id}/messages`;
                    this.scroller = true;
                } else {
                    if (this.messages[this.messages.length - 1] !== undefined)
                        this.scrollHolder = this.messages[this.messages.length - 1].id;
                }
                axios.get(page).then(({data}) => {
                    for (let i = 0; i < data.data.length; i++) {
                        if (!this.exist(data.data[i]))
                            this.messages.push(data.data[i]);
                    }
                    this.last_url = data.next_page_url;
                }).catch(({response}) => Vue.toasted.show(response.message).goAway(1500));
            },
            exist: function (msg) {
                for (let i = 0; i < this.messages.length; i++) {
                    if (this.messages.id === msg.id)
                        return true;
                }
                return false;
            },
            dates: function (input) {
                return input.split(" ")[0];
            },
            times: function (datetime) {
                let moment = require('moment');
                return moment(datetime, "YYYY-MM-DD HH:mm:ss").format('LT');
            },
            isDateChange: function (next, prev) {
                if (next === undefined || prev === undefined) return true;
                return this.dates(next.created_at) !== this.dates(prev.created_at);
            },
            stringDate: function (datetime) {
                let moment = require('moment');
                return moment(datetime, "YYYY-MM-DD HH:mm:ss").format('LL');
            }
        }
    }
</script>

<style scoped>
    .chats-body {
        width: 100% !important;
        height: 100% !important;
        background: whitesmoke;
        padding-bottom: 36px;
        position: relative;
    }

    .dashed {
        width: calc(100% - 3rem);
        height: calc(100% - 80px - 3rem);
        border: 5px dashed #b1b1b1;
    }

    .dashed h4 {
        position: absolute;
        margin: auto;
        top: 50%;
        left: 50%;
        color: #3d3d3d;
        transform: translate(-50%, -50%);
    }

    .date-flag {
        width: fit-content;
        height: fit-content;
        background: rgba(0, 0, 0, 0.50);
        border-radius: 5px;
        color: white;
        margin: 5px auto;
        padding: 1px 5px;
    }

    #drag {
        left: 0;
        right: 0;
    }

    .chats {
        width: calc(100% - 15px);
        max-height: 100%;
        margin: 0 15px;
        display: inline-grid;
        flex-wrap: wrap;
        overflow-x: hidden;
        overflow-y: scroll;
    }

    .chats::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0);
    }

    .chats::-webkit-scrollbar {
        width: 5px;
        background-color: rgba(255, 255, 255, 0);
    }

    .chats::-webkit-scrollbar-thumb {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0);
        background-color: rgba(0, 0, 0, 0.3);
    }

    .root-message {
        display: contents;
    }

    .message {
        width: auto !important;
        height: fit-content;
        text-align: left;
        max-width: 75%;
        padding: .25rem 1rem;
    }

    .rec {
        background: #fff !important;
        border-bottom: 2px solid #dfe4e8;
        color: #222429;
        margin: 5px auto 5px 5px;
        border-radius: 20px 20px 20px 0;
    }

    .send {
        background: #e9fdcf !important;
        color: #222429;
        border-bottom: 2px solid #c5dbd0;
        margin: 5px 5px 5px auto;
        border-radius: 20px 20px 0 20px;
    }

    .send small {
        color: #9bbb8a;
    }

    .send label {
        color: #9bbb8a;
    }
</style>