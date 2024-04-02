<script setup>
defineProps(['active_tab'])
</script>

<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#/">Test App</a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: ['/', '/statistics'].includes(active_tab) }" aria-current="page" href="#/statistics">Statistics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: active_tab == '/clients' }" href="#/clients">Clients</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style scoped>

</style>
