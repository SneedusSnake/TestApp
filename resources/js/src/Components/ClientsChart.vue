<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps(['clients'])
const chart = ref(null)
const { t } = useI18n()

onMounted(() => {
    new Chart(chart.value, {
        type: 'bar',
        data: {
            labels: props.clients.map(client => client.date),
            datasets: [
                {
                    label: t('statistics.clients.chart'),
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
