import { defineStore } from 'pinia';
import type { Indicator, IndicatorFeature, IndicatorData, SelectedFilters, FilterSelectOption, FilterGroupSelectOption, IndicatorFilters, FilterName, QueryBuilderContainer } from '../types/indicators';
import { ref, shallowRef, computed} from 'vue';

export const useIndicatorsStore = defineStore('indicators', () => {
  
  const indicator = ref<Indicator | null>(null);

  const indicatorData = shallowRef<IndicatorFeature | IndicatorData[] |null>(null);

  const indicatorDataCount = ref<number>();

  const queryOffset = ref<number>(0);

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

function removeComparedLocation(locationID: number){

    if(!comparedLocations.value){
        return;
    }

    const matchingIndex = comparedLocations.value.findIndex(comparison=>comparison[0].location_id === locationID);

    if(matchingIndex !== -1){
      
          comparedLocations.value.splice(matchingIndex, 1);

    }

}

function emptyComparedLocations(){

    if(!comparedLocations.value){
        return;
    }

    comparedLocations.value = null;

}

const timeframeOptions = computed(():Array<FilterSelectOption>=>{

    
    if(!indicatorFilters.value){

        return [];
    }
    
    return indicatorFilters.value.timeframe.map(t=>({
        name: 'timeframe',
        value: t,
        label: t
    }))

});


const locationTypeOptions = computed(():Array<FilterSelectOption>=>{
    
    if(!indicatorFilters.value){

        return [];
    }

    return indicatorFilters.value.location_type.map(location=>({
        name:'location_type',
        value: location.id,
        label: location.plural_name
    }))
})


const formatOptions = computed(():Array<FilterSelectOption>=>{

    if(!indicatorFilters.value){

        return [];

    }

    return indicatorFilters.value.format.map(f=>({
        name: 'format',
        value: f.id,
        label: f.name
    }))

})



const breakdownOptions = computed(():Array<FilterGroupSelectOption>=>{

    if(!indicatorFilters.value){
        
        return [];

    }

    return indicatorFilters.value.breakdown.map(b=>({
            groupLabel: b.name,
            value: b.id,
            items: b.sub_breakdowns.map(sub=>({
                name: 'breakdown',
                label: sub.name,
                value: sub.id
            }))
        }))

})


const queryContainer = ref<QueryBuilderContainer[]>([]);


function generateQueryContainer(): QueryBuilderContainer{

    return {
        id: crypto.randomUUID(),
        filterName: {
            label:  null,
            value: null
        },
        operator: {
            label: null,
            value: null
        },
        value: {
            label: null, 
            value: null
        }
    }
}


  return { 
      indicator, 
      indicatorData, 
      indicatorDataCount,
      queryOffset, 
      indicatorFilters, 
      selectedFilters,
      currentLocation,
      comparedLocations,
      locationIndicatorData,
      timeframeOptions,
      locationTypeOptions,
      formatOptions,
      breakdownOptions,
      queryContainer,
      updateSelectedFilters, 
      getFiltersAsParams, 
      getReducedSelectedFilters,
      setCurrentLocation,
      emptyCurrentLocation,
      updateComparedLocations,
      removeComparedLocation,
      emptyComparedLocations,
      generateQueryContainer
    };
}); 