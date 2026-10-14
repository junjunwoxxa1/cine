<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Funcion extends Model
{
    use HasFactory;

    protected $table = 'funcion';

    protected $fillable = [
        'pelicula_id',
        'sala_id',
        'fecha_hora',
        'precio',
        'precio_puntos',
        'asientos_disponibles',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'precio' => 'decimal:2',
        'precio_puntos' => 'integer',
        'asientos_disponibles' => 'integer',
    ];

    /**
     * La función pertenece a una película.
     */
    public function pelicula(): BelongsTo
    {
        return $this->belongsTo(Pelicula::class, 'pelicula_id');
    }

    /**
     * La función se realiza en una sala.
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    /**
     * Relación muchos a muchos con butacas (para verificar estado de disponibilidad).
     */
    public function butacas(): BelongsToMany
    {
        return $this->belongsToMany(Butaca::class, 'funcion_butaca', 'funcion_id', 'butaca_id')
            ->withPivot('disponible')
            ->withTimestamps();
    }
}
