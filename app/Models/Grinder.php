<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grinder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'notes',
        'coffee_shop_id',
    ];

    public function coffeeShop()
    {
        return $this->belongsTo(CoffeeShop::class);
    }

    public function calibrationSessions()
    {
        return $this->hasMany(CalibrationSession::class);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->model) {
            return "{$this->name} - {$this->model}";
        }
        return $this->name;
    }
}
