<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CineDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. PRODUCTOS DE LA TIENDA (Canchita, Gaseosas, Combos, etc.)
        // -------------------------------------------------------------
        $productos = [
            [
                'nombre' => 'Canchita Popcorn Grande',
                'descripcion' => 'Canchita salada tamaño grande (150g)',
                'precio' => 18.00,
                'precio_puntos' => 180,
                'stock' => 100,
            ],
            [
                'nombre' => 'Canchita Popcorn Mediana',
                'descripcion' => 'Canchita salada tamaño mediano (100g)',
                'precio' => 14.00,
                'precio_puntos' => 140,
                'stock' => 100,
            ],
            [
                'nombre' => 'Canchita Dulce Grande',
                'descripcion' => 'Canchita acaramelada tamaño grande',
                'precio' => 20.00,
                'precio_puntos' => 200,
                'stock' => 80,
            ],
            [
                'nombre' => 'Gaseosa Mediana (32 oz)',
                'descripcion' => 'Sabor a elección: Coca Cola, Inca Kola o Fanta',
                'precio' => 10.00,
                'precio_puntos' => 100,
                'stock' => 150,
            ],
            [
                'nombre' => 'Gaseosa Grande (44 oz)',
                'descripcion' => 'Sabor a elección: Coca Cola, Inca Kola o Fanta',
                'precio' => 12.50,
                'precio_puntos' => 125,
                'stock' => 150,
            ],
            [
                'nombre' => 'Combo 1: Personal',
                'descripcion' => '1 Canchita Mediana + 1 Gaseosa Mediana',
                'precio' => 22.00,
                'precio_puntos' => 220,
                'stock' => 50,
            ],
            [
                'nombre' => 'Combo 2: Dúo',
                'descripcion' => '1 Canchita Grande + 2 Gaseosas Medianas',
                'precio' => 35.00,
                'precio_puntos' => 350,
                'stock' => 50,
            ],
            [
                'nombre' => 'Nachos con Queso',
                'descripcion' => 'Porción de nachos crocantes acompañados de queso caliente',
                'precio' => 16.00,
                'precio_puntos' => 160,
                'stock' => 60,
            ],
            [
                'nombre' => 'Hot Dog Gigante',
                'descripcion' => 'Hot dog con salchicha de res y salsas a elección',
                'precio' => 13.00,
                'precio_puntos' => 130,
                'stock' => 40,
            ],
            [
                'nombre' => 'Agua Mineral 600ml',
                'descripcion' => 'Agua sin gas',
                'precio' => 6.00,
                'precio_puntos' => 60,
                'stock' => 200,
            ],
        ];

        foreach ($productos as $producto) {
            DB::table('producto')->insert(array_merge($producto, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // -------------------------------------------------------------
        // 2. SALAS Y BUTACAS
        // -------------------------------------------------------------
        $salasData = [
            ['numero_sala' => 'Sala 1 (3D)', 'filas' => ['A', 'B', 'C', 'D'], 'asientos_por_fila' => 8],
            ['numero_sala' => 'Sala 2 (2D)', 'filas' => ['A', 'B', 'C', 'D', 'E'], 'asientos_por_fila' => 10],
            ['numero_sala' => 'Sala 3 (XD / IMAX)', 'filas' => ['A', 'B', 'C', 'D', 'E', 'F'], 'asientos_por_fila' => 10],
        ];

        $salasCreadas = [];

        foreach ($salasData as $salaInfo) {
            $capacidadTotal = count($salaInfo['filas']) * $salaInfo['asientos_por_fila'];

            $salaId = DB::table('sala')->insertGetId([
                'numero_sala' => $salaInfo['numero_sala'],
                'capacidad' => $capacidadTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $butacaIds = [];

            // Generación de butacas (ej. A-1, A-2, B-1, etc.)
            foreach ($salaInfo['filas'] as $fila) {
                for ($num = 1; $num <= $salaInfo['asientos_por_fila']; $num++) {
                    $butacaId = DB::table('butaca')->insertGetId([
                        'numero_butaca' => "{$fila}-{$num}",
                        'sala_id' => $salaId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $butacaIds[] = $butacaId;
                }
            }

            $salasCreadas[] = [
                'id' => $salaId,
                'capacidad' => $capacidadTotal,
                'butaca_ids' => $butacaIds,
            ];
        }

        // -------------------------------------------------------------
        // 3. FUNCIONES Y DISPONIBILIDAD DE BUTACAS
        // -------------------------------------------------------------
        $peliculasIds = DB::table('pelicula')->pluck('id')->toArray();

        // Si no existen películas aún, omitimos la creación de funciones
        if (empty($peliculasIds)) {
            return;
        }

        $horarios = ['14:30:00', '17:45:00', '20:30:00', '22:15:00'];
        $dias = [0, 1, 2]; // Hoy, mañana y pasado mañana

        foreach ($dias as $diasOffset) {
            $fechaBase = Carbon::today()->addDays($diasOffset);

            foreach ($salasCreadas as $index => $sala) {
                // Selecciona una película de forma dinámica
                $peliculaId = $peliculasIds[array_rand($peliculasIds)];
                $hora = $horarios[$index % count($horarios)];
                $fechaHora = $fechaBase->format('Y-m-d') . ' ' . $hora;

                $funcionId = DB::table('funcion')->insertGetId([
                    'pelicula_id' => $peliculaId,
                    'sala_id' => $sala['id'],
                    'fecha_hora' => $fechaHora,
                    'precio' => 25.00,
                    'precio_puntos' => 250,
                    'asientos_disponibles' => $sala['capacidad'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Generar los registros pivote en funcion_butaca
                foreach ($sala['butaca_ids'] as $butacaId) {
                    DB::table('funcion_butaca')->insert([
                        'funcion_id' => $funcionId,
                        'butaca_id' => $butacaId,
                        'disponible' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
