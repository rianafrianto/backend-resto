<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'day', 'opening_time', 'closing_time'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
