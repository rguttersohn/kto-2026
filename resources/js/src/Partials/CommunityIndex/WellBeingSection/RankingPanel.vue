<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import {computed} from 'vue';
import { useWellBeingStore } from '../../../../stores/well-being';
import { Location } from '../../../../types/locations';

const wellBeing = useWellBeingStore();

const page = usePage<{
    location:Location
}>();

const currentLocationScore = computed(() => {

    if(!wellBeing.domainScoresByLocation){

        return null;

    }

    return wellBeing.domainScoresByLocation.find(score => score.id === page.props.location.id);
})

</script>

<template>
    <section>
        <h3>Ranking Panel</h3>
        <p>Score:</p>
        <pre>{{ currentLocationScore }}</pre>
    </section>
</template>