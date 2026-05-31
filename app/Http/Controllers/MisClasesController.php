<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDisciplina;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MisClasesController extends Controller
{
    public function index(Request $request): View
    {
        $profesor     = auth()->user()->profesor;
        $disciplinas  = $profesor->disciplinas()->where('estado', 'activa')->with('horarios')->orderBy('nombre')->get();

        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        // Clases tomadas este mes por disciplina (fechas distintas con registros)
        $clasesMes = AsistenciaDisciplina::whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->whereIn('disciplina_id', $disciplinas->pluck('id'))
            ->selectRaw('disciplina_id, COUNT(DISTINCT fecha) as total')
            ->groupBy('disciplina_id')
            ->get()
            ->keyBy('disciplina_id');

        return view('profesor.mis-clases', compact('disciplinas', 'clasesMes'));
    }
}
