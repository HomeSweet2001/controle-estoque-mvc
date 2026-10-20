<?php

namespace App\Core;

/**
 * Controller base.
 *
 * Oferece a infraestrutura comum a todos os controllers:
 * renderização de Views (com layout), redirecionamentos e proteção de rotas.
 */
abstract class Controller
{
    /**
     * Exige um usuário autenticado. Redireciona para o login caso contrário.
     */
    protected function exigirLogin(): void
    {
        if (!Auth::autenticado()) {
            $this->redirect('login');
        }
    }

    /**
     * Renderiza uma View envolvida pelo layout padrão.
     *
     * @param string $view  Caminho relativo dentro de app/Views (sem .php).
     * @param array  $dados Variáveis disponibilizadas para a View.
     */
    protected function render(string $view, array $dados = []): void
    {
        // Torna as chaves do array disponíveis como variáveis na View
        extract($dados, EXTR_SKIP);

        $caminhoView = __DIR__ . '/../Views/' . $view . '.php';

        if (!is_file($caminhoView)) {
            http_response_code(500);
            die('View não encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8'));
        }

        require __DIR__ . '/../Views/layout/header.php';
        require $caminhoView;
        require __DIR__ . '/../Views/layout/footer.php';
    }

    /**
     * Redireciona para uma rota interna, opcionalmente com parâmetros.
     */
    protected function redirect(string $rota = '', array $params = []): void
    {
        $destino = url($rota);
        if (!empty($params)) {
            $destino .= '?' . http_build_query($params);
        }

        header('Location: ' . $destino);
        exit;
    }
}
