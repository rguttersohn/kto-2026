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