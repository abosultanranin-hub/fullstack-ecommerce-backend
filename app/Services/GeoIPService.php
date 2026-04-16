<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoIPService
{
    /**
     * Get country code from IP address.
     * 
     * @param string $ip
     * @return string|null
     */
    public static function getCountryFromIP($ip)
    {
        // For local development, skip real check if IP is local
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'PS'; // Default to Palestine for testing as per user examples
        }

        try {
            return \Illuminate\Support\Facades\Cache::remember("geoip_{$ip}", 60 * 24 * 30, function () use ($ip) {
                $response = Http::get("http://ip-api.com/json/{$ip}");
                if ($response->successful()) {
                    return $response->json('countryCode');
                }
                return null;
            });
        } catch (\Exception $e) {
            Log::error("GeoIP Error: " . $e->getMessage());
        }

        return null;
    }
}
