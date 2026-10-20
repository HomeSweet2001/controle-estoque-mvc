<?php
/**
 * Funções auxiliares globais (helpers) usadas pelas Views e Controllers.
 *
 * Este arquivo NÃO declara namespace — as funções ficam disponíveis
 * globalmente em toda a aplicação.
 */

if (!function_exists('e')) {
    /**
     * Escapa texto para exibição segura em HTML (previne XSS).
     */
    function e(?string $texto): string
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatarPreco')) {
    /**
     * Formata um número como moeda brasileira (R$ 0,00).
     */
    function formatarPreco($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (!function_exists('formatarData')) {
    /**
     * Formata uma data no padrão brasileiro (d/m/Y).
     */
    function formatarData(?string $data): string
    {
        if (empty($data)) {
            return '—';
        }
        $timestamp = strtotime($data);
        return $timestamp ? date('d/m/Y', $timestamp) : '—';
    }
}

if (!function_exists('validadeVencida')) {
    /**
     * Indica se uma data de validade já passou.
     */
    function validadeVencida(?string $data): bool
    {
        if (empty($data)) {
            return false;
        }
        return strtotime($data) < strtotime(date('Y-m-d'));
    }
}

if (!function_exists('diasParaVencer')) {
    /**
     * Retorna quantos dias faltam para vencer (negativo se já venceu).
     */
    function diasParaVencer(?string $data): ?int
    {
        if (empty($data)) {
            return null;
        }
        $hoje  = new DateTime(date('Y-m-d'));
        $final = new DateTime($data);
        return (int) $hoje->diff($final)->format('%r%a');
    }
}

if (!function_exists('url')) {
    /**
     * Monta uma URL interna a partir do caminho base da aplicação.
     * Ex.: url('produtos/criar') => /controle-estoque/produtos/criar
     */
    function url(string $rota = ''): string
    {
        $rota = ltrim($rota, '/');
        return BASE_URL . ($rota === '' ? '/' : '/' . $rota);
    }
}

if (!function_exists('asset')) {
    /**
     * Monta a URL de um recurso estático (CSS, imagem, etc.).
     * Ex.: asset('assets/css/style.css')
     */
    function asset(string $caminho): string
    {
        return BASE_URL . '/' . ltrim($caminho, '/');
    }
}

if (!function_exists('autenticado')) {
    /**
     * Indica se há um usuário logado.
     */
    function autenticado(): bool
    {
        return \App\Core\Auth::autenticado();
    }
}

if (!function_exists('usuario_logado')) {
    /**
     * Retorna os dados do usuário logado (ou null).
     *
     * @return array{id:int, nome:string, login:string}|null
     */
    function usuario_logado(): ?array
    {
        return \App\Core\Auth::usuario();
    }
}
