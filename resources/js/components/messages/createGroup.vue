<template>
    <div class="modal fade" id="createGroupModal" tabindex="-1"
         role="dialog" aria-labelledby="contactsModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="exampleModalLabel">Create group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 h-100 border-top">
                    <div class="form-group py-3 mx-3" v-if="level === 1">
                        <label for="group">Group name</label>
                        <input class="form-control" placeholder="Group name" id="group" v-model="group"
                               v-on:keypress.enter="next">
                    </div>
                    <div class="form-group" v-else-if="level === 2">
                        <h5 class="m-3">Add member to "{{ this.group }}"</h5>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-action flex-column border-left-0 border-right-0 rounded-0"
                               href="#" @click="add(con)" v-for="con in contacts.data">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ con.name }}</h5>
                                    <small v-if="exists(con)">
                                        <i class="fa fa-check"></i>
                                    </small>
                                </div>
                                <small>{{ con.email }}</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="next">
                        <i class="fa fa-user-plus"></i>
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "createGroup",

        data: function () {
            return {
                group: "",
                level: 1,
                members: [],
                contacts: {
                    data: []
                }
            }
        },

        created: function () {
            this.fetch();
        },

        methods: {
            fetch: function () {
                axios.get('/contacts')
                    .then(({data}) => this.contacts = data)
                    .catch(({response}) => Vue.toasted.show(response.message).goAway(1500));
            },
            next: function () {
                if (this.level === 1) {
                    if (this.group.length > 3)
                        this.level = 2;
                    else
                        Vue.toasted.show("group name must be at least 3 character").goAway(1500)
                } else {
                    axios.post('/threads', {
                        users: this.members,
                        caption: this.group
                    }).then(({data}) => {
                        this.$emit('selectThread', data);
                        $('#createGroupModal').modal('toggle');
                    }).catch(({response}) => Vue.toasted.show(response.message).goAway(1500));
                }
            },
            exists: function (user) {
                for (let i = 0; i < this.members.length; i++)
                    if (user.id === this.members[i]) return true;
                return false;
            },
            add: function (user) {
                if (this.exists(user)) {
                    for (let i = 0; i < this.members.length; i++) {
                        if (user.id === this.members[i]) {
                            this.members.splice(i, 1);
                            return;
                        }
                    }
                } else {
                    this.members.push(user.id);
                }
            }
        }
    }
</script>

<style scoped>

</style>