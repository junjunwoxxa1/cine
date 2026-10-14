<?php

namespace App\Http\Controllers;

use App\Models\Butaca;
use App\Models\Funcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function selectButacas(Funcion $funcion)
    {
        // Carga la función con la película, sala y las butacas asociadas con su estado (disponible / ocupada)
        $funcion->load(['pelicula', 'sala', 'butacas']);

        // Agrupamos las butacas por fila extraída del número de butaca (ej: "A-1" -> "A")
        $butacasPorFila = $funcion->butacas->groupBy(function ($butaca) {
            return explode('-', $butaca->numero_butaca)[0] ?? 'A';
        });

        return view('reservas.butacas', compact('funcion', 'butacasPorFila'));
    }

    public function store(Request $request)
    {
        // 1. Validación de la petición
        $validated = $request->validate([
            'funcion_id' => 'required|exists:funcion,id',
            'cantidad_entradas' => 'required|integer|min:1|max:10',
            'butacas' => 'required|array|min:1',
            'butacas.*' => 'required|string',
        ]);

        if (count($validated['butacas']) !== (int) $validated['cantidad_entradas']) {
            return back()->withErrors(['butacas' => 'La cantidad de butacas seleccionadas no coincide con las entradas solicitadas.']);
        }

        $funcion = Funcion::findOrFail($validated['funcion_id']);

        // 2. Procesamiento dentro de una transacción para evitar inconsistencias
        try {
            DB::beginTransaction();

            // Formatear códigos de asientos de la vista ("A1") al formato almacenado en BD ("A-1")
            $numerosButacas = array_map(function ($codigo) {
                preg_match('/([A-Za-z]+)(\d+)/', $codigo, $matches);
                return isset($matches[1], $matches[2]) ? $matches[1] . '-' . $matches[2] : $codigo;
            }, $validated['butacas']);

            // Obtener los IDs de las butacas pertenecientes a la sala
            $butacas = Butaca::where('sala_id', $funcion->sala_id)
                ->whereIn('numero_butaca', $numerosButacas)
                ->get();

            if ($butacas->count() !== count($numerosButacas)) {
                return back()->withErrors(['butacas' => 'Una o más butacas seleccionadas no son válidas.']);
            }

            // Verificar disponibilidad en la tabla pivote funcion_butaca
            foreach ($butacas as $butaca) {
                $disponible = $funcion->butacas()
                    ->where('butaca_id', $butaca->id)
                    ->wherePivot('disponible', true)
                    ->exists();

                if (!$disponible) {
                    return back()->withErrors(['butacas' => "La butaca {$butaca->numero_butaca} ya no está disponible."]);
                }
            }

            // Marcar las butacas como no disponibles en la tabla pivote
            foreach ($butacas as $butaca) {
                $funcion->butacas()->updateExistingPivot($butaca->id, [
                    'disponible' => false,
                ]);
            }

            // Descontar asientos disponibles en la función
            $funcion->decrement('asientos_disponibles', count($butacas));

            DB::commit();

            // Guardar detalles temporales en la sesión para el flujo de pago
            session([
                'reserva_temp' => [
                    'funcion_id' => $funcion->id,
                    'butacas_ids' => $butacas->pluck('id')->toArray(),
                    'butacas_codigos' => $validated['butacas'],
                    'cantidad' => $validated['cantidad_entradas'],
                    'monto_total' => $funcion->precio * count($butacas),
                ]
            ]);

            // Redirigir a la vista de pago
            return redirect()->route('pago.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al procesar la reserva. Inténtalo de nuevo.']);
        }
    }
}
