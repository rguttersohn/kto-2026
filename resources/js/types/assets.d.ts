import { Point, Polygon, MultiPolygon, FeatureCollection } from "geojson";

export interface AssetCategory {
    id: number, 
    name: string
}

export interface ParentCategory {
    id: number,
    group_name: string,
    subcategories: AssetCategory[]
}

export interface Asset {
    id: number,
    description: string
}

type AllowedGeometry = Point | Polygon | MultiPolygon;

 export interface AssetFeature extends FeatureCollection<AllowedGeometry, Asset> {}
