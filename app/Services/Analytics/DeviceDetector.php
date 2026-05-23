<?php

namespace App\Services\Analytics;

class DeviceDetector
{
    public function detect(?string $userAgent, ?string $clientHint = null): string
    {
        if ($clientHint !== null && $clientHint !== '') {
            return strtolower($clientHint);
        }

        $userAgent = strtolower($userAgent ?? '');

        if ($userAgent === '') {
            return 'unknown';
        }

        if (preg_match('/bot|crawl|spider|slurp|facebookexternalhit/i', $userAgent)) {
            return 'bot';
        }

        if (preg_match('/mobile|android|iphone|ipod|windows phone/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/ipad|tablet|kindle|playbook/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
