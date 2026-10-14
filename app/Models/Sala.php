<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'sala';

    protected $fillable = [
        'numero_sala',
        'capacidad',
    ];

    protected $casts = [
        'capacidad' => 'integer',
    ];

    /**
     * Una sala tiene muchas butacas.
     */
    public function butacas(): HasMany
    {
        return $this->hasMany(Butaca::class, 'sala_id');
    }

    /**
     * Una sala tiene muchas funciones programadas.
     */
    public function funciones(): HasMany
    {
        return $this->hasMany(Funcion::class, 'sala_id');
    }
}
