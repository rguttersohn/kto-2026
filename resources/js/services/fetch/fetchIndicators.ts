import { SelectedFilters } from "../../types/indicators";

const BASE_URL = '/api/app/indicators'

export async function fetchIndicatorData( indicatorID:number, filtersAsParams:string | null, wantsGeoJSON:boolean = false){

    
    let url = `${BASE_URL}/${indicatorID}/data`;

    if(filtersAsParams){
        url += `?${filtersAsParams}`;
    }

    
    const response = await fetch(url,{
        headers:{
            'Content-Type': 'application/json',
            'Accept': wantsGeoJSON ? 'application/geo+json' : 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error(`Error fetching indicator data: ${response.statusText}`);
    }

    const data = await response.json();

    console.log(data);


}