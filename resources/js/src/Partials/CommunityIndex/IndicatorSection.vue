<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { SelectChangeEvent } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { Indicator } from '../../../types/indicators';
import { Select } from 'primevue';
import { fetchLocationIndicatorData } from '../../../services/fetch/fetch-locations';
import { useErrorStore } from '../../../stores/errors';
import { Location } from '../../../types/locations';

const page = usePage<{
    indicators: Indicator[],
    location: Location
}>();


const {indicators, location} = page.props;
const indicator = useIndicatorsStore();
const error = useErrorStore();


async function handleChange(event: SelectChangeEvent){

    indicator.indicator = event.value;


    if(!indicator.indicator){

        console.error('current indicator is null');

        return;
    }

    const locationID = location.id;

    const indicatorID = indicator.indicator.id;

    const params = indicator.getFiltersAsParams(indicator.selectedFilters);

    const {data, error:responseError} = await fetchLocationIndicatorData(locationID, indicatorID, params);

    if(responseError.status){

        error.error = true;

        error.errorMessage = responseError.message;

        return;
     
    }

    indicator.indicatorData = data;

}


</script>

<template>
    <section class="my-20 px-20">
        <h2>Indicators</h2>
        <section class="w-10/12">
            <Select
                :options="indicators"
                option-label="name"
                @change="handleChange"
                filter
                :pt="{
                        root: {
                            class: 'w-2/4 relative p-3 rounded-lg border-2 border-gray-700',
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
                        pcFilterContainer: {
                            root: {
                                class: 'w-full relative w-3/4 mx-aut0 bg-white'
                            }
                        },
                        pcFilter: {
                            root:{
                                class: 'w-full px-3 py-1 border-2 border-gray-700 bg-gray-100'
                            }
                        },
                        pcFilterIconContainer: {
                            root: {
                                class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3'
                            }
                        }
                    }"
            >
                <template v-slot:value>
                    {{ indicator.indicator?.name  ??  'Select an Indicator'}}
                </template>
            </Select>
        </section>
    </section>
</template>