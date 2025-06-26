import { defineStore } from 'pinia';
import type { Indicator, IndicatorFeature, IndicatorData, SelectedFilters, FilterSelectOption, IndicatorFilters, FilterName } from '../types/indicators';
import { ref, shallowRef } from 'vue';


export const useIndicatorsStore = defineStore('indicators', () => {
  
  const indicator = ref<Indicator | null>(null);

  const indicatorData = shallowRef<IndicatorFeature | null>(null);

  const selectedFilters = ref<SelectedFilters>([]);

  const indicatorFilters = ref<IndicatorFilters | null>(null);

  const locationIndicatorData = shallowRef<IndicatorData[] | null>(null);

  function updateSelectedFilters(filterSelectOption: FilterSelectOption){

    const matchingFilterIndex = selectedFilters.value.findIndex(filterCondition=>filterCondition.name === filterSelectOption.name);

    if(matchingFilterIndex !== -1){
      
          selectedFilters.value.splice(matchingFilterIndex, 1);

    }

    selectedFilters.value.push({
          name: filterSelectOption.name,
          operator: 'eq',
          value: filterSelectOption.value
    })
 
 }

 function getFiltersAsParams(selectedFilters: SelectedFilters):string | null{

    if(selectedFilters.length === 0){
        return '';
    }

    return selectedFilters.map(filter => {
        return `filter[${filter.name}][${filter.operator}]=${Array.isArray(filter.value) ?
            filter.value.join(',') : filter.value}`;
    }).join('&');

}

function getReducedSelectedFilters(filterName: FilterName): SelectedFilters{

    return selectedFilters.value.filter(filter => filter.name !== filterName);

}


  return { indicator, indicatorData, indicatorFilters, selectedFilters, locationIndicatorData, updateSelectedFilters, getFiltersAsParams, getReducedSelectedFilters};
}); 