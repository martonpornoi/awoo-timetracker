<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'owner_id',
    ];
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
}
