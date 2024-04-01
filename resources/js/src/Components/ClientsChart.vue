<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref } from 'vue'

const props = defineProps(['clients'])
const chart = ref(null)

onMounted(() => {
    new Chart(chart.value, {
        type: 'bar',
        data: {
            labels: Object.keys(props.clients),
            datasets: [
                {
                    label: 'Clients registered by date',
                    data: Object.values(props.clients),
                },
            ],
        }
    })
})
</script>

<template>
    <canvas ref="chart"></canvas>
</template>

<style scoped>

</style>
