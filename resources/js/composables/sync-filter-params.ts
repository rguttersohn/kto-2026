import { watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useIndicatorsStore } from '../stores/indicators';
import { useSearchParams } from './search-params';
import { SelectedFilter } from '../types/indicators';

export function useSyncFiltersToURL() {
  const { selectedFilters } = storeToRefs(useIndicatorsStore());
  const { setParam, clearParams } = useSearchParams();

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

        setParam(`filter[${filter.filterName.value}][${filter.operator.value}]`, filter.value.value.toString());
        
      });

    },
    { deep: true }
  );
}
