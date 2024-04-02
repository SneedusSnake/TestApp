<script setup>
import {onMounted, ref } from 'vue'
import { fetchClients } from "../Services/statistics"
import ClientsChart from "./ClientsChart.vue";

const clients = ref()

onMounted(async () => {
    clients.value = await fetchClients()
})
</script>

<template>
    <ClientsChart v-if="clients" :clients="clients"/>
    <table v-if="clients" class="table table-striped table-sm">
        <thead>
        <th>Date</th>
        <th>New Clients</th>
        </thead>
        <tbody>
        <tr v-for="client in clients">
            <td>{{ client.date }} </td>
            <td>{{ client.count }}</td>
        </tr>
        </tbody>
    </table>
</template>

<style scoped>

</style>
