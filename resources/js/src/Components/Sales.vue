<script setup>
import {onMounted, ref} from "vue";
import {fetchSales} from "../Services/sales";
import Paginator from "./Paginator.vue";

const sales = ref()
const meta = ref()

onMounted(async () => updateSales(1))

async function updateSales(page) {
    const salesResponse = await fetchSales({page})
    sales.value = salesResponse.data
    meta.value = salesResponse.meta
}

</script>

<template>
    <div class="container text-center">
        <table class="table table-striped table-bordered table-sm mt-lg-5 table-hover">
            <thead>
                <th>{{ $t('sales.id_header') }}</th>
                <th>{{ $t('sales.amount_header') }}</th>
                <th>{{ $t('sales.client_header') }}</th>
                <th>{{ $t('sales.date_header') }}</th>
            </thead>
            <tbody>
                <tr v-for="sale in sales">
                    <td>{{ sale.id }}</td>
                    <td>{{ sale.amount }}</td>
                    <td>{{ sale.client.first_name }} {{ sale.client.last_name }}</td>
                    <td>{{ sale.created_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="container text-center">
        <Paginator v-if="meta"
                   @pageChanged="updateSales"
                   :per_page="meta.per_page"
                   :total_pages="meta.last_page"
                   :current_page="meta.current_page"
                   :buttons_count="10"
        />
    </div>
</template>

<style scoped>

</style>
