<x-layouts::auth.clear :title="'Seleccionar Butacas - ' . $funcion->pelicula->titulo">
    <main class="max-w-7xl mx-auto px-6 py-8 space-y-6" x-data="reservaButacas({
        cantidadPermitida: 2,
        butacasIniciales: []
    })">

        <div class="flex items-center justify-between">
            <a href="{{ route('peliculas.show', $funcion->pelicula) }}"
                class="inline-flex items-center space-x-2 text-sm font-semibold text-gray-400 hover:text-white transition-colors group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                <span class="underline underline-offset-4">Cambiar Función</span>
            </a>

            <div
                class="border border-gray-800 bg-gray-900/80 text-gray-200 px-4 py-2 text-xs font-bold tracking-wider rounded-xl flex items-center gap-2 shadow-lg">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                </svg>
                <span>Reserva: <span class="text-white" x-text="formatearTiempo(tiempoRestante)">09:59</span>
                    </span>
            </div>
        </div>

        <!-- Contenedor Principal de Selección y Resumen -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div
                class="lg:col-span-8 border border-gray-800 bg-gray-900/60 rounded-2xl p-8 sm:p-12 min-h-[480px] flex flex-col justify-between items-center shadow-xl">

                <!-- Pantalla -->
                <div class="w-full max-w-lg text-center space-y-3">
                    <p class="text-xs font-bold tracking-widest uppercase text-gray-400">PANTALLA</p>
                    <div
                        class="w-full h-1.5 bg-gradient-to-r from-gray-800 via-red-600 to-gray-800 rounded-full shadow-lg shadow-red-900/20">
                    </div>
                </div>

                <!-- Mapa de Asientos -->
                <div class="my-12 space-y-6 w-full max-w-lg">
                    @foreach ($butacasPorFila as $fila => $butacas)
                        <div class="flex items-center justify-center space-x-3">
                            <!-- Indicador Fila Izquierda -->
                            <span class="w-4 text-center font-bold text-sm text-gray-400">{{ $fila }}</span>

                            <div class="flex flex-wrap gap-2.5 justify-center">
                                @foreach ($butacas as $butaca)
                                    @php
                                        $esDisponible = $butaca->pivot->disponible;
                                        $codigoClean = str_replace('-', '', $butaca->numero_butaca);
                                    @endphp

                                    <button type="button"
                                        @if ($esDisponible) @click="toggleButaca('{{ $codigoClean }}')" @endif
                                        :disabled="{{ $esDisponible ? 'false' : 'true' }}"
                                        :class="{
                                            'border-dashed border-gray-800 bg-gray-950 text-gray-600 cursor-not-allowed opacity-50': {{ !$esDisponible ? 'true' : 'false' }},
                                            'border-red-500 bg-red-600 text-white shadow-lg shadow-red-900/50 scale-105': {{ $esDisponible ? 'true' : 'false' }} &&
                                                esSeleccionada('{{ $codigoClean }}'),
                                            'border-gray-800 bg-gray-900 text-gray-300 hover:border-gray-600 hover:bg-gray-800 hover:text-white': {{ $esDisponible ? 'true' : 'false' }} &&
                                                !esSeleccionada('{{ $codigoClean }}')
                                        }"
                                        class="cursor-pointer w-9 h-9 border rounded-lg text-xs font-bold flex items-center justify-center transition-all duration-200">
                                        {{ $codigoClean }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Indicador Fila Derecha -->
                            <span class="w-4 text-center font-bold text-sm text-gray-400">{{ $fila }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Leyenda de Estados -->
                <div
                    class="flex items-center justify-center space-x-8 text-xs font-medium text-gray-300 pt-4 border-t border-gray-800/60 w-full">
                    <div class="flex items-center space-x-2">
                        <span class="w-4 h-4 border border-gray-700 bg-gray-900 rounded inline-block"></span>
                        <span>Disponible</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-4 h-4 border border-red-500 bg-red-600 rounded inline-block"></span>
                        <span>Seleccionada</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="w-4 h-4 border border-dashed border-gray-800 bg-gray-950 opacity-50 rounded inline-block"></span>
                        <span class="text-gray-500">Ocupada</span>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-4 border border-gray-800 bg-gray-900/60 rounded-2xl p-6 space-y-6 shadow-xl">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white">Tu Selección</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-3">PELÍCULA</p>
                    <p class="text-base font-bold text-white mt-0.5">
                        {{ $funcion->pelicula->titulo }} - {{ $funcion->sala->numero_sala }}
                    </p>
                    <p class="text-sm font-medium text-gray-400 mt-1">
                        {{ $funcion->fecha_hora->locale('es')->isoFormat('dddd, HH:mm [hrs]') }}
                    </p>
                </div>

                <hr class="border-gray-800">

                <!-- Cantidad de Entradas -->
                <div class="space-y-2">
                    <label for="cantidad_entradas"
                        class="block text-xs font-bold text-gray-400 uppercase tracking-wider">
                        Cantidad de Entradas
                    </label>
                    <select id="cantidad_entradas" x-model.number="cantidadPermitida" @change="alCambiarCantidad()"
                        class="w-full border border-gray-800 rounded-xl p-3 text-sm font-bold bg-gray-900 text-gray-200 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                        <option value="1">1 Entrada</option>
                        <option value="2">2 Entradas</option>
                        <option value="3">3 Entradas</option>
                        <option value="4">4 Entradas</option>
                        <option value="5">5 Entradas</option>
                    </select>
                </div>

                <!-- Butacas Seleccionadas -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">BUTACAS SELECCIONADAS</p>
                    <div class="flex flex-wrap gap-2 min-h-[36px]">
                        <template x-for="b in seleccionadas" :key="b">
                            <span
                                class="border border-red-500/50 bg-red-600/10 px-3 py-1.5 rounded-lg text-xs font-bold text-red-400"
                                x-text="b"></span>
                        </template>
                        <template x-if="seleccionadas.length === 0">
                            <span class="text-xs text-gray-500 italic">Ninguna butaca seleccionada</span>
                        </template>
                    </div>
                </div>

                <hr class="border-gray-800">

                <!-- Botones de Acción -->
                <form action="{{ route('reservas.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="funcion_id" value="{{ $funcion->id }}">
                    <input type="hidden" name="cantidad_entradas" :value="cantidadPermitida">
                    <template x-for="b in seleccionadas" :key="b">
                        <input type="hidden" name="butacas[]" :value="b">
                    </template>

                    <button type="submit" :disabled="seleccionadas.length !== cantidadPermitida"
                        :class="seleccionadas.length === cantidadPermitida ?
                            'bg-red-600 border-red-500 text-white hover:bg-red-700 shadow-lg shadow-red-900/40 cursor-pointer' :
                            'bg-gray-800 border-gray-700 text-gray-500 cursor-not-allowed'"
                        class="w-full border py-3 px-4 rounded-xl text-xs font-bold tracking-widest uppercase transition-all duration-200">
                        CONTINUAR AL PAGO
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ route('home') }}"
                        class="text-xs font-bold text-gray-400 hover:text-white underline underline-offset-4 transition-colors">
                        Cancelar Compra
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Lógica en Alpine JS -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reservaButacas', (config) => ({
                cantidadPermitida: config.cantidadPermitida || 2,
                seleccionadas: [],
                tiempoRestante: 599, // 9 minutos y 59 segundos en segundos

                init() {
                    setInterval(() => {
                        if (this.tiempoRestante > 0) {
                            this.tiempoRestante--;
                        }
                    }, 1000);
                },

                formatearTiempo(segundos) {
                    const min = String(Math.floor(segundos / 60)).padStart(2, '0');
                    const seg = String(segundos % 60).padStart(2, '0');
                    return `${min}:${seg}`;
                },

                toggleButaca(codigo) {
                    if (this.esSeleccionada(codigo)) {
                        this.seleccionadas = this.seleccionadas.filter(item => item !== codigo);
                    } else {
                        if (this.seleccionadas.length < this.cantidadPermitida) {
                            this.seleccionadas.push(codigo);
                        } else {
                            // Si supera el límite de entradas, reemplaza la primera seleccionada
                            this.seleccionadas.shift();
                            this.seleccionadas.push(codigo);
                        }
                    }
                },

                esSeleccionada(codigo) {
                    return this.seleccionadas.includes(codigo);
                },

                alCambiarCantidad() {
                    if (this.seleccionadas.length > this.cantidadPermitida) {
                        this.seleccionadas = this.seleccionadas.slice(0, this.cantidadPermitida);
                    }
                }
            }));
        });
    </script>
</x-layouts::auth.clear>
