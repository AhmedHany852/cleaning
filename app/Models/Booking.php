<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

public function getAvailabilityAttribute()
{
        $currentTime = Carbon::now();
        // Calculate the time difference
        $timeDifference = $currentTime->diffInHours($this->booking_time);
        // Check availability
        return $timeDifference >= $this->service->duration ? 1 : 0;

}
}
