{{-- ─────────────────────── Datos generales ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos de la Actividad</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div class="sm:col-span-2">
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $actividad->nombre ?? '') }}"
                placeholder="Ej: Cancha de tenis, Pileta, Quincho 1…"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('nombre')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="descripcion" class="block text-sm font-medium text-slate-700 mb-1.5">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="2"
                placeholder="Información adicional sobre la actividad…"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400 resize-y">{{ old('descripcion', $actividad->descripcion ?? '') }}</textarea>
            @error('descripcion')
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
                <option value="activa"   {{ old('estado', $actividad->estado ?? 'activa') === 'activa'   ? 'selected' : '' }}>Activa</option>
                <option value="inactiva" {{ old('estado', $actividad->estado ?? '') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
            </select>
            @error('estado')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="anticipacion_dias" class="block text-sm font-medium text-slate-700 mb-1.5">
                Anticipación máxima (días)
            </label>
            <input type="number" id="anticipacion_dias" name="anticipacion_dias" min="0" step="1"
                value="{{ old('anticipacion_dias', $actividad->anticipacion_dias ?? '') }}"
                placeholder="Sin límite"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('anticipacion_dias') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('anticipacion_dias')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="max_turnos_activos" class="block text-sm font-medium text-slate-700 mb-1.5">
                Máx. turnos activos por socio
            </label>
            <input type="number" id="max_turnos_activos" name="max_turnos_activos" min="1" step="1"
                value="{{ old('max_turnos_activos', $actividad->max_turnos_activos ?? '') }}"
                placeholder="Sin límite"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('max_turnos_activos') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('max_turnos_activos')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2 flex flex-col sm:flex-row gap-5">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="requiere_aprobacion" value="1"
                    {{ old('requiere_aprobacion', $actividad->requiere_aprobacion ?? false) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Las reservas requieren aprobación de administración
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" id="requiere_pago" name="requiere_pago" value="1"
                    {{ old('requiere_pago', $actividad->requiere_pago ?? false) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Requiere pago previo
            </label>
        </div>

        <div id="costo-wrapper" class="sm:col-span-2 sm:max-w-xs">
            <label for="costo" class="block text-sm font-medium text-slate-700 mb-1.5">
                Costo por turno
            </label>
            <div class="flex rounded-lg border overflow-hidden focus-within:ring-2 focus-within:ring-blue-500
                {{ $errors->has('costo') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                <input type="number" id="costo" name="costo" step="0.01" min="0"
                    value="{{ old('costo', isset($actividad) ? $actividad->costo : '') }}"
                    class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none {{ $errors->has('costo') ? 'bg-red-50' : '' }}">
            </div>
            @error('costo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- ─────────────────────── Disciplinas requeridas ─────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Restricción por disciplina</h2>
        <p class="text-xs text-slate-400 mt-0.5">Si seleccionás disciplinas, solo podrán reservar turnos los socios inscriptos en al menos una de ellas. Dejá vacío para que esté disponible para todos.</p>
    </div>
    <div class="p-6">
        @if($disciplinas->isEmpty())
            <p class="text-sm text-slate-400">No hay disciplinas activas cargadas.</p>
        @else
            @php
                $seleccionadas = old('disciplinas_requeridas',
                    isset($actividad) ? $actividad->disciplinasRequeridas->pluck('id')->toArray() : []
                );
            @endphp
            <div class="flex flex-wrap gap-3">
                @foreach($disciplinas as $disciplina)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="disciplinas_requeridas[]" value="{{ $disciplina->id }}"
                            {{ in_array($disciplina->id, (array) $seleccionadas) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        {{ $disciplina->nombre }}
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ─────────────────────── Franjas horarias ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Franjas de disponibilidad</h2>
            <p class="text-xs text-slate-400 mt-0.5">Cada franja define un día, un rango horario, la duración de cada turno y el cupo de socios por turno.</p>
        </div>
        <button type="button" id="btn-agregar-franja"
            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Agregar franja
        </button>
    </div>

    <div id="franjas-container" class="divide-y divide-slate-100">
        @php
            $franjasIniciales = old('franjas', isset($actividad) ? $actividad->franjas->map(fn($f) => [
                'dia_semana'       => $f->dia_semana,
                'hora_inicio'      => substr($f->hora_inicio, 0, 5),
                'hora_fin'         => substr($f->hora_fin, 0, 5),
                'duracion_minutos' => $f->duracion_minutos,
                'cupo'             => $f->cupo,
            ])->toArray() : []);
        @endphp

        @forelse($franjasIniciales as $i => $f)
            <div class="franja-fila flex flex-col lg:flex-row items-start lg:items-center gap-3 p-4">
                <select name="franjas[{{ $i }}][dia_semana]"
                    class="text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-full lg:w-36">
                    @foreach(\App\Models\Actividad::diasOrdenados() as $dia)
                        <option value="{{ $dia }}" {{ ($f['dia_semana'] ?? '') === $dia ? 'selected' : '' }}>
                            {{ \App\Models\Actividad::etiquetaDia($dia) }}
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <input type="time" name="franjas[{{ $i }}][hora_inicio]" value="{{ $f['hora_inicio'] ?? '' }}"
                        class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    <span class="text-slate-400 text-sm shrink-0">a</span>
                    <input type="time" name="franjas[{{ $i }}][hora_fin]" value="{{ $f['hora_fin'] ?? '' }}"
                        class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <div class="flex-1 lg:w-32">
                        <input type="number" min="1" step="1" name="franjas[{{ $i }}][duracion_minutos]" value="{{ $f['duracion_minutos'] ?? '' }}"
                            placeholder="Duración (min)"
                            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    </div>
                    <div class="flex-1 lg:w-24">
                        <input type="number" min="1" step="1" name="franjas[{{ $i }}][cupo]" value="{{ $f['cupo'] ?? '' }}"
                            placeholder="Cupo"
                            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    </div>
                </div>
                <button type="button" class="btn-quitar-franja text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0 lg:ml-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @empty
            <div id="franjas-vacio" class="p-6 text-center text-sm text-slate-400">
                Sin franjas cargadas. Hacé clic en «Agregar franja» para definir días, horarios, duración y cupo.
            </div>
        @endforelse
    </div>
</div>

<script>
(function () {
    const container  = document.getElementById('franjas-container');
    const btnAgregar = document.getElementById('btn-agregar-franja');
    const dias = @json(\App\Models\Actividad::diasOrdenados());
    const etiquetas = @json(array_combine(\App\Models\Actividad::diasOrdenados(), array_map(fn($d) => \App\Models\Actividad::etiquetaDia($d), \App\Models\Actividad::diasOrdenados())));

    function indiceActual() {
        return container.querySelectorAll('.franja-fila').length;
    }

    function quitarVacio() {
        const vacio = document.getElementById('franjas-vacio');
        if (vacio) vacio.remove();
    }

    function mostrarVacioSiCorresponde() {
        if (container.querySelectorAll('.franja-fila').length === 0) {
            const div = document.createElement('div');
            div.id = 'franjas-vacio';
            div.className = 'p-6 text-center text-sm text-slate-400';
            div.textContent = 'Sin franjas cargadas. Hacé clic en «Agregar franja» para definir días, horarios, duración y cupo.';
            container.appendChild(div);
        }
    }

    function crearFila(idx) {
        const fila = document.createElement('div');
        fila.className = 'franja-fila flex flex-col lg:flex-row items-start lg:items-center gap-3 p-4 border-t border-slate-100';

        const select = document.createElement('select');
        select.name = `franjas[${idx}][dia_semana]`;
        select.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-full lg:w-36';
        dias.forEach(dia => {
            const opt = document.createElement('option');
            opt.value = dia;
            opt.textContent = etiquetas[dia];
            select.appendChild(opt);
        });

        const wrapHoras = document.createElement('div');
        wrapHoras.className = 'flex items-center gap-2 w-full lg:w-auto';

        const inputInicio = document.createElement('input');
        inputInicio.type = 'time';
        inputInicio.name = `franjas[${idx}][hora_inicio]`;
        inputInicio.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';

        const sep = document.createElement('span');
        sep.className = 'text-slate-400 text-sm shrink-0';
        sep.textContent = 'a';

        const inputFin = document.createElement('input');
        inputFin.type = 'time';
        inputFin.name = `franjas[${idx}][hora_fin]`;
        inputFin.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';

        wrapHoras.append(inputInicio, sep, inputFin);

        const wrapConfig = document.createElement('div');
        wrapConfig.className = 'flex items-center gap-2 w-full lg:w-auto';

        const wrapDuracion = document.createElement('div');
        wrapDuracion.className = 'flex-1 lg:w-32';
        const inputDuracion = document.createElement('input');
        inputDuracion.type = 'number';
        inputDuracion.min = '1';
        inputDuracion.step = '1';
        inputDuracion.name = `franjas[${idx}][duracion_minutos]`;
        inputDuracion.placeholder = 'Duración (min)';
        inputDuracion.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';
        wrapDuracion.appendChild(inputDuracion);

        const wrapCupo = document.createElement('div');
        wrapCupo.className = 'flex-1 lg:w-24';
        const inputCupo = document.createElement('input');
        inputCupo.type = 'number';
        inputCupo.min = '1';
        inputCupo.step = '1';
        inputCupo.name = `franjas[${idx}][cupo]`;
        inputCupo.placeholder = 'Cupo';
        inputCupo.className = 'text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full';
        wrapCupo.appendChild(inputCupo);

        wrapConfig.append(wrapDuracion, wrapCupo);

        const btnQuitar = document.createElement('button');
        btnQuitar.type = 'button';
        btnQuitar.className = 'btn-quitar-franja text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0 lg:ml-auto';
        btnQuitar.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>`;
        btnQuitar.addEventListener('click', () => { fila.remove(); mostrarVacioSiCorresponde(); });

        fila.append(select, wrapHoras, wrapConfig, btnQuitar);
        return fila;
    }

    btnAgregar.addEventListener('click', () => {
        quitarVacio();
        container.appendChild(crearFila(indiceActual()));
    });

    container.addEventListener('click', e => {
        const btn = e.target.closest('.btn-quitar-franja');
        if (btn) {
            btn.closest('.franja-fila').remove();
            mostrarVacioSiCorresponde();
        }
    });

    // Mostrar/ocultar costo según "Requiere pago previo"
    const checkPago = document.getElementById('requiere_pago');
    const costoWrapper = document.getElementById('costo-wrapper');
    function actualizarCosto() {
        costoWrapper.classList.toggle('hidden', !checkPago.checked);
    }
    checkPago.addEventListener('change', actualizarCosto);
    actualizarCosto();
})();
</script>
