import { defineStore } from 'pinia';
import type { Indicator, IndicatorFeature, IndicatorData, SelectedFilter, FilterSelectOption, FilterGroupSelectOption, IndicatorFilters, FilterNameValue, FilterOperators} from '../types/indicators';
import { ref, shallowRef, computed } from 'vue';

export const useIndicatorsStore = defineStore('indicators', () => {

  const indicator = ref<Indicator | null>(null);

  function resetIndicator(){
    indicator.value = null;
  }

  const indicatorData = shallowRef<IndicatorFeature | IndicatorData[] | null>(null);

  function emptyIndicatorData(){

    indicatorData.value = null;

  }

  const indicatorDataCount = ref<number>();

  function emptyIndicatorDataCount(){

    indicatorDataCount.value = 0;

  }

  const selectedFilters = ref<SelectedFilter[]>([]);

  function emptySelectedFilters(){

    selectedFilters.value = [];

  }

  const queryOffset = ref<number>(0);

  const indicatorFilters = ref<IndicatorFilters | null>(null);

  const currentLocation = ref<Pick<IndicatorData, 'location_id' | 'location' | 'location_type' | 'location_type_id'> | null>(null);

  const locationIndicatorData = shallowRef<IndicatorData[] | null>(null);

  const comparedLocations = ref<IndicatorData[][] | null>(null);

  function getFiltersAsParams(selectedFilters: SelectedFilter[]): string {
   
    const params = new URLSearchParams();
   
    selectedFilters.forEach(filter => {
        
        const name = filter.filterName.value;
        const operator = filter.operator.value;
        const value = filter.value.value;

        if (!name || !operator || value === null) {

            return;

        }

      if (Array.isArray(value)) {
        value.forEach(val => {
          params.append(`filter[${name}][${operator}][]`, val.toString());
        });
      } else {
        params.append(`filter[${name}][${operator}]`, value.toString());
      }
    });
    
    return params.toString();

  }

  function getParamsAsFilters(queryString: string): SelectedFilter[] {
    const params = new URLSearchParams(queryString);
    const grouped: Record<string, Record<string, (string | number)[]>> = {};
  
    // Step 1: Group values by filter name and operator
    for (const [key, value] of params.entries()) {
      const match = key.match(/^filter\[(\w+)\]\[(\w+)\](?:\[\d+\])?$/);
      if (!match) continue;
  
      const [, name, operator] = match;
      const parsedValue = isNaN(Number(value)) ? value : Number(value);
  
      if (!grouped[name]) grouped[name] = {};
      if (!grouped[name][operator]) grouped[name][operator] = [];
  
      grouped[name][operator].push(parsedValue);
    }
  
    // Step 2: Convert grouped data into SelectedFilter[]
    const selectedFilters: SelectedFilter[] = [];
  
    Object.entries(grouped).forEach(([name, operators]) => {
      Object.entries(operators).forEach(([operator, values]) => {
        const multi = values.length > 1;
        const value: SelectedFilter['value'] = {
          label: multi ? values : values[0],
          value: multi ? values : values[0]
        };
  
        selectedFilters.push({
          id: crypto.randomUUID(),
          filterName: {
            label: null,
            value: name as FilterNameValue
          },
          operator: {
            label: null,
            value: operator as FilterOperators
          },
          value
        });
      });
    });
  
    return selectedFilters;
  }
  

  function getReducedSelectedFilters(filterNameValue: FilterNameValue): SelectedFilter[] {

        return selectedFilters.value.filter(filter=>filter.filterName.value !== filterNameValue);
  }

  function setCurrentLocation(locationIndicatorData: IndicatorData) {
    currentLocation.value = {
      location_id: locationIndicatorData.location_id,
      location: locationIndicatorData.location,
      location_type: locationIndicatorData.location_type,
      location_type_id: locationIndicatorData.location_type_id
    };
  }

  function emptyCurrentLocation() {
    currentLocation.value = null;
    comparedLocations.value = null;
  }

  function updateComparedLocations(locationIndicatorData: IndicatorData[]) {
    if (!comparedLocations.value) {
      comparedLocations.value = [];
    }

    comparedLocations.value.push(locationIndicatorData);
  }

  function removeComparedLocation(locationID: number) {
    if (!comparedLocations.value) {
      return;
    }

    const matchingIndex = comparedLocations.value.findIndex(comparison => comparison[0].location_id === locationID);

    if (matchingIndex !== -1) {
      comparedLocations.value.splice(matchingIndex, 1);
    }
  }

  function emptyComparedLocations() {
    if (!comparedLocations.value) {
      return;
    }

    comparedLocations.value = null;
  }

  const timeframeOptions = computed((): Array<FilterSelectOption> => {
    if (!indicatorFilters.value) {
      return [];
    }

    return indicatorFilters.value.timeframe.map(t => ({
      name: 'timeframe',
      value: t,
      label: t
    }));
  });

  const locationTypeOptions = computed((): Array<FilterSelectOption> => {
    if (!indicatorFilters.value) {
      return [];
    }

    return indicatorFilters.value.location_type.map(location => ({
      name: 'location_type',
      value: location.id,
      label: location.plural_name
    }));
  });

  const formatOptions = computed((): Array<FilterSelectOption> => {
    if (!indicatorFilters.value) {
      return [];
    }

    return indicatorFilters.value.format.map(f => ({
      name: 'format',
      value: f.id,
      label: f.name
    }));
  });

  const breakdownOptions = computed((): Array<FilterGroupSelectOption> => {
    if (!indicatorFilters.value) {
      return [];
    }

    return indicatorFilters.value.breakdown.map(b => ({
      groupLabel: b.name,
      value: b.id,
      items: b.sub_breakdowns.map(sub => ({
        name: 'breakdown',
        label: sub.name,
        value: sub.id
      }))
    }));
  });


  function generateFilterContainer(): SelectedFilter {
    return {
      id: crypto.randomUUID(),
      filterName: {
        label: null,
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
    };
  }

  function resetStore(){
    resetIndicator();
    emptyIndicatorData();
    emptyIndicatorData();
    emptySelectedFilters();
    emptyCurrentLocation();
    emptyComparedLocations();
  }


  return {
    indicator,
    resetIndicator,
    indicatorData,
    emptyIndicatorData,
    indicatorDataCount,
    emptyIndicatorDataCount,
    queryOffset,
    indicatorFilters,
    selectedFilters,
    emptySelectedFilters,
    currentLocation,
    comparedLocations,
    locationIndicatorData,
    timeframeOptions,
    locationTypeOptions,
    formatOptions,
    breakdownOptions,
    getFiltersAsParams,
    getParamsAsFilters,
    getReducedSelectedFilters,
    setCurrentLocation,
    emptyCurrentLocation,
    updateComparedLocations,
    removeComparedLocation,
    emptyComparedLocations,
    generateFilterContainer,
    resetStore
    };
});
