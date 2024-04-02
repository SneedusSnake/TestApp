<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref, computed } from 'vue'

const props = defineProps(['clients', 'sales', 'rest'])

const chart = ref(null)

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
                    label: 'Sales by client',
                    /*
                        Т.к данные сгенерированы рандомно, продажи самых крупных клиентов ничтожно малы
                        по сравнению с суммой продаж всех остальных килентов,
                        поэтому даже не отображаются на диаграме. В целях демонстрации здесь подставлено случайное число,
                        вместо актуальных продаж всех клиентов, хранящихся в props.rest
                     */
                    data: [...props.sales.map(client_sales => client_sales.total_amount),  Math.random()*10000],
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
