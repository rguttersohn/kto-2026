import { FeatureCollection, Polygon, MultiPolygon} from 'geojson';
import { LocationType } from './locations';

/***
 * 
 * Indicators
 * 
 */

export interface IndicatorData {
  data: number;
  location_id: number;
  location: string;
  location_type_id: number;
  location_type: string;
  timeframe: string;
  breakdown: string;
  format: string;
}

type AllowedGeometry = Polygon | MultiPolygon;

export interface IndicatorFeature extends FeatureCollection<AllowedGeometry, IndicatorData> {}

export interface Indicator {
  id: number;
  name: string;
  slug: string;
  definition: string;
  source: string;
  note: string;
}

export interface Breakdown {
    name: string, 
    id: number
    sub_breakdowns: SubBreakdown[]
}

interface SubBreakdown {
    id: number;
    name: string;
}

/**
 *  Filters
 */

export interface IndicatorFilters {
  timeframe: number[];
  location_type: LocationType[];
  format:Array<{
    name: string, 
    id: number
  }>;
  breakdown: Array<Breakdown>;
}

type FilterNameValue = 'timeframe' | 'location_type' | 'format' | 'breakdown' | 'data';

/**
 * 
 * Type for selecting filters in a select component
 */
interface FilterSelectOption {
  name: FilterNameValue
  value: number | string,
  label: number | string
}

interface FilterGroupSelectOption {
  groupLabel: string, 
  value: string | number, 
  items: FilterSelectOption[]
}


/**
 * 
 * Filters types for the query builder
 * 
 */


type FilterOperators = 'eq' | 'neq' | 'gt' | 'gte' | 'lt' | 'lte' | 'in' | 'nin' | 'null' | 'notnull';


export interface SelectedFilter {
  
  id: string,
  filterName: {
      label:  string | null,
      value: FilterNameValue | null
  },
  operator: {
      label: string | null,
      value: FilterOperators | null
  },
  value: {
      label: string | number | null, 
      value: number | string | null
  }

}

export interface SelectedSort {
  id: string;
  sortField: {
    label: string;
    value: string;
  };
  direction: 'asc' | 'desc';
}