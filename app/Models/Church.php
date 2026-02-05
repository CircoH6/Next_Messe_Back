<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $fillable = [
        'name'
    ];

    public function recurringMasses()
    {
        return $this->hasMany(ReccuringMass::class);
    }

    public function massEvents()
    {
        return $this->hasMany(MassEvent::class);
    }

}
