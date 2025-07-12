import { useIndicatorsStore } from './../stores/indicators';
import { useSearchParams } from './search-params';
import {watch} from 'vue';
import { storeToRefs } from 'pinia';

export function useSyncIndicatorParam(){
    
    const { indicator } = storeToRefs(useIndicatorsStore());
    const params = useSearchParams();

   watch(indicator , ()=>{

        if(indicator.value){
            
            params.setParam('indicator', indicator.value.id.toString())

            return;
        }

        if(!indicator.value){
            
            params.removeParam('indicator');
            
            return
        }

   })



}