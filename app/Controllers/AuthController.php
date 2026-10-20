<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;
use App\Services\UsuarioService;

/**
 * Controller de autenticação: login, autocadastro e logout.
 */
class AuthController extends Controller
{
    private Usuario $usuarioModel;
    private UsuarioService $usuarioService;

    public function __construct()
    {
        $this->usuarioModel   = new Usuario();
        $this->usuarioService = new UsuarioService($this->usuarioModel);
    }

    /**
     * GET /login
     * Exibe o formulário de login.
     */
    public function mostrarLogin(): void
    {
        // Usuário já autenticado não precisa ver o login
        if (Auth::autenticado()) {
            $this->redirect('');
        }

        $this->render('auth/login', [
            'tituloPagina' => 'Entrar',
            'login'        => '',
            'erros'        => [],
            'mensagem'     => $_GET['mensagem'] ?? '',
        ]);
    }

    /**
     * POST /login
     * Valida as credenciais e inicia a sessão.
     */
    public function autenticar(): void
    {
        if (Auth::autenticado()) {
            $this->redirect('');
        }

        $login = trim($_POST['login'] ?? '');
        $senha = (string) ($_POST['senha'] ?? '');
        $erros = [];

        // ---- Validações básicas -------------------------------------------
        if ($login === '') {
            $erros[] = 'Informe o usuário.';
        }
        if ($senha === '') {
            $erros[] = 'Informe a senha.';
        }

        // ---- Verificação das credenciais ----------------------------------
        if (empty($erros)) {
            $usuario = $this->usuarioModel->buscarPorLogin($login);

            // Mensagem genérica: não revela se o erro foi no usuário ou na senha
            if ($usuario === null || !$this->usuarioModel->verificarSenha($senha, $usuario['senha'])) {
                $erros[] = 'Usuário ou senha inválidos.';
            } else {
                Auth::entrar($usuario);
                $this->redirect('');
            }
        }

        // Em caso de erro, devolve o login digitado e volta ao formulário
        $this->render('auth/login', [
            'tituloPagina' => 'Entrar',
            'login'        => $login,
            'erros'        => $erros,
            'mensagem'     => '',
        ]);
    }

    /**
     * GET /logout
     * Encerra a sessão e volta para a tela de login.
     */
    public function logout(): void
    {
        Auth::sair();
        $this->redirect('login', ['mensagem' => 'Você saiu do sistema.']);
    }

    /**
     * GET /registrar
     * Exibe o formulário de autocadastro (rota pública).
     */
    public function mostrarRegistro(): void
    {
        if (Auth::autenticado()) {
            $this->redirect('');
        }

        $this->render('auth/registrar', [
            'tituloPagina' => 'Criar conta',
            'usuario'      => ['nome' => '', 'login' => ''],
            'erros'        => [],
        ]);
    }

    /**
     * POST /registrar
     * Valida e cria uma nova conta, já autenticando o usuário.
     */
    public function registrar(): void
    {
        if (Auth::autenticado()) {
            $this->redirect('');
        }

        $usuario = [
            'nome'  => trim($_POST['nome'] ?? ''),
            'login' => trim($_POST['login'] ?? ''),
        ];

        $senha       = (string) ($_POST['senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmacao'] ?? '');

        $erros = $this->usuarioService->validar($usuario, $senha, $confirmacao);

        if (empty($erros)) {
            $this->usuarioService->cadastrar($usuario, $senha);

            // Após o cadastro, o usuário é enviado para a tela de login
            $this->redirect('login', ['mensagem' => 'Conta criada com sucesso! Faça login para continuar.']);
        }

        $this->render('auth/registrar', [
            'tituloPagina' => 'Criar conta',
            'usuario'      => $usuario,
            'erros'        => $erros,
        ]);
    }
}
