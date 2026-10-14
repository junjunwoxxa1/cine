<x-layouts::auth.clear :title="'Pago y Confirmación - ' . $funcion->pelicula->titulo">
    <main class="max-w-7xl mx-auto px-6 py-8 space-y-6" x-data="pagoController({
        precioUnitario: {{ $funcion->precio }},
        cantidadEntradas: {{ $reserva['cantidad'] }},
        puntosUsuario: {{ $cliente['puntos'] }},
        productosData: {{ json_encode($productos->keyBy('id')) }}
    })">

        <!-- Enlace Volver a Butacas -->
        <div>
            <a href="{{ route('reservas.butacas', $funcion) }}"
                class="inline-flex items-center space-x-2 text-sm font-semibold text-gray-400 hover:text-white transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="underline underline-offset-4">Volver a Butacas</span>
            </a>
        </div>

        <h1 class="text-3xl font-extrabold text-white tracking-tight">Pago y Confirmación</h1>

        <form action="{{ route('pago.procesar') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf

            <!-- Columna Izquierda -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Datos del Cliente -->
                <div class="border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-4 shadow-xl">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-300">Datos del Comprador</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nombre Completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $cliente['nombre']) }}" required
                                placeholder="Ej. Juan Pérez"
                                class="w-full border border-gray-800 rounded-xl p-3 text-sm bg-gray-900 text-white placeholder-gray-600 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $cliente['email']) }}" required
                                placeholder="ejemplo@correo.com"
                                class="w-full border border-gray-800 rounded-xl p-3 text-sm bg-gray-900 text-white placeholder-gray-600 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- 2. Productos de Dulcería / Tienda -->
                <div class="border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-800/80 pb-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-300">Añadir Dulcería y Snacks</h2>
                        <span class="text-xs text-gray-400">Opcional</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        @foreach ($productos as $producto)
                            <div class="border border-gray-800 bg-gray-900/80 rounded-xl p-4 flex items-center justify-between space-x-3">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $producto->nombre }}</p>
                                    <p class="text-xs text-red-400 font-semibold mt-0.5">S/ {{ number_format($producto->precio, 2) }}</p>
                                </div>

                                <div class="flex items-center space-x-2 bg-gray-950 border border-gray-800 rounded-lg p-1">
                                    <button type="button" @click="modificarCantidadProducto({{ $producto->id }}, -1)"
                                        class="w-7 h-7 rounded bg-gray-900 hover:bg-gray-800 text-gray-200 font-bold flex items-center justify-center transition-colors">
                                        -
                                    </button>
                                    <span class="w-6 text-center text-xs font-bold text-white"
                                        x-text="cantidadesProductos[{{ $producto->id }}] || 0">0</span>
                                    <button type="button" @click="modificarCantidadProducto({{ $producto->id }}, 1)"
                                        class="w-7 h-7 rounded bg-gray-900 hover:bg-gray-800 text-gray-200 font-bold flex items-center justify-center transition-colors">
                                        +
                                    </button>
                                </div>

                                <input type="hidden" name="productos[{{ $producto->id }}]" :value="cantidadesProductos[{{ $producto->id }}] || 0">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-4 shadow-xl">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-300">Descuentos Aplicables</h2>

                    <select name="descuento" x-model="descuentoSeleccionado" @change="calcularTotales()"
                        class="w-full border border-gray-800 rounded-xl p-3 text-sm font-semibold bg-gray-900 text-gray-200 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                        <option value="ninguno">Ninguno (Entrada General)</option>
                        <option value="estudiante">Descuento Estudiante (-S/ 4.00 por entrada)</option>
                        <option value="socio">Promoción Socio Cine+ (-S/ 8.00 total)</option>
                    </select>

                    <div class="border border-dashed border-gray-800 rounded-xl p-4 bg-gray-950/40 text-xs text-gray-400 flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                        </svg>
                        <span><strong>Importante:</strong> Los descuentos requieren presentar identificación vigente en el ingreso a la sala.</span>
                    </div>
                </div>

                <div class="border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-4 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-300">Club Cine+</h2>
                        <p class="text-xs text-gray-400 mt-1">Tienes <strong class="text-white" x-text="puntosUsuario">0</strong> pts disponibles.</p>
                    </div>

                    <label class="inline-flex items-center space-x-3 border border-gray-800 bg-gray-900 hover:bg-gray-800 px-4 py-3 rounded-xl cursor-pointer transition-colors">
                        <input type="checkbox" name="canjear_puntos" value="1" x-model="canjearPuntos" @change="calcularTotales()"
                            class="w-4 h-4 rounded border-gray-700 text-red-600 focus:ring-red-500 bg-gray-950">
                        <span class="text-xs font-bold text-gray-200">Canjear Entradas</span>
                    </label>
                </div>

                <div class="border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-6 shadow-xl">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-300">Método de Pago</h2>

                    <input type="hidden" name="metodo_pago" :value="metodoPago">

                    <div class="flex flex-wrap gap-3">
                        <button type="button" @click="metodoPago = 'tarjeta'"
                            :class="metodoPago === 'tarjeta' ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-900/40' : 'border-gray-800 bg-gray-900 text-gray-300 hover:bg-gray-800'"
                            class="border px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                            Tarjeta
                        </button>
                        <button type="button" @click="metodoPago = 'yape'"
                            :class="metodoPago === 'yape' ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-900/40' : 'border-gray-800 bg-gray-900 text-gray-300 hover:bg-gray-800'"
                            class="border px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                            Yape
                        </button>
                        <button type="button" @click="metodoPago = 'plin'"
                            :class="metodoPago === 'plin' ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-900/40' : 'border-gray-800 bg-gray-900 text-gray-300 hover:bg-gray-800'"
                            class="border px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                            Plin
                        </button>
                        <button type="button" @click="metodoPago = 'apple_pay'"
                            :class="metodoPago === 'apple_pay' ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-900/40' : 'border-gray-800 bg-gray-900 text-gray-300 hover:bg-gray-800'"
                            class="border px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                            Apple Pay
                        </button>
                    </div>

                    <template x-if="metodoPago === 'tarjeta'">
                        <div class="space-y-4 pt-2">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Número de Tarjeta</label>
                                <input type="text" name="numero_tarjeta" placeholder="0000 0000 0000 0000"
                                    class="w-full border border-gray-800 rounded-xl p-3 text-sm bg-gray-900 text-white placeholder-gray-600 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Vencimiento</label>
                                    <input type="text" name="vencimiento" placeholder="MM/YY"
                                        class="w-full border border-gray-800 rounded-xl p-3 text-sm bg-gray-900 text-white placeholder-gray-600 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">CVC</label>
                                    <input type="password" name="cvc" placeholder="***" maxlength="4"
                                        class="w-full border border-gray-800 rounded-xl p-3 text-sm bg-gray-900 text-white placeholder-gray-600 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="metodoPago !== 'tarjeta'">
                        <div class="p-4 border border-dashed border-gray-800 rounded-xl bg-gray-950/40 text-center text-xs text-gray-400">
                            Se generará un código QR en el siguiente paso para completar tu pago con <strong class="text-white capitalize" x-text="metodoPago"></strong>.
                        </div>
                    </template>
                </div>

            </div>

            <!-- Columna Derecha: Resumen -->
            <div class="lg:col-span-4 border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-6 shadow-xl">
                <h2 class="text-xl font-bold tracking-tight text-white">Resumen del Pedido</h2>

                <div class="space-y-3 text-sm">
                    <!-- Subtotal Entradas -->
                    <div class="flex justify-between text-gray-300">
                        <span>{{ $reserva['cantidad'] }}x Entradas</span>
                        <span class="font-bold text-white">S/ <span x-text="subtotalEntradas.toFixed(2)">0.00</span></span>
                    </div>

                    <!-- Subtotal Productos -->
                    <div class="flex justify-between text-gray-300" x-show="subtotalProductos > 0">
                        <span>Dulcería / Snacks</span>
                        <span class="font-bold text-white">S/ <span x-text="subtotalProductos.toFixed(2)">0.00</span></span>
                    </div>

                    <!-- Descuento -->
                    <div class="flex justify-between text-red-400 text-xs" x-show="montoDescuento > 0">
                        <span>Descuento aplicado</span>
                        <span>- S/ <span x-text="montoDescuento.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                <hr class="border-gray-800">

                <div class="flex justify-between items-baseline">
                    <span class="text-sm font-bold text-gray-300 uppercase tracking-wider">Total a Pagar</span>
                    <span class="text-2xl font-black text-white">S/ <span x-text="total.toFixed(2)">0.00</span></span>
                </div>

                <div class="border border-dashed border-amber-500/40 bg-amber-500/10 rounded-xl p-3.5 text-xs text-amber-300 flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Esta compra te suma <strong>+10 Puntos Cine+</strong></span>
                </div>

                <button type="submit"
                    class="w-full bg-red-600 border border-red-500 text-white hover:bg-red-700 py-3.5 px-4 rounded-xl text-xs font-bold tracking-widest uppercase shadow-lg shadow-red-900/40 transition-all duration-200 cursor-pointer">
                    PAGAR Y CONFIRMAR
                </button>
            </div>

        </form>
    </main>

    <!-- Lógica Alpine.js -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pagoController', (config) => ({
                precioUnitario: config.precioUnitario || 0,
                cantidadEntradas: config.cantidadEntradas || 1,
                puntosUsuario: config.puntosUsuario || 0,
                productosData: config.productosData || {},
                cantidadesProductos: {},
                metodoPago: 'tarjeta',
                descuentoSeleccionado: 'ninguno',
                canjearPuntos: false,
                subtotalEntradas: 0,
                subtotalProductos: 0,
                montoDescuento: 0,
                total: 0,

                init() {
                    this.subtotalEntradas = this.precioUnitario * this.cantidadEntradas;
                    this.calcularTotales();
                },

                modificarCantidadProducto(id, cambio) {
                    let actual = this.cantidadesProductos[id] || 0;
                    let nuevo = Math.max(0, actual + cambio);
                    this.cantidadesProductos[id] = nuevo;
                    this.calcularTotales();
                },

                calcularTotales() {
                    // Cálculo de Dulcería
                    this.subtotalProductos = 0;
                    for (let id in this.cantidadesProductos) {
                        let cantidad = this.cantidadesProductos[id];
                        if (cantidad > 0 && this.productosData[id]) {
                            this.subtotalProductos += this.productosData[id].precio * cantidad;
                        }
                    }

                    // Cálculo de Descuentos en Entradas
                    let desc = 0;
                    if (this.canjearPuntos) {
                        desc = this.subtotalEntradas;
                    } else {
                        if (this.descuentoSeleccionado === 'estudiante') {
                            desc = 4 * this.cantidadEntradas;
                        } else if (this.descuentoSeleccionado === 'socio') {
                            desc = 8;
                        }
                    }

                    this.montoDescuento = Math.min(desc, this.subtotalEntradas);
                    this.total = Math.max(0, (this.subtotalEntradas - this.montoDescuento) + this.subtotalProductos);
                }
            }));
        });
    </script>
</x-layouts::auth.clear>