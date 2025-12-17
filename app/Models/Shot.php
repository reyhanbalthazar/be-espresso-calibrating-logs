<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shot extends Model
{
    use HasFactory;

    protected $fillable = [
        'calibration_session_id',
        'shot_number',
        'grind_setting',
        'dose',
        'yield',
        'time_seconds',
        'taste_notes',
        'action_taken',
    ];

    protected $casts = [
        'dose' => 'decimal:2',
        'yield' => 'decimal:2',
        'time_seconds' => 'integer',
    ];

    public function calibrationSession()
    {
        return $this->belongsTo(CalibrationSession::class);
    }

    public function getExtractionYieldAttribute()
    {
        if ($this->dose > 0) {
            return ($this->yield / $this->dose) * 100;
        }
        return 0;
    }

    public function getExtractionRatioAttribute()
    {
        if ($this->dose > 0) {
            return $this->yield / $this->dose;
        }
        return 0;
    }

    public function getFlowRateAttribute()
    {
        if ($this->time_seconds > 0) {
            return $this->yield / $this->time_seconds;
        }
        return 0;
    }
}
