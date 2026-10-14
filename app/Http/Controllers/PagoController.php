<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Producto;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $reserva = session('reserva_temp');

        if (!$reserva) {
            return redirect()->route('home')->withErrors(['error' => 'No hay una reserva activa.']);
        }

        $funcion = Funcion::with(['pelicula', 'sala'])->findOrFail($reserva['funcion_id']);

        // Obtener productos disponibles de la tienda
        $productos = Producto::where('stock', '>', 0)->get();

        // Datos del usuario autenticado o valores por defecto
        $user = auth()->user();
        $cliente = [
            'nombre' => $user ? $user->name : '',
            'email' => $user ? $user->email : '',
            'puntos' => $user ? ($user->puntos ?? 120) : 120,
        ];

        return view('pago.index', compact('reserva', 'funcion', 'productos', 'cliente'));
    }

    public function procesar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'metodo_pago' => 'required|in:tarjeta,yape,plin,apple_pay',
            'descuento' => 'nullable|string',
            'canjear_puntos' => 'nullable|boolean',
            'productos' => 'nullable|array',
            'productos.*' => 'integer|min:0',
            'numero_tarjeta' => 'required_if:metodo_pago,tarjeta|nullable|string',
            'vencimiento' => 'required_if:metodo_pago,tarjeta|nullable|string',
            'cvc' => 'required_if:metodo_pago,tarjeta|nullable|string',
        ]);

        $reserva = session('reserva_temp');

        if (!$reserva) {
            return redirect()->route('home')->withErrors(['error' => 'La sesión de pago ha expirado.']);
        }

        // Aquí puedes realizar la llamada a la pasarela de pago o guardar el comprobante/tickets en BD

        // Limpiamos la sesión temporal
        session()->forget('reserva_temp');

        return redirect()->route('home')->with('success', '¡Pago confirmado! Tu reserva ha sido procesada con éxito.');
    }
}
