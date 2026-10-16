<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryRegion extends Model
{
    use HasFactory;

    protected $table = 'country_regions';

    protected $fillable = ['country_id', 'region_id'];

    public function country() { return $this->belongsTo(Country::class); }

    public function region() { return $this->belongsTo(Region::class); }
}
