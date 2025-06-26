
export interface LocationType {
    id: number;
    name: string;
    plural_name: string;
    slug: string;
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