<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'visit_date',
        'ip_hash',
        'session_key',
        'path',
        'referrer',
        'user_agent',
        'is_unique_daily',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'is_unique_daily' => 'boolean',
        ];
    }
}
