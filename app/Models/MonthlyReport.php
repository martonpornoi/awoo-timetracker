<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'status',
        'created_by',
        'snapshot',
        'export_path',
        'closed_at',
    ];

    protected $casts = [
        'month' => 'date',
        'closed_at' => 'datetime',
        'snapshot' => 'array',
    ];

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'locked_by_report_id');
    }
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
