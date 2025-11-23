<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'minutes',
        'description',
        'locked_by_report_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function lockedByReport(): BelongsTo
    {
        return $this->belongsTo(MonthlyReport::class, 'locked_by_report_id');
    }

    public function getHoursAttribute(): float
    {
        return $this->minutes / 60.0;
    }
}
