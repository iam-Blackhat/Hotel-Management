<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function truckEmployee(){
        return $this->hasMany(TruckEmployees::class);
    }
}
