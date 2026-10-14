<x-layouts::auth.clear title="Bienvenido a Cine+" :title="__('Welcome')">
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-14">
        <section>
            <div class="flex items-center space-x-3 pb-3 border-b border-gray-800 mb-8">
                <div class="w-1.5 h-7 bg-linear-to-b from-amber-400 to-red-600 rounded-full"></div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Top 10 Más Vistas de la Semana</h2>
            </div>

            <!-- Horizontal Grid for Top Movies -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- Movie Item 1 -->
                @foreach ($peliculas as $pelicula)
                    <a href="{{ route('peliculas.show', $pelicula) }}" class="group space-y-3 cursor-pointer">
                        <div
                            class="border border-gray-800 aspect-video rounded-xl bg-gray-900 flex items-center justify-center relative overflow-hidden group-hover:border-gray-600 transition-all shadow-lg group-hover:shadow-red-500/10">
                            @if ($pelicula->imagen)
                                <img src="{{ $pelicula->imagen_url }}" alt="{{ $pelicula->titulo }}"
                                    class="w-full h-full object-cover rounded-xl">
                            @else
                                <svg class="w-full h-full text-gray-800 stroke-current opacity-60" viewBox="0 0 100 100"
                                    preserveAspectRatio="none">
                                    <line x1="0" y1="0" x2="100" y2="100"
                                        stroke-width="0.5" />
                                    <line x1="100" y1="0" x2="0" y2="100"
                                        stroke-width="0.5" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3
                                class="font-bold text-base leading-tight text-gray-100 group-hover:text-red-400 transition-colors">
                                {{ $pelicula->titulo }}</h3>
                            <p class="text-xs text-gray-400 font-medium mt-1">{{ $pelicula->genero }}</p>
                        </div>
                    </a>
                @endforeach

                <!-- Movie Item 2 -->
                {{-- <div class="group space-y-3 cursor-pointer">
                    <div
                        class="border border-gray-800 aspect-video rounded-xl bg-gray-900 flex items-center justify-center relative overflow-hidden group-hover:border-gray-600 transition-all shadow-lg group-hover:shadow-red-500/10">
                        <svg class="w-full h-full text-gray-800 stroke-current opacity-60" viewBox="0 0 100 100"
                            preserveAspectRatio="none">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5" />
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5" />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="font-bold text-base leading-tight text-gray-100 group-hover:text-red-400 transition-colors">
                            Inception: Reestreno</h3>
                        <p class="text-xs text-gray-400 font-medium mt-1">Acción / Sci-Fi</p>
                    </div>
                </div> --}}

                <!-- Movie Item 3 -->
                {{-- <div class="group space-y-3 cursor-pointer">
                    <div
                        class="border border-gray-800 aspect-video rounded-xl bg-gray-900 flex items-center justify-center relative overflow-hidden group-hover:border-gray-600 transition-all shadow-lg group-hover:shadow-red-500/10">
                        <svg class="w-full h-full text-gray-800 stroke-current opacity-60" viewBox="0 0 100 100"
                            preserveAspectRatio="none">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5" />
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5" />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="font-bold text-base leading-tight text-gray-100 group-hover:text-red-400 transition-colors">
                            The Matrix: Resurrections</h3>
                        <p class="text-xs text-gray-400 font-medium mt-1">Ciencia Ficción</p>
                    </div>
                </div> --}}

            </div>
        </section>
        <section x-data="{ selectedCinema: 'all' }">
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-800 mb-8 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-7 bg-linear-to-b from-amber-400 to-red-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Cartelera Actual</h2>
                </div>

                <!-- Cinema Selector Dropdown -->
                <div>
                    <select x-model="selectedCinema"
                        class="bg-gray-900 border border-gray-800 rounded-lg px-4 py-2 text-sm font-semibold text-gray-200 focus:outline-none focus:border-red-500 cursor-pointer w-full sm:w-auto shadow-sm">
                        <option value="all">Todos los cines</option>
                        <option value="cine-1">Cine Central</option>
                        <option value="cine-2">Cine Norte</option>
                    </select>
                </div>
            </div>

            <!-- Vertical Posters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- Card 1 -->
                <div
                    class="bg-gray-900/60 border border-gray-800 rounded-2xl p-4 flex flex-col justify-between space-y-4 hover:border-gray-700 transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <div class="border border-gray-800 aspect-3/4 rounded-xl bg-gray-900 relative overflow-hidden">
                        <svg class="w-full h-full text-gray-800 stroke-current opacity-60" viewBox="0 0 100 100"
                            preserveAspectRatio="none">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5" />
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-lg leading-snug text-white">Inception: Reestreno</h3>
                        <p class="text-xs text-gray-400 font-medium">Acción, Sci-Fi</p>
                    </div>
                    <button
                        class="w-full bg-linear-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-red-900/30">
                        Comprar Entradas
                    </button>
                </div>
                <!-- Card 2 -->
                <div
                    class="bg-gray-900/60 border border-gray-800 rounded-2xl p-4 flex flex-col justify-between space-y-4 hover:border-gray-700 transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <div class="border border-gray-800 aspect-3/4 rounded-xl bg-gray-900 relative overflow-hidden">
                        <svg class="w-full h-full text-gray-800 stroke-current opacity-60" viewBox="0 0 100 100"
                            preserveAspectRatio="none">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5" />
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-lg leading-snug text-white">Avatar 3</h3>
                        <p class="text-xs text-gray-400 font-medium">Fantasía, Sci-Fi</p>
                    </div>
                    <button
                        class="w-full bg-linear-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-red-900/30">
                        Comprar Entradas
                    </button>
                </div>

            </div>
        </section>
    </main>
    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</x-layouts::auth.clear>
