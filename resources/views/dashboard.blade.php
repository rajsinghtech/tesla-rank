@section('vendor-scripts')
    @vite('resources/js/leaflet.js')
@endsection

@section('page-styles')
    <style>

    </style>
@endsection

@section('page-scripts')
    <script>
        window.locations = @json($locations);
        window.destinations = @json($destinations);
        window.speeds = @json($speeds);
        window.directions = @json($directions);
    </script>
@endsection

<x-app-layout>

    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4">
                <div class="leaflet-map" id="map" style="height: 500px; border-radius: 5px;"></div>

                <livewire:map.time-slider />

                {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <x-welcome />
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
