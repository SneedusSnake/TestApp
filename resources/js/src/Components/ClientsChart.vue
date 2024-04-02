<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref } from 'vue'

const props = defineProps(['clients'])
const chart = ref(null)

onMounted(() => {
    new Chart(chart.value, {
        type: 'bar',
        data: {
            labels: props.clients.map(client => client.date),
            datasets: [
                {
                    label: 'Clients registered by date',
                    data: props.clients.map(client => client.count)
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
