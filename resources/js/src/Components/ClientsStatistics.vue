<script setup>
import {onMounted, ref, watch} from 'vue'
import { fetchClients } from "../Services/statistics"
import ClientsChart from "./ClientsChart.vue";

const clients = ref()
const date_from = ref()
const date_to = ref()
watch(date_from, () => updateClients())
watch(date_to, () => updateClients())

onMounted(async () => {
    await updateClients()
})
async function updateClients() {
    clients.value = await fetchClients(date_from.value, date_to.value)
}
</script>

<template>
    <div class="container">
        <div class="row">
            <div class="col form-floating">
                <input id="clientsFrom" type="date" class="form-control form-control-sm" v-model="date_from">
                <label class="form-label" for="clientsFrom">{{ $t('statistics.clients.date_from') }}</label>
            </div>
            <div class="col form-floating">
                <input id="clientsTo" type="date" class="form-control form-control-sm" v-model="date_to">
                <label class="form-label" for="clientsTo">{{ $t('statistics.clients.date_from') }}</label>
            </div>
        </div>
    </div>
    <ClientsChart v-if="clients" :clients="clients"/>
    <table v-if="clients" class="table table-striped table-bordered table-hover table-sm mt-lg-5">
        <thead>
        <th>{{ $t('statistics.clients.date_table_header') }}</th>
        <th>{{ $t('statistics.clients.new_clients_amount_table_header') }}</th>
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
