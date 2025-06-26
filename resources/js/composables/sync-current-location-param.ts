import { useIndicatorsStore } from './../stores/indicators';
import { useSearchParams } from './search-params';
import {watch} from 'vue';
import { storeToRefs } from 'pinia';

export function useSyncCurrentLocationParam(){
    
    const { currentLocation } = storeToRefs(useIndicatorsStore());
    const params = useSearchParams();

   watch(currentLocation, ()=>{

        if(currentLocation.value){
            
            params.setParam('current-location', currentLocation.value.location_id.toString())

            return;
        }

        if(!currentLocation.value){
            
            params.removeParam('current-location');
            
            return
        }

   })



}