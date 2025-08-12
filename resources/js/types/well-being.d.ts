import { SelectedFilter } from "./indicators"
import { Location } from "./locations"

export interface Domain {
    id: number,
    name: string
}


export interface WellBeingData {
    id: number,
    domain_id: number,
    year: number,
    score: number,
    location_id: number
}

/**
 * 
 * Domain by location
 */

export interface LocationDomain extends Location {

    rankings: Array<Domain>
}

/**
 * 
 * Filters applied to well-being data
 */


export type FilterNameValue = 'domain' | 'year' | 'location';

export interface WellBeingFilter extends Omit<SelectedFilter, 'filterName'> {
    filterName: {
        label: string | null,
        value: FilterNameValue | null
    }
}

