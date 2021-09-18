<template>
    <div>
        <p>Hello!</p>
    </div>
</template>

<script>
export default {
    name: "GameComponent",
    mounted() {
        console.log(this.$router.currentRoute.value.name);
    }
}
</script>

<style scoped>

</style>