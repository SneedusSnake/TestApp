<script setup>
    import { computed } from "vue";

    const props = defineProps(['per_page', 'total_pages', 'current_page', 'buttons_count'])

    const emit = defineEmits(['pageChanged'])

    const start_page = computed(() => {
        if (props.current_page === 1) {
            return 1
        }

        if (props.current_page === props.total_pages) {
            return props.total_pages - props.buttons_count
        }

        return props.current_page - 1
    })

    const pages = computed(() => {
        let pages = []

        for (let i = start_page.value; i <= Math.min(props.total_pages, start_page.value + props.buttons_count - 1); i++) {
            pages.push(i)
        }

        return pages
    })

    function onClickPage(page) {
        emit('pageChanged', page)
    }
</script>

<template>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item">
                <button @click="onClickPage(current_page - 1)" :class="{ disabled: current_page === 1 }" class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </button>
            </li>
            <li v-for="page in pages"  class="page-item"><button @click="onClickPage(page)" :class="{disabled: current_page === page }" class="page-link">{{ page }}</button></li>
            <li class="page-item">
                <button @click="onClickPage(current_page + 1)" :class="{ disabled: current_page === total_pages }" class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </li>
        </ul>
    </nav>
</template>

<style scoped>

</style>
