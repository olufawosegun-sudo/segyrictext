<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'first_name', 'middle_name', 'last_name', 'email', 'phone', 'password', 'status', 'country_code', 'currency_code', 'email_verified_at', 'phone_verified_at', 'last_login_at', 'remember_token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Map user signup country to default currency code.
     */
    public function resolveSignupCurrencyCode(): string
    {
        if (!empty($this->currency_code)) {
            return strtoupper($this->currency_code);
        }

        $countryCode = strtoupper($this->country_code ?? '');

        // Standard ISO Country -> Currency mappings for target markets
        $countryCurrencyMap = [
            'NG' => 'NGN',
            'KE' => 'KES',
            'GH' => 'GHS',
            'ZA' => 'ZAR',
            'UG' => 'UGX',
            'EG' => 'EGP',
            'TZ' => 'TZS',
            'RW' => 'RWF',
            'US' => 'USD',
            'GB' => 'GBP',
            'CA' => 'CAD',
            'FR' => 'EUR',
            'DE' => 'EUR',
            'NL' => 'EUR',
        ];

        if (isset($countryCurrencyMap[$countryCode])) {
            return $countryCurrencyMap[$countryCode];
        }

        // Try looking up in countries table
        if ($countryCode) {
            $country = Country::where('iso2', $countryCode)->first();
            if ($country && !empty($country->currency_code)) {
                return strtoupper($country->currency_code);
            }
        }

        return 'NGN';
    }

    /**
     * User's documents/texts.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * User's categories.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Wallet::class)->latest('id');
    }

    public function primaryWallet()
    {
        return $this->hasOne(Wallet::class)->latest('id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function verificationSessions(): HasMany
    {
        return $this->hasMany(VerificationSession::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }


    /**
     * Check if user has all required profile fields filled.
     */
    public function isProfileComplete(): bool
    {
        return !blank($this->first_name) && !blank($this->last_name) && !blank($this->phone);
    }

    /**
     * Get computed full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return !empty($parts) ? implode(' ', $parts) : ($this->name ?? '');
    }
}
