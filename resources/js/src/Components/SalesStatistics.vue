<script setup>
import {onMounted, ref, computed, watch } from 'vue'
import { fetchSales } from "../Services/statistics"
import { fetchClients } from "../Services/clients"
import SalesChart from "./SalesChart.vue";

const sales = ref()
const date_from = ref()
const date_to = ref()
const rest = computed(() =>
        sales.value.total - sales.value.clients.map(client => +client.total_amount).
        reduce((sum, amount) => sum + amount, 0)
)
const clients = ref()

watch(date_from, () => updateSales())
watch(date_to, () => updateSales())
watch(sales, async () => {
    clients.value = (await fetchClients({
        ids: sales.value.clients.map(client => client.client_id)
    })).data.reduce((map, client) => {
        map[client.id] = client
        return map
    }, {})
})

onMounted(async () => {
    await updateSales()
})

async function updateSales() {
    clients.value = null
    sales.value = await fetchSales(date_from.value, date_to.value)
}
</script>

<template>
    <div class="container">
        <div class="row">
            <div class="col form-floating">
                <input id="salesFrom" type="date" class="form-control form-control-sm" placeholder="test" v-model="date_from">
                <label class="form-label" for="salesFrom">Date From</label>
            </div>
            <div class="col form-floating">
                <input id="salesTo" type="date" class="form-control form-control-sm" placeholder="test" v-model="date_to">
                <label class="form-label" for="salesTo">Date To</label>
            </div>
        </div>
    </div>
    <SalesChart v-if="clients" :clients="clients" :sales="sales.clients" :rest="rest"/>
    <table class="table table-striped table-bordered table-hover table-sm mt-lg-5">
        <thead>
        <th>Client</th>
        <th>Amount</th>
        </thead>
        <tbody v-if="clients" >
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
