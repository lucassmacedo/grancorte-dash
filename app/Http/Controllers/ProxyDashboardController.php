<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyDashboardController extends Controller
{
    public function show(Request $request)
    {
        $url = $request->query('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return view('proxy-error', [
                'message' => 'URL inválida',
                'code' => 400
            ]);
        }

        try {
            // Baixa o HTML do dashboard externo com timeout configurável
            $response = Http::withoutVerifying()
                ->timeout(10) // Timeout de 10 segundos para não travar muito
                ->connectTimeout(5) // Timeout de conexão de 5 segundos
                ->get($url);

            if (!$response->successful()) {
                \Log::warning('Dashboard proxy retornou erro HTTP', [
                    'url' => $url,
                    'status' => $response->status(),
                    'request_url' => $request->fullUrl()
                ]);
                return response()->view('proxy-error', [
                    'message' => 'Dashboard OEEtv temporariamente indisponível',
                    'code' => 502
                ], 502);
            }

            $html = $response->body();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Timeout ao acessar dashboard proxy', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $url,
                'request_url' => $request->fullUrl()
            ]);
            return response()->view('proxy-error', [
                'message' => 'Dashboard OEEtv não respondeu a tempo',
                'code' => 504
            ], 504);
        } catch (\Throwable $e) {
            \Log::error('Erro ao acessar dashboard proxy', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $url,
                'request_url' => $request->fullUrl()
            ]);
            return response()->view('proxy-error', [
                'message' => 'Erro ao carregar dashboard OEEtv',
                'code' => 502
            ], 502);
        }

        // Corrige caminhos relativos para absolutos
        $baseUrl = preg_replace('/\/[^\/]*$/', '/', $url); // pega o diretório base da URL
        $hostUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);

        // Corrige href/src começando com "css/", "js/", "images/", etc.
        $html = preg_replace_callback(
            '/(href|src)=["\']((?!https?:|\/)[^"\']+)/i',
            function ($matches) use ($baseUrl) {
                return $matches[1] . '="' . $baseUrl . $matches[2] . '"';
            },
            $html
        );
        // Corrige href/src começando com "/" (raiz)
        $html = preg_replace_callback(
            '/(href|src)=["\']\/(?!\/)([^"\']+)/i',
            function ($matches) use ($hostUrl) {
                return $matches[1] . '="' . $hostUrl . '/' . $matches[2] . '"';
            },
            $html
        );


        return response($html, 200)->header('Content-Type', 'text/html');
    }
}
