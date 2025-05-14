<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Keeping Track Online</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])    
        @endif
    </head>
    <body class="w-screen min-h-[100vh] bg-white">
        <nav class="flex flex-col justify-center items-end h-20 px-20 bg-red-500">
            <svg class="size-6 stroke-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </nav>
        <main class="flex flex-col justify-center items-center h-[calc(100vh-80px)]">
            <section x-data="searchData">
                <hgroup class="my-10">
                    <h1 class="text-4xl font-bold">Welcome to Keeping Track Online</h1>
                    <p class="text-2xl">The largest municipal-level database tracking the well-being of children.</p>
                </hgroup>
                <div
                    role="tablist"
                    class="flex ml-1"
                    >
                    <button
                        role="tab"
                        x-on:click="currentTab = 'search';results=null;query=''"
                        class="w-40 p-3 rounded-t-lg border-[1px] border-blue-400"
                        x-bind:aria-selected="currentTab === 'search' ? true : false"
                        x-bind:class="currentTab === 'search' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-400'"
                        >
                        <span>Search</span>
                    </button>
                    <button
                        role="tab"
                        x-on:click="currentTab = 'aiSearch';results=null;query=''"
                        class="w-40 p-3 rounded-t-lg border-[1px] border-blue-400"
                        x-bind:aria-selected="currentTab === 'aiSearch' ? true : false"
                        x-bind:class="currentTab === 'aiSearch' ? 'bg-blue-500 text-white': 'bg-blue-100 text-blue-400'"
                        >
                        <span>AI Search</span>
                    </button>
                </div>
                <form class="flex">
                    <label
                        x-ref="searchLabel"
                        role="tabpanel"
                        class="flex flex-col items-center">
                            <div class="flex items-center relative w-[50vw] p-6 rounded-lg bg-white border-2 border-blue-400 focus-within:border-red-500">
                                <input
                                    aria-controls=""
                                    type="text"
                                    x-model="query"
                                    x-bind:placeholder="currentPlaceholder"
                                    class="appearance-none w-11/12 p-3 text-xl focus:outline-none"
                                    x-on:keyup.enter.prevent.stop="fetchSearchResults"
                                    >
                                <div class="w-1/12">
                                    <svg x-show="searching" class="size-6 animate-spin stroke-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    <button
                                        type="button"
                                        x-show="!searching && query !== ''" 
                                        aria-label="clear search"
                                        x-on:click.prevent.stop="query = '';console.log(event.target)"
                                    >
                                        <svg
                                            aria-hidden="true"
                                            class="size-8 stroke-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </button>
                                </div>
                                <span
                                    x-text="currentSearchLabel"
                                    class="absolute inset-x-1/2 -translate-x-1/2 bottom-0 w-fit mb-1 whitespace-nowrap"></span>
                            </div>
                    </label>
                    <button
                        x-on:click.prevent="fetchSearchResults"
                        x-bind:disabled="query === '' ? true : false"
                        class="p-1 rounded-lg bg-red-500 text-white disabled:opacity-50"
                    >
                        Search
                    </button>
                </form>
                <ul
                    x-show="results"
                    x-anchor="$refs.searchLabel"
                    class="w-[50vw] h-48 overflow-y-auto px-1 py-3 bg-white shadow-lg"
                    >
                        <template
                            x-bind:key="result.id"
                            x-for="result in results"
                            >
                            <li>
                                <p
                                    x-text="result.name"
                                    class="hover:bg-blue-100"
                                    ></p>
                            </li>
                        </template>
                        <template
                            x-if="results && results.length === 0"
                            >
                            <li>
                                <p
                                    class="hover:bg-blue-100"
                                >
                                    No results
                                </p>
                            </li>
                        </template>
                </ul>
            </section>
        </main>
    </body>
</html>
