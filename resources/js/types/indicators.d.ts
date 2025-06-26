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

type FilterName = 'timeframe' | 'location_type' | 'format' | 'breakdown';

/**
 * 
 * Type for selecting filters in a select component
 */
interface FilterSelectOption {
  name: FilterName
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
 * Selected filters for sending to the API
 * 
 */

type FilterOperators = 'eq' | 'neq' | 'gt' | 'gte' | 'lt' | 'lte' | 'in' | 'nin' | 'null' | 'notnull';

interface FilterCondition {
  id: string,
  name: FilterName
  operator: FilterOperators
  value: string | number | string[] | number[]
}

type SelectedFilters = Array<FilterCondition>;