<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalibrationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'bean_id',
        'grinder_id',
        'user_id',
        'session_date',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function bean()
    {
        return $this->belongsTo(Bean::class);
    }

    public function grinder()
    {
        return $this->belongsTo(Grinder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shots()
    {
        return $this->hasMany(Shot::class)->orderBy('shot_number');
    }

    public function getShotCountAttribute()
    {
        return $this->shots()->count();
    }

    public function getLatestShotAttribute()
    {
        return $this->shots()->orderBy('shot_number', 'desc')->first();
    }
}
