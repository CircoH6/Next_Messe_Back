<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassEvent extends Model
{
    protected $fillable = [
        'church_id',
        'date',
        'time',
        'status',
        'note'
    ];

    protected $casts = [
        'date',
        'time'
    ];

    public function church()
    {
        return $this->belongsTo(Church::class);
    }
}
