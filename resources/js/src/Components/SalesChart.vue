<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps(['clients', 'sales', 'rest'])

const chart = ref(null)
const { t } = useI18n()

const labels = computed(() => [
    ...props.sales.map(client_sale => {
        const client = props.clients[client_sale.client_id]

        return `${client.first_name} ${client.last_name}`
    }),
    'rest'
])

onMounted(() => {
    new Chart(chart.value, {
        type: 'bar',
        data: {
            labels: labels.value,
            datasets: [
                {
                    label: t('statistics.sales.chart'),
                    /*
                        Т.к данные сгенерированы рандомно, продажи самых крупных клиентов ничтожно малы
                        по сравнению с суммой продаж всех остальных килентов,
                        поэтому даже не отображаются на диаграме. В целях демонстрации здесь подставлено случайное число,
                        вместо актуальных продаж всех клиентов, хранящихся в props.rest
                     */
                    data: [...props.sales.map(client_sales => client_sales.total_amount),  Math.random()*10000],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)',
                        'rgba(135, 159, 79, 0.2)',
                        'rgba(180, 81, 159, 0.2)',
                        'rgba(255, 110, 86, 0.2)',
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)',
                        'rgb(135, 159, 79)',
                        'rgb(180, 81, 159)',
                        'rgb(255, 110, 86)',
                    ],
                    borderWidth: 1,
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
