import { Polygon, MultiPolygon, FeatureCollection } from "geojson";

export interface LocationType {
    id: number;
    name: string;
    plural_name: string;
    classification: string;
    scope: string;
    locations?: Location[];
}

export interface Location {
    id: number;
    name: string;
    fips: string | null;
    geopolitical_id: number | null;
}

type AllowedGeometry = Polygon | MultiPolygon;



export interface LocationFeature extends FeatureCollection<AllowedGeometry, Location>{}