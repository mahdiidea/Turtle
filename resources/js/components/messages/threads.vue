<template>
    <div class="form-group threads flip-list-move" style="margin-top: -1px">
        <transition-group name="flip-list" tag="div" v-on:before-enter="beforeEnter" v-on:enter="enter"
                          v-on:leave="leave">
            <a class="list-group-item list-group-item-action flex-column border-left-0 border-right-0 rounded-0"
               :id="thread != null ? (thread.id === th.id ? 'selected' : '') : ''" style="white-space: nowrap"
               @click="$emit('selectThread', th)" v-for="th in orderedThreads" v-bind:key="th.id">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1 w-100 content-ellipsis">{{ th.name }}
                        <span class="float-right text-small text-black-50">{{ fromNow(th.updated_at) }}</span>
                    </h5>
                </div>
                <div class="content-ellipsis d-flex">
                    <small style="color: #419fd9">{{ last_message_attach(th.last_message) }}</small>
                    <div style="width: calc(100% - 15px)" class="content-ellipsis">{{ th.last_message == null ? "&nbsp;" : th.last_message.body }}</div>
                    <small class="float-right" v-if="th.you != null && th.you">
                        <img src="/medias/check-green.svg" alt="" width="17" height="17" v-if="th.seen == null">
                        <img src="/medias/check-green.svg" alt="" width="17" height="17" v-else-if="th.seen === 0">
                        <img src="/medias/d-check-green.svg" alt="" width="17" height="17" v-else-if="th.seen === 1">
                    </small>
                    <small class="float-right" v-else-if="th.unread != null && th.unread > 0">
                        <span class="badge badge-success" style="font-size: 11px">{{ th.unread }}</span>
                    </small>
                </div>
            </a>
        </transition-group>
    </div>
</template>

<script>
    import 'lodash';
    import Velocity from 'velocity-animate';

    export default {
        name: "threads",

        props: ['auth_id', 'search', 'thread'],

        data: function () {
            return {
                threads: [],
                query: this.search
            }
        },

        watch: {
            search: function (newVal, oldVal) {
                this.query = newVal;
            }
        },

        computed: {
            orderedThreads: function () {
                let vm = this;
                return _.orderBy(this.threads.filter(function (item) {
                    return item.name.toLowerCase().indexOf(vm.query.toLowerCase()) !== -1
                }), 'updated_at').reverse();
            }
        },

        created: function () {
            let vm = this;
            vm.fetch();
            Echo.private(`messengers.${this.auth_id}`)
                .listen('ThreadEvent', (e) => {
                    vm.updateOrCreate(e.thread);
                });
        },

        methods: {
            fetch: function () {
                axios.get('/threads')
                    .then(({data}) => this.threads = data)
                    .catch(({response}) => Vue.toasted.show(response.message).goAway(1500));
            },
            beforeEnter: function (el) {
                el.style.opacity = 0;
                el.style.height = 0;
            },
            enter: function (el, done) {
                let delay = el.dataset.index * 150;
                setTimeout(function () {
                    Velocity(
                        el,
                        {opacity: 1, height: '73.3px'},
                        {complete: done}
                    )
                }, delay)
            },
            leave: function (el, done) {
                let delay = el.dataset.index * 150;
                setTimeout(function () {
                    Velocity(
                        el,
                        {opacity: 0, height: 0},
                        {complete: done}
                    )
                }, delay)
            },
            last_message_attach: function (msg) {
                if (msg == null)
                    return "";

                if (msg.attaches.length === 1) {
                    return msg.attaches[0].name + ", ";
                }

                if (msg.attaches.length > 1) {
                    return msg.attaches.length + " attachments, "
                }

                return " ";

            },
            updateOrCreate: function (thread) {
                for (let i = 0; i < this.threads.length; i++) {
                    if (this.threads[i].id === thread.id) {
                        console.log("changed seen : " + thread.seen);
                        Vue.set(this.threads, i, thread);
                        return;
                    }
                }
                this.threads.push(thread);
            }
        }
    }
</script>

<style scoped>
    .threads a {
        cursor: pointer;
    }

    #selected {
        background: whitesmoke;
    }

    .flip-list-move {
        transition: transform 1s;
    }
</style>