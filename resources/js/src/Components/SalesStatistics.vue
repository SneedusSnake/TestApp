<script setup>
import {onMounted, ref, computed } from 'vue'
import { fetchSales } from "../Services/statistics"
import { fetchClients } from "../Services/clients"
import SalesChart from "./SalesChart.vue";

const sales = ref()
const clients = ref()
const rest = computed(() =>
        sales.value.total - sales.value.clients.map(client => +client.total_amount).
        reduce((sum, amount) => sum + amount, 0)
)

onMounted(async () => {
    sales.value = await fetchSales()
    clients.value = (await fetchClients(sales.value.clients.map(client => client.client_id))).reduce((map, client) => {
        map[client.id] = client
        return map
    }, {})

})
</script>

<template>
    <SalesChart v-if="clients" :clients="clients" :sales="sales.clients" :rest="rest"/>
    <table v-if="clients" class="table table-striped table-sm">
        <thead>
        <th>Client</th>
        <th>Amount</th>
        </thead>
        <tbody>
        <tr v-for="client_sales in sales.clients">
            <td>{{ clients[client_sales.client_id].first_name }}  {{ clients[client_sales.client_id].last_name }}</td>
            <td>{{ client_sales.total_amount }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>{{ rest }}</td>
        </tr>
        </tbody>
    </table>

</template>

<style scoped>

</style>
