<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
   
    protected $fillable = ['user_id', 'service_id', 'name', 'address', 'phone', 'date', 'total_price', 'status'];
    public function user()
{
    return $this->belongsTo(AppUsers::class);
}

public function service()
{
    return $this->belongsTo(Service::class);
}
}
