import { defineStore } from 'pinia';
import type { Indicator, IndicatorFeature, IndicatorData, SelectedFilters, FilterSelectOption, IndicatorFilters, FilterName } from '../types/indicators';
import { ref, shallowRef, watch} from 'vue';

export const useIndicatorsStore = defineStore('indicators', () => {
  
  const indicator = ref<Indicator | null>(null);

  const indicatorData = shallowRef<IndicatorFeature | null>(null);

  const selectedFilters = ref<SelectedFilters | null>(null);

  const indicatorFilters = ref<IndicatorFilters | null>(null);

  const currentLocation = ref<Pick<IndicatorData, 'location_id' | 'location' | 'location_type' | 'location_type_id'> | null>(null);

  const locationIndicatorData = shallowRef<IndicatorData[] | null>(null);

  const comparedLocations = ref<IndicatorData[][] | null>(null);

  function updateSelectedFilters(filterSelectOption: FilterSelectOption){

    if(!selectedFilters.value){
        return;
    }

    const matchingFilterIndex = selectedFilters.value.findIndex(filterCondition=>filterCondition.name === filterSelectOption.name);

    if(matchingFilterIndex !== -1){
      
          selectedFilters.value.splice(matchingFilterIndex, 1);

    }

    const id = crypto.randomUUID();

    selectedFilters.value.push({
        id: id,
        name: filterSelectOption.name,
        operator: 'eq',
        value: filterSelectOption.value
    })
 
 }

 function getFiltersAsParams(selectedFilters: SelectedFilters):string{

    if(selectedFilters.length === 0){
        return '';
    }

    return selectedFilters.map(filter => {
        return `filter[${filter.name}][${filter.operator}]=${Array.isArray(filter.value) ?
            filter.value.join(',') : filter.value}`;
    }).join('&');

}

function getReducedSelectedFilters(filterName: FilterName): SelectedFilters{

    if(!selectedFilters.value){
        return [];
    }

    return selectedFilters.value.filter(filter => filter.name !== filterName);

}

function setCurrentLocation(locationIndicatorData: IndicatorData){
    currentLocation.value = {
        location_id: locationIndicatorData.location_id,
        location: locationIndicatorData.location,
        location_type: locationIndicatorData.location_type,
        location_type_id: locationIndicatorData.location_type_id
    };
}

function emptyCurrentLocation(){

    currentLocation.value = null;
    comparedLocations.value = null;

}

function updateComparedLocations(locationIndicatorData: IndicatorData[]){

    if(!comparedLocations.value){
        comparedLocations.value = [];
    }

    comparedLocations.value.push(locationIndicatorData);
}

  return { 
      indicator, 
      indicatorData, 
      indicatorFilters, 
      selectedFilters,
      currentLocation,
      comparedLocations,
      locationIndicatorData,
      updateSelectedFilters, 
      getFiltersAsParams, 
      getReducedSelectedFilters,
      setCurrentLocation,
      emptyCurrentLocation,
      updateComparedLocations
    };
}); 