<script setup>
import {onMounted, ref, defineEmits } from "vue";
import { fetchCountries } from "../Services/countries";
import { createClient } from "../Services/clients";

const emit = defineEmits(['clientCreated'])
const countries = ref()
const client = ref({
    websites: [],
    emails: []
})
const errors = ref({})

onMounted(async () => {
    countries.value = await fetchCountries()
})

async function postForm() {
    errors.value = {}
    const response = await createClient(client.value)

    errors.value = response.errors ? response.errors : {}

    if (!response.errors) {
        client.value = {
            websites: [],
            emails: []
        }
        emit('clientCreated')
    }
}

</script>

<template>
    <form>
        <div class="row">
            <div class="col-sm-3">
                <input v-model="client.first_name" type="text" class="form-control form-control-sm" :placeholder="$t('clients.form.first_name')" required>
            </div>
            <div class="col-3">
                <input v-model="client.last_name" type="text" class="form-control form-control-sm" :placeholder="$t('clients.form.last_name')" required>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <input v-model="client.email" :class="{'is-invalid': errors.email?.length}" type="email" class="form-control form-control-sm" :placeholder="$t('clients.form.email')" required>
                <div v-for="error in errors.email" class="invalid-feedback">
                    {{ error }}
                </div>
            </div>
            <div class="col-sm-3">
                <input v-model="client.websites[0]" :class="{'is-invalid': errors['websites.0']}" type="url" class="form-control form-control-sm" :placeholder="$t('clients.form.website')" required>
                <div v-for="error in errors['websites.0']" class="invalid-feedback">
                    {{ error }}
                </div>
            </div>
        </div>
        <div class="row" v-for="n in Math.max(client.emails.length, client.websites.length, 1)">
            <div  class="col-sm-3">
                <input v-if="client.email" v-model="client.emails[n]" :class="{'is-invalid': errors['emails.' + n]}" type="email" class="form-control form-control-sm" :placeholder="$t('clients.form.additional_email')">
                <div v-for="error in errors['emails.' + n]" class="invalid-feedback">
                    {{ error }}
                </div>
            </div>
            <div class="col-sm-3">
                <input v-if="client.websites[0] && client.websites[n-1]" v-model="client.websites[n]" :class="{'is-invalid': errors['websites.' + n]}" type="url" class="form-control form-control-sm" :placeholder="$t('clients.form.additional_website')">
                <div v-for="error in errors['websites.' + n]" class="invalid-feedback">
                    {{ error }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <select class="form-select" v-model="client.country_id" required>
                    <option disabled selected>{{ $t('clients.form.country_selection') }}</option>
                    <option v-if="countries" v-for="country in countries" :value="country.id">{{ country.country }}</option>
                </select>
            </div>
        </div>
        <button @click="postForm" type="submit" class="btn btn-primary">{{ $t('clients.form.submit') }}</button>
    </form>
</template>

<style scoped>
    form {
        margin-top: 10px;
    }
    .row {
        margin-bottom: 10px;
    }
</style>
