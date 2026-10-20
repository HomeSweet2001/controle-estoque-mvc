<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Router;

/**
 * ============================================================================
 *  Front Controller — Sistema de Controle de Estoque (Arquitetura MVC)
 * ============================================================================
 *
 *  Este é o único arquivo acessado diretamente pelo navegador. Ele:
 *    1. Define o caminho base da aplicação;
 *    2. Carrega as configurações e os helpers;
 *    3. Registra o autoloader PSR-4 (namespace App\ => pasta app/);
 *    4. Resolve a URI da requisição;
 *    5. Registra as rotas e despacha para o Controller correto.
 */

// ---------------------------------------------------------------------------
// Exibição de erros (recomenda-se desativar em produção)
// ---------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---------------------------------------------------------------------------
// Caminho base — calculado dinamicamente para funcionar tanto na raiz do
// htdocs quanto dentro de uma subpasta (ex.: /controle-estoque/)
// ---------------------------------------------------------------------------
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$scriptDir = rtrim($scriptDir, '/');
define('BASE_URL', ($scriptDir === '' || $scriptDir === '.') ? '' : $scriptDir);

// ---------------------------------------------------------------------------
// Configurações e funções auxiliares
// ---------------------------------------------------------------------------
require __DIR__ . '/app/Config/config.php';
require __DIR__ . '/app/Core/helpers.php';

// ---------------------------------------------------------------------------
// Autoloader PSR-4 simples: App\Alguma\Coisa => app/Alguma/Coisa.php
// ---------------------------------------------------------------------------
spl_autoload_register(static function (string $classe): void {
    $prefixo = 'App\\';

    if (strncmp($classe, $prefixo, strlen($prefixo)) !== 0) {
        return;
    }

    $relativo = substr($classe, strlen($prefixo));
    $arquivo  = __DIR__ . '/app/' . str_replace('\\', '/', $relativo) . '.php';

    if (is_file($arquivo)) {
        require $arquivo;
    }
});

// ---------------------------------------------------------------------------
// Sessão (necessária para a autenticação)
// ---------------------------------------------------------------------------
Auth::iniciar();

// ---------------------------------------------------------------------------
// Resolve a URI requisitada (removendo o caminho base)
// ---------------------------------------------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (BASE_URL !== '' && strpos($uri, BASE_URL) === 0) {
    $uri = substr($uri, strlen(BASE_URL));
}

$uri = '/' . trim($uri, '/');

// Acesso direto ao index.php é tratado como a rota raiz
if ($uri === '/index.php') {
    $uri = '/';
}

// ---------------------------------------------------------------------------
// Definição das rotas
// ---------------------------------------------------------------------------
$router = new Router();

// Autenticação (rotas públicas)
$router->get('/login',  'AuthController@mostrarLogin');
$router->post('/login', 'AuthController@autenticar');
$router->get('/registrar',  'AuthController@mostrarRegistro');
$router->post('/registrar', 'AuthController@registrar');
$router->get('/logout', 'AuthController@logout');

// Produtos (rotas protegidas — exigem login)
$router->get('/',  'ProdutoController@index');
$router->get('/produtos', 'ProdutoController@index');

$router->get('/produtos/criar',  'ProdutoController@criar');
$router->post('/produtos/criar', 'ProdutoController@salvar');

$router->get('/produtos/editar/{id}',  'ProdutoController@editar');
$router->post('/produtos/editar/{id}', 'ProdutoController@atualizar');

$router->get('/produtos/excluir/{id}', 'ProdutoController@excluir');

// Usuários (rotas protegidas — exigem login)
$router->get('/usuarios',  'UsuarioController@index');
$router->get('/usuarios/criar',  'UsuarioController@criar');
$router->post('/usuarios/criar', 'UsuarioController@salvar');
$router->get('/usuarios/excluir/{id}', 'UsuarioController@excluir');

// ---------------------------------------------------------------------------
// Despacha a requisição
// ---------------------------------------------------------------------------
$router->despachar($_SERVER['REQUEST_METHOD'] ?? 'GET', $uri);
