<?php

namespace App\Services\GeoIp;

final readonly class GeoLocation
{
    public function __construct(
        public ?string $countryCode,
        public ?string $countryName,
        public ?string $city = null,
        public ?string $region = null,
    ) {}

    public function zoneLabel(): string
    {
        $parts = array_filter([$this->city, $this->region, $this->countryName]);

        return $parts === [] ? 'Zona desconocida' : implode(', ', $parts);
    }
}
