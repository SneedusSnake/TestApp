<script setup>
import {onMounted, ref, computed } from 'vue'
import { fetchSales } from "../Services/statistics"
import { fetchClients } from "../Services/clients"
import SalesChart from "./SalesChart.vue";

const sales = ref()
const clients = ref()
const rest = computed(() =>
        sales.value.total - Object.values(sales.value.clients).
        reduce((sum, amount) => +sum + +amount, 0)
)

onMounted(async () => {
    sales.value = await fetchSales()
    clients.value = (await fetchClients(Object.keys(sales.value.clients))).reduce((map, client) => {
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
        <tr v-for="(amount, clientId) in sales.clients">
            <td>{{ clients[clientId].first_name }}  {{ clients[clientId].last_name }}</td>
            <td>{{ amount }}</td>
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
