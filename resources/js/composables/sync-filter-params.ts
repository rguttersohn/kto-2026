import { watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useIndicatorsStore } from '../stores/indicators';
import { useSearchParams } from './search-params';
import { FilterCondition } from '../types/indicators';

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

      selectedFilters.value.forEach((filter:FilterCondition) => {
        setParam(`filter[${filter.name}][${filter.operator}]`, filter.value.toString());
      });

    },
    { deep: true }
  );
}
