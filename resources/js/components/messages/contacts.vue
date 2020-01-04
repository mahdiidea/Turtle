<template>
    <div class="modal fade" id="contactsModal" tabindex="-1"
         role="dialog" aria-labelledby="contactsModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="exampleModalLabel">Contacts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 h-100">
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action flex-column border-left-0 border-right-0 rounded-0"
                           href="#" @click="create(con)" v-for="con in contacts.data">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ con.name }}</h5>
                                <!--<small>3 days ago</small>-->
                            </div>
                            <!--<p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>-->
                            <small>{{ con.email }}</small>
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="fa fa-user-plus"></i>
                        Add Contact
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "contacts",

        data: function () {
            return {
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
            create: function (con) {
                axios.post('/threads', {
                    users: [con.id]
                }).then(({data}) => {
                    this.$emit('selectThread', data);
                    $('#contactsModal').modal('toggle');
                }).catch(({response}) => Vue.toasted.show(response.message).goAway(1500));
            }
        }
    }
</script>

<style scoped>
    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        max-height: 512px;
        overflow-y: auto;
    }
</style>