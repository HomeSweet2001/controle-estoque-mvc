<?php

namespace App\Core;

/**
 * Roteador simples da aplicação.
 *
 * Registra rotas (GET/POST) e despacha a requisição para o controller
 * correspondente. Suporta parâmetros dinâmicos na URL, como {id}.
 */
class Router
{
    /** @var array<string, array<int, array{padrao: string, acao: string}>> */
    private array $rotas = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $rota, string $acao): void
    {
        $this->adicionar('GET', $rota, $acao);
    }

    public function post(string $rota, string $acao): void
    {
        $this->adicionar('POST', $rota, $acao);
    }

    private function adicionar(string $metodo, string $rota, string $acao): void
    {
        // Converte {id} em um grupo de captura de regex: ([^/]+)
        $padrao = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $rota);

        $this->rotas[$metodo][] = [
            'padrao' => '#^' . $padrao . '$#',
            'acao'   => $acao,
        ];
    }

    /**
     * Encontra a rota correspondente e executa o controller.
     */
    public function despachar(string $metodo, string $uri): void
    {
        $metodo = strtoupper($metodo);

        foreach ($this->rotas[$metodo] ?? [] as $rota) {
            if (preg_match($rota['padrao'], $uri, $capturas)) {
                array_shift($capturas); // remove a correspondência completa

                [$classe, $acao] = explode('@', $rota['acao']);
                $fqcn = 'App\\Controllers\\' . $classe;

                if (!class_exists($fqcn)) {
                    $this->naoEncontrado();
                    return;
                }

                $controller = new $fqcn();
                call_user_func_array([$controller, $acao], $capturas);
                return;
            }
        }

        $this->naoEncontrado();
    }

    private function naoEncontrado(): void
    {
        http_response_code(404);
        require __DIR__ . '/../Views/erros/404.php';
    }
}
