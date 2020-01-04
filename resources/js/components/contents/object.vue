<template>
    <div class="card">
        <div class="card-body">
            <p>{{ object.body }}</p>
        </div>
        <div class="card-footer">
            {{ object.user.name }}
        </div>
    </div>
</template>

<script>
    export default {
        props: ['object'],

        data: function () {
            return {

            }
        }
    }
</script>

<style scoped>

</style>