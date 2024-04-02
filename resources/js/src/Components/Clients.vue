<script setup>
    import { ref, onMounted } from 'vue'
    import { fetchClients } from "../Services/clients";
    import Paginator from "./Paginator.vue";
    import ClientForm from "./ClientForm.vue";

    const clients = ref()
    const meta = ref()
    const formOpened = ref(false)

    onMounted(async () => {
        await updateClients(1)
    })

    async function updateClients(page) {
        formOpened.value = false
        let clientsResponse = await fetchClients({page})
        clients.value = clientsResponse.data
        meta.value = clientsResponse.meta
    }
</script>

<template>
    <div class="container text-center">
        <div class="row">
            <div class="col">
                <button @click="formOpened = !formOpened" class="btn btn-primary">Add client</button>
            </div>
        </div>
        <ClientForm v-if="formOpened" @clientCreated="updateClients(meta.current_page)"/>
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
        <div class="container text-center">
            <Paginator v-if="meta"
                       @pageChanged="updateClients"
                       :per_page="meta.per_page"
                       :total_pages="meta.last_page"
                       :current_page="meta.current_page"
                       :buttons_count="10"
            />
        </div>

    </div>
</template>

<style scoped>
    table {
        margin-top: 10px;
    }
    .row {
        margin-top: 10px;
    }
</style>
