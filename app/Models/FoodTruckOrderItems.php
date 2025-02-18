<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTruckOrderItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['metadata' => 'array'];

            public function truck(){
        return $this->belongsTo(Truck::class);
    }
}
