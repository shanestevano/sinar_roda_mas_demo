<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RackLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'zone',
        'description',
        'capacity',
    ];

    public function tires(): HasMany
    {
        return $this->hasMany(Tire::class);
    }
}
