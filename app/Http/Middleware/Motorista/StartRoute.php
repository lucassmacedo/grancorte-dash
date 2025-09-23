<?php

namespace App\Http\Middleware\Motorista;

use App\Models\LogisticaEntregaInicio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StartRoute
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $started = LogisticaEntregaInicio::where('carga', auth()->user()->carga)
            ->where('placa', auth()->user()->placa)
            ->first();
        
        if (!$started) {
            return redirect()->route('motoristas.startRoute');
        }

        return $next($request);
    }
}
