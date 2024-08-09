<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if (Request::getHost() == 'mtenancy_custom_multi_db.test')
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3>Team URL:</h3>
                        {{ Auth::user()->tenant->subdomain }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
