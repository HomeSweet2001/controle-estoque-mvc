<?php

namespace App\Core;

/**
 * Gerenciador de autenticação baseado em sessão.
 *
 * Centraliza o controle de login/logout e o acesso ao usuário logado,
 * evitando espalhar manipulação de $_SESSION pela aplicação.
 */
class Auth
{
    /**
     * Inicia a sessão (deve ser chamado uma única vez, no front controller).
     */
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Indica se há um usuário autenticado na sessão.
     */
    public static function autenticado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Retorna os dados do usuário logado (ou null).
     *
     * @return array{id:int, nome:string, login:string}|null
     */
    public static function usuario(): ?array
    {
        if (!self::autenticado()) {
            return null;
        }

        return [
            'id'    => (int) $_SESSION['usuario_id'],
            'nome'  => (string) ($_SESSION['usuario_nome'] ?? ''),
            'login' => (string) ($_SESSION['usuario_login'] ?? ''),
        ];
    }

    /**
     * Registra o usuário na sessão (efetiva o login).
     *
     * @param array<string, mixed> $usuario Registro vindo do banco.
     */
    public static function entrar(array $usuario): void
    {
        // Renova o ID da sessão para prevenir fixação de sessão
        session_regenerate_id(true);

        $_SESSION['usuario_id']    = (int) $usuario['id'];
        $_SESSION['usuario_nome']  = (string) $usuario['nome'];
        $_SESSION['usuario_login'] = (string) $usuario['login'];
    }

    /**
     * Encerra a sessão do usuário (logout).
     */
    public static function sair(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
