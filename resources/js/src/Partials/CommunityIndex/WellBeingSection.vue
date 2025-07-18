<script lang="ts" setup>
import { Select, SelectChangeEvent } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage<{
    well_being_domains: Array<{
        id: number,
        name:string
    }>
}>();

const currentDomain = ref<{
    id:number, 
    name:string
} | null>(null);

function handleDomainSelection(event:SelectChangeEvent){

    currentDomain.value = event.value;

}

</script>

<template>
    <section class="my-20 w-11/12 mx-auto">
        <h2>Well-Being Score</h2>
        <div class="flex border-2 border-gray-700 p-20">
            <Select
                :options="page.props.well_being_domains"
                option-label="name"
                @change="handleDomainSelection"
                :pt="{
                    root: {
                        class: 'relative p-3 rounded-lg border-2 border-gray-700',
                    },
                    dropdownIcon: {
                        class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3',
                    },
                    listContainer: {
                        class: 'p-3 overflow-y-auto bg-white border-b-2 border-x-2 border-gray-700 shadow-sm',
                    },
                    option: {
                        class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white',
                    },
                }"
            >
                <template v-slot:value>{{ currentDomain?.name ?? 'Select a Domain' }}</template>
            </Select>
        </div>
    </section>
</template>