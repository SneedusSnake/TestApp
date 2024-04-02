<script setup>
import { ref, computed } from 'vue'
import Statistics from "./Statistics.vue"
import Clients from "./Clients.vue";
import Menu from "./Menu.vue";
const count = ref(0)

const routes = {
    '/': Statistics,
    '/statistics': Statistics,
    '/clients': Clients
}

const currentPath = ref(window.location.hash)

window.addEventListener('hashchange', () => {
    currentPath.value = window.location.hash
})

const currentView = computed(() => {
    return routes[currentPath.value.slice(1) || '/'] || Statistics
})
</script>

<template>
    <Menu :active_tab="currentPath.slice(1)" />
    <component :is="currentView"></component>
</template>
<style scoped>
</style>
