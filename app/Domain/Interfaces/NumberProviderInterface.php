<?php

namespace App\Domain\Interfaces;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\DTOs\ProviderNumberDTO;
use App\Models\Country;
use App\Models\Service;

interface NumberProviderInterface
{
    /**
     * Check if provider has active inventory for given country & service.
     */
    public function checkAvailability(Country $country, Service $service): bool;

    /**
     * Reserve a real or virtual phone number from provider.
     */
    public function reserveNumber(Country $country, Service $service): ProviderNumberDTO;

    /**
     * Poll incoming SMS messages for an active provider reservation.
     *
     * @return array<IncomingSmsDTO>
     */
    public function getMessages(string $providerNumberId): array;

    /**
     * Release or cancel a phone number session.
     */
    public function releaseNumber(string $providerNumberId): bool;
}
