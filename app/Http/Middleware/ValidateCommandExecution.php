<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateCommandExecution
{
    /**
     * Lista de comandos permitidos para execução manual
     */
    private const ALLOWED_COMMANDS = [
        'import:clientes',
        'import:vendedores',
        'import:dashboard',
        'import:produtos',
        'import:logistica',
        'import:notas',
        'import:pedidos-faturados',
        'import:titulos',
        'clientes:generate-score',
        'import:pendencias-financeiras',
        'update:latlong'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Aplicar validação apenas para rotas de execução de comando
        if ($request->routeIs('command-executor.execute')) {
            $command = $request->input('command');
            
            if (!$command) {
                Log::warning('Tentativa de execução sem comando especificado', [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Comando não especificado'
                ], 400);
            }
            
            if (!in_array($command, self::ALLOWED_COMMANDS)) {
                Log::warning('Tentativa de execução de comando não autorizado via middleware', [
                    'command' => $command,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? 'Usuário não identificado',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'allowed_commands' => self::ALLOWED_COMMANDS
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Comando não autorizado para execução manual. Apenas comandos definidos no agendador são permitidos.',
                    'command' => $command
                ], 403);
            }
        }

        return $next($request);
    }
    
    /**
     * Retorna a lista de comandos permitidos
     * 
     * @return array
     */
    public static function getAllowedCommands()
    {
        return self::ALLOWED_COMMANDS;
    }
}
