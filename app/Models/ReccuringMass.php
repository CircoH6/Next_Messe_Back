<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReccuringMass extends Model
{
    protected $fillable = [
        'church_id',
        'day_of_week',
        'time'
    ];

    protected $casts = [
        'day_of_week' => 'array'
    ];

    public function church(){
        return $this->belongsTo(Church::class);
    }
}
