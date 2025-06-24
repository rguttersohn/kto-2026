import { FeatureCollection, Geometry, Polygon, MultiPolygon} from 'geojson';
import { LocationType } from './locations';

export interface IndicatorData {
  data: number;
  location_id: number;
  location: string;
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

export interface IndicatorFilters {
  timeframe: number[];
  location_type: LocationType[];
  format?:Array<{
    name: string, 
    id: number
  }>;
  breakdown?: string[];
}

export interface Breakdown {
    name: string, 
    id: number
    sub_breakdowns?: SubBreakdown[]
}


interface SubBreakdown {
    id: number;
    name: string;
}