<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategoryService extends Model
{
    use HasFactory;

    protected $table = 'service_category_service';

    protected $fillable = ['service_category_id', 'service_id'];

    public function serviceCategory() { return $this->belongsTo(ServiceCategory::class); }

    public function service() { return $this->belongsTo(Service::class); }
}
