<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bean extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'origin',
        'roastery',
        'roast_level',
        'roast_date',
        'is_blend',
        'notes',
        'coffee_shop_id',
    ];

    protected $casts = [
        'roast_date' => 'date',
        'is_blend' => 'boolean',
    ];

    public function coffeeShop()
    {
        return $this->belongsTo(CoffeeShop::class);
    }

    public function calibrationSessions()
    {
        return $this->hasMany(CalibrationSession::class);
    }

    public function getRoastAgeAttribute()
    {
        if (!$this->roast_date) {
            return null;
        }
        return $this->roast_date->diffInDays(now());
    }

    public function getFormattedRoastLevelAttribute()
    {
        return ucfirst($this->roast_level);
    }
}