<template>
    <div class="container">
        <messages-index :auth_id="auth_id"></messages-index>
    </div>
</template>

<script>
    export default {
        name: "activities",

        props: ['auth_id'],
    }
</script>

<style scoped>

</style>