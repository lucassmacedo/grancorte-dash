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
            abort(400, 'URL inválida');
        }

        // Baixa o HTML do dashboard externo
        $response = Http::withoutVerifying()->get($url);
        $html     = $response->body();

        // Corrige caminhos relativos para absolutos
        $baseUrl = preg_replace('/\/[^\/]*$/', '/', $url); // pega o diretório base da URL
        $hostUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);

        // Corrige href/src começando com "css/", "js/", "images/", etc.
        $html = preg_replace_callback(
            '/(href|src)=["\\\']((?!https?:|\/)[^"\\\']+)/i',
            function ($matches) use ($baseUrl) {
                return $matches[1] . '="' . $baseUrl . $matches[2] . '"';
            },
            $html
        );
        // Corrige href/src começando com "/" (raiz)
        $html = preg_replace_callback(
            '/(href|src)=["\\\']\/(?!\/)([^"\\\']+)/i',
            function ($matches) use ($hostUrl) {
                return $matches[1] . '="' . $hostUrl . '/' . $matches[2] . '"';
            },
            $html
        );

        return response($html, 200)->header('Content-Type', 'text/html');
    }
}
