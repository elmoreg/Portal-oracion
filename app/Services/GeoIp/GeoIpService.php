<?php

namespace App\Services\GeoIp;

use MaxMind\Db\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIpService
{
    private ?Reader $reader = null;

    private bool $attemptedOpen = false;

    public function resolve(?string $ip): ?GeoLocation
    {
        if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP) || $this->isPrivateOrReserved($ip)) {
            return null;
        }

        $reader = $this->reader();

        if (! $reader) {
            return null;
        }

        try {
            /** @var array<string, mixed>|null $data */
            $data = $reader->get($ip);
        } catch (InvalidDatabaseException) {
            return null;
        }

        if (! $data || empty($data['country_code'])) {
            return null;
        }

        $countryCode = strtoupper((string) $data['country_code']);

        return new GeoLocation(
            countryCode: $countryCode,
            countryName: $this->countryName($countryCode),
            city: $this->cleanString($data['city'] ?? null),
            region: $this->cleanString($data['state1'] ?? null),
        );
    }

    private function reader(): ?Reader
    {
        if ($this->attemptedOpen) {
            return $this->reader;
        }

        $this->attemptedOpen = true;

        $path = file_exists(config('geoip.city_database_path'))
            ? config('geoip.city_database_path')
            : config('geoip.country_database_path');

        if (! $path || ! file_exists($path)) {
            return null;
        }

        try {
            $this->reader = new Reader($path);
        } catch (InvalidDatabaseException) {
            $this->reader = null;
        }

        return $this->reader;
    }

    private function countryName(string $isoCode): string
    {
        static $countries = null;

        $countries ??= file_exists(config('geoip.countries_es_path'))
            ? require config('geoip.countries_es_path')
            : [];

        return $countries[$isoCode] ?? $isoCode;
    }

    private function cleanString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    private function isPrivateOrReserved(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
