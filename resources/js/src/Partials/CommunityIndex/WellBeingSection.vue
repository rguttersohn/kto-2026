<script lang="ts" setup>
import DomainSelection from './WellBeingSection/DomainSelection.vue';
import RankingPanel from './WellBeingSection/RankingPanel.vue';

</script>

<template>
    <section class="my-20 w-11/12 mx-auto">
        <h2>Well-Being Score</h2>
        <div class="flex gap-x-10 border-2 border-gray-700 p-20">
            <DomainSelection/>
            <RankingPanel/>
        </div>
    </section>
</template>