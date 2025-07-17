import { IndicatorData } from './../types/indicators.d';
import { setActivePinia, createPinia } from 'pinia';
import { useIndicatorsStore } from './indicators';
import { beforeEach, describe, it, expect } from 'vitest';


describe('useIndicatorStore', ()=>{

    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('converts filters to params string', ()=>{


            const indicator = useIndicatorsStore();

            indicator.selectedFilters = [{
                id:crypto.randomUUID(),
                filterName: {
                    label: 'Timeframe',
                    value: 'timeframe'
                },
                operator: {
                    label: 'Equals',
                    value: 'eq'
                },
                value: {
                    label: 2020,
                    value: 2020
                }
            }];

            const result = indicator.getFiltersAsParams(indicator.selectedFilters);

            const resultDecoded = decodeURIComponent(result);

            expect(resultDecoded).toBe('filter[timeframe][eq]=2020');
    
    });

    it('converts selected filters array with value.label and value.value array to a param string', ()=>{

        const indicator = useIndicatorsStore();

        indicator.selectedFilters = [
            {
                id: crypto.randomUUID(),
                filterName: { label: 'Year', value: 'timeframe' },
                operator: { label: 'In', value: 'in' },
                value: { label: [2020, 2021], value: [2020, 2021] }
            }
        ];

        const result = indicator.getFiltersAsParams(indicator.selectedFilters);

        const resultDecoded = decodeURIComponent(result);

        expect(resultDecoded).toBe('filter[timeframe][in][]=2020&filter[timeframe][in][]=2021');

    });


    it('converts selected filters where value.value and value.label are strings or an array to a param string', ()=>{

        const indicator = useIndicatorsStore();

        indicator.selectedFilters = [
            {
                id:crypto.randomUUID(),
                filterName: {
                    label: 'Location Type',
                    value: 'location_type'
                },
                operator: {
                    label: 'Equals',
                    value: 'eq'
                },
                value: {
                    label: 'Council District',
                    value: 1
                }
            },
            {
                id: crypto.randomUUID(),
                filterName: { label: 'Year', value: 'timeframe' },
                operator: { label: 'In', value: 'in' },
                value: { label: [2020, 2021], value: [2020, 2021] }
            }
        ];

        const result = indicator.getFiltersAsParams(indicator.selectedFilters);

        const resultDecoded = decodeURIComponent(result);

        expect(resultDecoded).toBe('filter[location_type][eq]=1&filter[timeframe][in][]=2020&filter[timeframe][in][]=2021');

    });

    it('converts empty selected filters array to an empty string', ()=>{

        const indicator = useIndicatorsStore();

        indicator.selectedFilters = [];

        const result = indicator.getFiltersAsParams(indicator.selectedFilters);

        const resultDecoded = decodeURIComponent(result);

        expect(resultDecoded).toBe('');

    });


    it('skips selected filter objects with missing values', ()=>{

        const indicator = useIndicatorsStore();

        indicator.selectedFilters = [
            {
                id:crypto.randomUUID(),
                filterName: {
                    label: 'Location Type',
                    value: 'location_type'
                },
                operator: {
                    label: 'Equals',
                    value: 'eq'
                },
                value: {
                    label: 'Council District',
                    value: 1
                }
            },
            {
                id: crypto.randomUUID(),
                filterName: { label: 'Year', value: 'timeframe' },
                operator: { label: 'In', value: 'in' },
                value: {
                    label: null,
                    value: null
                }
            }
        ];

        const result = indicator.getFiltersAsParams(indicator.selectedFilters);

        const resultDecoded = decodeURIComponent(result);

        expect(resultDecoded).toBe('filter[location_type][eq]=1');

    });

})

describe('timeframeOptions', ()=>{

    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('returns an array of timeframe filter options', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: []
        };

        const result = indicator.timeframeOptions;

        expect(result).toStrictEqual([
                {
                label: 2020,
                name: "timeframe",
                value: 2020
            },
            {
                label: 2021,
                name: "timeframe",
                value: 2021
            }
    
        ]);

    })


    it('returns an empty array if timeframe array is empty', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [],
            location_type: [],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: []
        };

        const result = indicator.timeframeOptions;

        expect(result).toStrictEqual([]);

    })

    it('returns an empty array if indicator filters is null', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = null;

        const result = indicator.timeframeOptions;

        expect(result).toStrictEqual([]);

    })

})

describe('locationTypeOptions', ()=>{

    beforeEach(()=>{

        setActivePinia(createPinia());

    })


    it('returns an array of location type options', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [
                {
                    id: 1,
                    name: 'City Council District',
                    plural_name: 'City Council Districts',
                    classification: 'political',
                    scope: 'local'
                }
            ],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: []
        };


        const result = indicator.locationTypeOptions;

        expect(result).toStrictEqual([
            {   
                name: 'location_type',
                label: indicator.indicatorFilters.location_type[0].plural_name,
                value: indicator.indicatorFilters.location_type[0].id,
            }
        ])

    })

    it('returns an en empty array if location type filters is empty', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: []
        };


        const result = indicator.locationTypeOptions;

        expect(result).toStrictEqual([])

    })

    it('returns an en empty array if indicat filters is null', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = null;

        const result = indicator.locationTypeOptions;

        expect(result).toStrictEqual([]);

    })


})

describe('formatOptions', ()=>{


    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('returns an array of format options', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [
                {
                    id: 1,
                    name: 'City Council District',
                    plural_name: 'City Council Districts',
                    classification: 'political',
                    scope: 'local'
                }
            ],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: []
        };


        const result = indicator.formatOptions;

        expect(result).toStrictEqual([
            {   
                name: 'format',
                label: indicator.indicatorFilters.format[0].name,
                value: indicator.indicatorFilters.format[0].id,
            },
            {   
                name: 'format',
                label: indicator.indicatorFilters.format[1].name,
                value: indicator.indicatorFilters.format[1].id,
            }
        ])

    })


    it('returns an en empty array if format filters is empty', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [],
            format: [],
            breakdown: []
        };


        const result = indicator.locationTypeOptions;

        expect(result).toStrictEqual([])

    })


    it('returns an en empty array if indicator filters are null', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = null;

        const result = indicator.locationTypeOptions;

        expect(result).toStrictEqual([])

    })



})

describe('breakdownOptions', ()=>{


    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('returns an array of breakdown options', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [
                {
                    id: 1,
                    name: 'City Council District',
                    plural_name: 'City Council Districts',
                    classification: 'political',
                    scope: 'local'
                }
            ],
            format: [
                {
                    name:'Percent',
                    id: 2
                },
                {
                    name: 'Number',
                    id: 1
                }
            ],
            breakdown: [
                {
                    "name":"Race/Ethnicity","id":7,
                    "sub_breakdowns":[
                        {"id":8,"name":"Black"},
                        {"id":9,"name":"White"},
                    ]}
                ]
            };


        const result = indicator.breakdownOptions;

        expect(result).toStrictEqual([
            {
                groupLabel: indicator.indicatorFilters.breakdown[0].name,
                value: indicator.indicatorFilters.breakdown[0].id,
                items: [
                    {   
                        name: 'breakdown',
                        label: indicator.indicatorFilters.breakdown[0].sub_breakdowns[0].name,
                        value: indicator.indicatorFilters.breakdown[0].sub_breakdowns[0].id,
                    },
                    {   
                        name: 'breakdown',
                        label: indicator.indicatorFilters.breakdown[0].sub_breakdowns[1].name,
                        value: indicator.indicatorFilters.breakdown[0].sub_breakdowns[1].id,
                    } 
                ]
            }       
        ])

    })


    it('returns an en empty array if breakdown filters is empty', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = {
            timeframe: [2020,2021],
            location_type: [],
            format: [],
            breakdown: []
        };


        const result = indicator.breakdownOptions;

        expect(result).toStrictEqual([])

    })


    it('returns an en empty array if indicator filters are null', ()=>{

        const indicator = useIndicatorsStore();

        indicator.indicatorFilters = null;

        const result = indicator.breakdownOptions;

        expect(result).toStrictEqual([])

    })



})


describe('updateComparedLocations', ()=>{

    beforeEach(()=>{

        setActivePinia(createPinia());

    })



    it('updates compared locations array with a new compared location objection', ()=>{

        const indicator = useIndicatorsStore();

        indicator.comparedLocations = [
            [
                {   
                    data: 20,
                    location_id: 1,
                    location: 'East Village',
                    location_type: 'Neighborhood Tabulation Area',
                    location_type_id: 2,
                    timeframe: '2020',
                    breakdown: 'population',
                    format: 'number'
                },
            ]
        ];

        const newComparedLocations: IndicatorData[] = [
                {   
                    data: 22,
                    location_id: 3,
                    location: 'Harlem',
                    location_type: 'Neighborhood Tabulation Area',
                    location_type_id: 2,
                    timeframe: '2020',
                    breakdown: 'population',
                    format: 'number'
                }
            ]

        indicator.updateComparedLocations(newComparedLocations);

        expect(indicator.comparedLocations).toStrictEqual([
                [
                    {   
                        data: 20,
                        location_id: 1,
                        location: 'East Village',
                        location_type: 'Neighborhood Tabulation Area',
                        location_type_id: 2,
                        timeframe: '2020',
                        breakdown: 'population',
                        format: 'number'
                    },
                ],
                [
                    {   
                        data: 22,
                        location_id: 3,
                        location: 'Harlem',
                        location_type: 'Neighborhood Tabulation Area',
                        location_type_id: 2,
                        timeframe: '2020',
                        breakdown: 'population',
                        format: 'number'
                    }
                ]

            ]
        )

    })


})

describe('removeComparedLocation', ()=>{

    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('removes a compared location from compared location array', ()=>{

        const indicator = useIndicatorsStore();

        indicator.comparedLocations = [
                [
                    {   
                        data: 20,
                        location_id: 1,
                        location: 'East Village',
                        location_type: 'Neighborhood Tabulation Area',
                        location_type_id: 2,
                        timeframe: '2020',
                        breakdown: 'population',
                        format: 'number'
                    },
                ],
                [
                    {   
                        data: 22,
                        location_id: 3,
                        location: 'Harlem',
                        location_type: 'Neighborhood Tabulation Area',
                        location_type_id: 2,
                        timeframe: '2020',
                        breakdown: 'population',
                        format: 'number'
                    }
                ]

            ];
        
        indicator.removeComparedLocation(1);

        expect(indicator.comparedLocations).toStrictEqual([
            [
                {   
                    data: 22,
                    location_id: 3,
                    location: 'Harlem',
                    location_type: 'Neighborhood Tabulation Area',
                    location_type_id: 2,
                    timeframe: '2020',
                    breakdown: 'population',
                    format: 'number'
                }
            ]
        ]);
        

    })
})