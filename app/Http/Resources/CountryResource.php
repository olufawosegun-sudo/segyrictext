<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $code = strtoupper(trim($this->iso2 ?? $this->iso3 ?? 'US'));
        $normCode = ($code === 'GB' || $code === 'GBR') ? 'UK' : (($code === 'USA') ? 'US' : $code);
        $countryConfig = config("numbers.all_services_countries.{$normCode}")
            ?? config("numbers.all_services_countries.{$code}")
            ?? [];

        $strStock = (int) $this->countryServices()->where('is_active', true)->sum('str_stock');
        $ltrStock = (int) $this->countryServices()->where('is_active', true)->sum('ltr_stock');
        $servicesCount = (int) $this->countryServices()->where('is_active', true)->count();
        $totalAvailable = $strStock > 0 ? $strStock : ($ltrStock > 0 ? $ltrStock : 9999);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'code' => $normCode,
            'dial_code' => $this->dial_code,
            'currency_code' => $this->currency_code,
            'flag_emoji' => $countryConfig['flag'] ?? $this->getFlagEmoji($this->iso2),
            'carrier' => $countryConfig['carrier'] ?? null,
            'available_numbers' => $totalAvailable,
            'str_stock' => $strStock,
            'ltr_stock' => $ltrStock,
            'services_count' => $servicesCount,
            'allowed_durations' => $countryConfig['allowed_durations'] ?? [30],
            'default_duration' => $countryConfig['default_duration'] ?? 30,
            'has_str' => $strStock > 0,
            'has_ltr' => $ltrStock > 0 || !empty($countryConfig),
            'is_best_value' => (bool) ($this->is_best_value ?? false),
            'is_active' => (bool) $this->is_active,
        ];
    }

    protected function getFlagEmoji(string $countryCode): string
    {
        $countryCode = strtoupper(trim($countryCode));
        if ($countryCode === 'UK') {
            $countryCode = 'GB';
        }
        if (strlen($countryCode) !== 2) {
            return '🌐';
        }
        $flag = '';
        foreach (str_split($countryCode) as $char) {
            $flag .= mb_chr(127397 + ord($char), 'UTF-8');
        }
        return $flag;
    }
}
