{{-- ─────────────────────── Datos generales ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos de la Disciplina</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div class="sm:col-span-2">
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $disciplina->nombre ?? '') }}"
                placeholder="Ej: Natación, Fútbol, Tenis…"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('nombre')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="descripcion" class="block text-sm font-medium text-slate-700 mb-1.5">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="2"
                placeholder="Información adicional sobre la disciplina…"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400 resize-y">{{ old('descripcion', $disciplina->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="costo" class="block text-sm font-medium text-slate-700 mb-1.5">
                Costo <span class="text-red-500">*</span>
            </label>
            <div class="flex rounded-lg border overflow-hidden focus-within:ring-2 focus-within:ring-blue-500
                {{ $errors->has('costo') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                <input type="number" id="costo" name="costo" step="0.01" min="0"
                    value="{{ old('costo', isset($disciplina) ? $disciplina->costo : '') }}"
                    class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none {{ $errors->has('costo') ? 'bg-red-50' : '' }}">
            </div>
            @error('costo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tipo_costo" class="block text-sm font-medium text-slate-700 mb-1.5">
                Tipo de costo <span class="text-red-500">*</span>
            </label>
            <select id="tipo_costo" name="tipo_costo"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('tipo_costo') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="mensual"   {{ old('tipo_costo', $disciplina->tipo_costo ?? 'mensual') === 'mensual'   ? 'selected' : '' }}>Mensual</option>
                <option value="por_clase" {{ old('tipo_costo', $disciplina->tipo_costo ?? '') === 'por_clase' ? 'selected' : '' }}>Por clase</option>
                <option value="anual"     {{ old('tipo_costo', $disciplina->tipo_costo ?? '') === 'anual'     ? 'selected' : '' }}>Anual</option>
            </select>
            @error('tipo_costo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="estado" class="block text-sm font-medium text-slate-700 mb-1.5">
                Estado <span class="text-red-500">*</span>
            </label>
            <select id="estado" name="estado"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('estado') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="activa"   {{ old('estado', $disciplina->estado ?? 'activa') === 'activa'   ? 'selected' : '' }}>Activa</option>
                <option value="inactiva" {{ old('estado', $disciplina->estado ?? '') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
            </select>
            @error('estado')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- ─────────────────────── Horarios ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Días y Horarios</h2>
        <button type="button" id="btn-agregar-horario"
            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Agregar franja
        </button>
    </div>

    <div id="horarios-container" class="divide-y divide-slate-100">
        @php
            $horariosIniciales = old('horarios', isset($disciplina) ? $disciplina->horarios->map(fn($h) => [
                'dia_semana'  => $h->dia_semana,
                'hora_inicio' => substr($h->hora_inicio, 0, 5),
                'hora_fin'    => substr($h->hora_fin, 0, 5),
            ])->toArray() : []);
        @endphp

        @forelse($horariosIniciales as $i => $h)
            <div class="horario-fila flex flex-col sm:flex-row items-start sm:items-center gap-3 p-4">
                <select name="horarios[{{ $i }}][dia_semana]"
                    class="text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-40">
                    @foreach(\App\Models\Disciplina::diasOrdenados() as $dia)
                        <option value="{{ $dia }}" {{ ($h['dia_semana'] ?? '') === $dia ? 'selected' : '' }}>
                            {{ \App\Models\Disciplina::etiquetaDia($dia) }}
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2 flex-1">
                    <input type="time" name="horarios[{{ $i }}][hora_inicio]" value="{{ $h['hora_inicio'] ?? '' }}"
                        class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    <span class="text-slate-400 text-sm shrink-0">a</span>
                    <input type="time" name="horarios[{{ $i }}][hora_fin]" value="{{ $h['hora_fin'] ?? '' }}"
                        class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <button type="button" class="btn-quitar-horario text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @empty
            <div id="horarios-vacio" class="p-6 text-center text-sm text-slate-400">
                Sin franjas horarias. Hacé clic en «Agregar franja» para agregar.
            </div>
        @endforelse
    </div>
</div>

<script>
(function () {
    const container  = document.getElementById('horarios-container');
    const btnAgregar = document.getElementById('btn-agregar-horario');
    const dias = @json(\App\Models\Disciplina::diasOrdenados());
    const etiquetas = @json(array_combine(\App\Models\Disciplina::diasOrdenados(), array_map(fn($d) => \App\Models\Disciplina::etiquetaDia($d), \App\Models\Disciplina::diasOrdenados())));

    function indiceActual() {
        return container.querySelectorAll('.horario-fila').length;
    }

    function quitarVacio() {
        const vacio = document.getElementById('horarios-vacio');
        if (vacio) vacio.remove();
    }

    function mostrarVacioSiCorresponde() {
        if (container.querySelectorAll('.horario-fila').length === 0) {
            const div = document.createElement('div');
            div.id = 'horarios-vacio';
            div.className = 'p-6 text-center text-sm text-slate-400';
            div.textContent = 'Sin franjas horarias. Hacé clic en «Agregar franja» para agregar.';
            container.appendChild(div);
        }
    }

    function crearFila(idx) {
        const fila = document.createElement('div');
        fila.className = 'horario-fila flex flex-col sm:flex-row items-start sm:items-center gap-3 p-4 border-t border-slate-100';

        const select = document.createElement('select');
        select.name = `horarios[${idx}][dia_semana]`;
        select.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-40';
        dias.forEach(dia => {
            const opt = document.createElement('option');
            opt.value = dia;
            opt.textContent = etiquetas[dia];
            select.appendChild(opt);
        });

        const wrapHoras = document.createElement('div');
        wrapHoras.className = 'flex items-center gap-2 flex-1';

        const inputInicio = document.createElement('input');
        inputInicio.type = 'time';
        inputInicio.name = `horarios[${idx}][hora_inicio]`;
        inputInicio.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';

        const sep = document.createElement('span');
        sep.className = 'text-slate-400 text-sm shrink-0';
        sep.textContent = 'a';

        const inputFin = document.createElement('input');
        inputFin.type = 'time';
        inputFin.name = `horarios[${idx}][hora_fin]`;
        inputFin.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';

        wrapHoras.append(inputInicio, sep, inputFin);

        const btnQuitar = document.createElement('button');
        btnQuitar.type = 'button';
        btnQuitar.className = 'btn-quitar-horario text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0';
        btnQuitar.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>`;
        btnQuitar.addEventListener('click', () => { fila.remove(); mostrarVacioSiCorresponde(); });

        fila.append(select, wrapHoras, btnQuitar);
        return fila;
    }

    btnAgregar.addEventListener('click', () => {
        quitarVacio();
        container.appendChild(crearFila(indiceActual()));
    });

    container.addEventListener('click', e => {
        const btn = e.target.closest('.btn-quitar-horario');
        if (btn) {
            btn.closest('.horario-fila').remove();
            mostrarVacioSiCorresponde();
        }
    });
})();
</script>
