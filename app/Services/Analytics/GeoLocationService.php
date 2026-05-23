<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    public function resolveCity(string $ip): ?string
    {
        if ($this->isPrivateOrLocalIp($ip)) {
            return 'Local';
        }

        $url = str_replace('{ip}', $ip, (string) config('analytics.geo_api_url'));

        try {
            $response = Http::timeout((int) config('analytics.geo_timeout'))
                ->acceptJson()
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (!is_array($data)) {
                return null;
            }

            if (($data['status'] ?? null) !== 'success') {
                return null;
            }

            $city = $data['city'] ?? null;

            return is_string($city) && $city !== '' ? $city : null;
        } catch (\Throwable $exception) {
            Log::warning('Geo lookup failed.', [
                'ip' => $ip,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function isPrivateOrLocalIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
            return true;
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
