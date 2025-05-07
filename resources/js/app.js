import Alpine from 'alpinejs'
import anchor from '@alpinejs/anchor'
 
window.Alpine = Alpine

Alpine.plugin(anchor);

document.addEventListener('alpine:init', function(){

    Alpine.data('searchData', ()=>({
        currentTab: 'search',
        query: '',
        results: null,
        searching: false,
        tabs: {
            search: {
                url: '/api/search',
                placeholder: 'Try \'population\' or \'housing\' ',
                searchLabel: 'Search using keywords'
            },
            aiSearch:{
                url: '/api/ai-search',
                placeholder:'Try \'Where is rent most expensive?\'',
                searchLabel: 'Search with AI'
            }
        },
        async fetchSearchResults(event){
            console.log(this.query);
            console.log(event.target);
            this.searching = true;
            
            const response = await fetch(`${this.currentTabURL}/?search=${this.query}`);
            
            if(!response.ok){

                const data = response.json();

                console.error(data.error.message);
            }

            const data = await response.json();

            this.results = data.data.indicators;

            this.searching = false;

            this.query = '';
            
        },
        get currentTabURL(){
            return this.tabs[this.currentTab].url;
        },
        get currentPlaceholder(){
            return this.tabs[this.currentTab].placeholder;
        },
        get currentSearchLabel(){
            return this.tabs[this.currentTab].searchLabel;
        },
        aiSearchPrompts:[
            'Try \'Show me indicators on baby and maternal health\'',
            'Try \'Show me indicators on youth justice\'',
            'Try \'I want data on students and economic security\'',
            'Try \'Where is rent most expensive?\''
        ]
    }))
})
 
Alpine.start();
