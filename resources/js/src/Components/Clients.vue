<script setup>
    import { ref, onMounted } from 'vue'
    import { fetchClients } from "../Services/clients";
    import Paginator from "./Paginator.vue";

    const clients = ref()
    const meta = ref()

    onMounted(async () => {
        await updateClients(1)
    })

    async function updateClients(page) {
        let clientsResponse = await fetchClients({page})
        clients.value = clientsResponse.data
        meta.value = clientsResponse.meta
    }
</script>

<template>
    <div class="">
        <table class="table table-striped table-bordered table-sm mt-lg-5 table-hover">
            <thead>
            <th>First name</th>
            <th>Last name</th>
            <th>Country</th>
            <th>Email</th>
            <th>Website</th>
            </thead>
            <tbody v-if="clients">
            <tr v-for="client in clients">
                <td>{{ client.first_name }}</td>
                <td>{{ client.last_name }}</td>
                <td>{{ client.country.country }}</td>
                <td>{{ client.email }}</td>
                <td>{{ client.websites[0] }}</td>
            </tr>
            </tbody>
        </table>
        <Paginator v-if="meta"
                   @pageChanged="updateClients"
                   :per_page="meta.per_page"
                   :total_pages="meta.last_page"
                   :current_page="meta.current_page"
                   :buttons_count="10"
        />

    </div>
</template>

<style scoped>

</style>
