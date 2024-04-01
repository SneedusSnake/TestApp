<script setup>
import SalesStatistics from "./SalesStatistics.vue";
import ClientsStatistics from "./ClientsStatistics.vue";
import {ref} from "vue";

const chart = ref(null)
</script>

<template>
    <div class="container text-center">
        <div class="row">
            <div class="col"><SalesStatistics/></div>
            <div class="col"><ClientsStatistics/></div>
        </div>
    </div>
</template>
