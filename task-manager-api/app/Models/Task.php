<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium',
    ];
}