<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { shallowRef } from 'vue';
import { Link } from '@inertiajs/vue3';
    
    defineOptions({
        layout: AppLayout
    });

    
    const props = defineProps<{
        location_types: Array<{
            id: number, 
            plural_name: string,
            slug: string,
            scope:string,
            classification: string, 
        }>


    }>();

const locations = shallowRef<{
    id: number,
    name: string,
    fips: number, 
    geopolitical_id: number,
    
} | null>(null)

async function handleLocationTypeClicked(event:Event){

    const target = event.target as HTMLButtonElement;

    const locationTypeID = target.dataset.locationTypeId;

    if(!locationTypeID){

        return;
    }

    const url = `/api/app/location-types/${locationTypeID}`;

    const response = await fetch(url);

    const data = await response.json();

    locations.value = data.data.locations;

}

</script>

<template>
    <h1>Community Profiles</h1>
    <h2>Select a Location Type</h2>
    <div class="flex">
        <ul>
            <li
                v-for="{plural_name, id} in props.location_types"
                :key="id"
                >
                <button
                    @click="handleLocationTypeClicked"
                    class="w-48 p-3 border-2"
                    :data-location-type-id="id"
                    >{{ plural_name }}</button>
            </li>
        </ul>
        <ul v-show="locations">
            <li 
                v-for="{id, name } in locations"
                :key="id"
                >
                <Link :href="`/community-profiles/${id}`">{{ name }}</Link>
                </li>
        </ul>
    </div>
</template>