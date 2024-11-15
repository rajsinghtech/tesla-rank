<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $locations = DB::table('telemetry_data')->selectRaw("
            vin,
            created_at,
            (SELECT data_item->'value'->'location_value'->>'latitude'
            FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
            WHERE data_item->>'key' = 'Location')::float AS latitude,
            (SELECT data_item->'value'->'location_value'->>'longitude'
            FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
            WHERE data_item->>'key' = 'Location')::float AS longitude
        ")
            ->whereRaw("
            EXISTS (
                SELECT 1
                FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
                WHERE data_item->>'key' = 'Location'
                AND data_item->'value'->'location_value'->>'latitude' IS NOT NULL
                AND data_item->'value'->'location_value'->>'longitude' IS NOT NULL
            )
        ")
            ->whereDay('created_at', now()->day)
            ->orderByRaw('
            vin ASC, created_at DESC, data_hash ASC;
        ')->get();

        $destinations = DB::table('telemetry_data')->selectRaw("
            vin,
            created_at,
            (SELECT data_item->'value'->'location_value'->>'latitude'
            FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
            WHERE data_item->>'key' = 'DestinationLocation')::float AS latitude,
            (SELECT data_item->'value'->'location_value'->>'longitude'
            FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
            WHERE data_item->>'key' = 'DestinationLocation')::float AS longitude
        ")
            ->whereRaw("
            EXISTS (
                SELECT 1
                FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
                WHERE data_item->>'key' = 'DestinationLocation'
                AND data_item->'value'->'location_value'->>'latitude' IS NOT NULL
                AND data_item->'value'->'location_value'->>'longitude' IS NOT NULL
            )
        ")
            ->whereDay('created_at', now()->day)
            ->orderByRaw('
            vin ASC, created_at DESC, data_hash ASC;
        ')->get();

        $speeds = DB::table('telemetry_data')->selectRaw("
            vin,
            created_at,
            (SELECT data_item->'value'->>'string_value'
            FROM jsonb_array_elements(data->'data') AS data_item
            WHERE data_item->>'key' = 'VehicleSpeed') AS battery_range
        ")
            ->whereRaw("
            EXISTS (
                SELECT 1
                FROM jsonb_array_elements(telemetry_data.data->'data') AS data_item
                WHERE data_item->>'key' = 'VehicleSpeed'
                AND data_item->'value'->>'string_value' IS NOT NULL
            )
        ")
            ->whereDay('created_at', now()->day)
            ->orderByRaw('
            vin ASC, created_at DESC, data_hash ASC;
        ')->get();

        return view('dashboard', compact('locations', 'destinations'));
    }
}
