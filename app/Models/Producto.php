<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'precio_puntos',
        'stock',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_puntos' => 'integer',
        'stock' => 'integer',
    ];
}
