<?php

namespace App\Infrastructure\Providers;

use App\Services\PvaDeals\PvaDealsClient as BasePvaDealsClient;

/**
 * Compatibility wrapper for existing application calls.
 * Inherits all robust client features, rate limiting, and normalizations.
 */
class PvaDealsClient extends BasePvaDealsClient
{
    // Inherits all methods from App\Services\PvaDeals\PvaDealsClient
}
