<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoffeeShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function beans()
    {
        return $this->hasMany(Bean::class);
    }

    public function grinders()
    {
        return $this->hasMany(Grinder::class);
    }

    public function calibrationSessions()
    {
        return $this->hasMany(CalibrationSession::class);
    }
}
