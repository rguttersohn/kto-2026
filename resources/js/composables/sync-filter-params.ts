import { watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useIndicatorsStore } from '../stores/indicators';
import { useSearchParams } from './search-params';
import { SelectedFilter } from '../types/indicators';

export function useSyncFiltersToURL() {
  const { selectedFilters } = storeToRefs(useIndicatorsStore());
  const { setParam, clearParams, appendParam } = useSearchParams();

  watch(
    selectedFilters,
    () => {
        
      clearParams(/^filter\[/);
      
      if(!selectedFilters.value){
        return;
      }

      selectedFilters.value.forEach((filter:SelectedFilter) => {

        if(!filter.value.value){

           return;
        }

        /**
         * 
         * if (Array.isArray(value)) {
        value.forEach(val => {
          params.append(`filter[${name}][${operator}][]`, val.toString());
        });
      } else {
        params.append(`filter[${name}][${operator}]`, value.toString());
      }
         */

        if(Array.isArray(filter.value.value) ){
          
          filter.value.value.forEach(val => {
            
            appendParam(`filter[${filter.filterName.value}][${filter.operator.value}][]`, val.toString());

          })
          
          return;
        }

        appendParam(`filter[${filter.filterName.value}][${filter.operator.value}]`, filter.value.value.toString());
        
      });

    },
    { deep: true }
  );
}
