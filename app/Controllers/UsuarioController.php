<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;
use App\Services\UsuarioService;

/**
 * Controller de gestão de usuários (área autenticada).
 *
 * Permite listar, cadastrar e excluir usuários do sistema.
 */
class UsuarioController extends Controller
{
    private Usuario $usuarioModel;
    private UsuarioService $usuarioService;

    public function __construct()
    {
        // Somente usuários autenticados podem gerenciar usuários
        $this->exigirLogin();

        $this->usuarioModel   = new Usuario();
        $this->usuarioService = new UsuarioService($this->usuarioModel);
    }

    /**
     * GET /usuarios
     * Lista os usuários cadastrados.
     */
    public function index(): void
    {
        $this->render('usuarios/index', [
            'tituloPagina' => 'Usuários',
            'usuarios'     => $this->usuarioModel->todos(),
            'usuarioAtual' => Auth::usuario(),
            'mensagem'     => $_GET['mensagem'] ?? '',
            'erro'         => $_GET['erro'] ?? '',
        ]);
    }

    /**
     * GET /usuarios/criar
     * Exibe o formulário de cadastro de usuário.
     */
    public function criar(): void
    {
        $this->render('usuarios/form', [
            'tituloPagina' => 'Novo usuário',
            'usuario'      => ['nome' => '', 'login' => ''],
            'erros'        => [],
            'acao'         => url('usuarios/criar'),
            'rotuloBotao'  => 'Cadastrar usuário',
        ]);
    }

    /**
     * POST /usuarios/criar
     * Valida e grava um novo usuário.
     */
    public function salvar(): void
    {
        $usuario = [
            'nome'  => trim($_POST['nome'] ?? ''),
            'login' => trim($_POST['login'] ?? ''),
        ];

        $senha       = (string) ($_POST['senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmacao'] ?? '');

        $erros = $this->usuarioService->validar($usuario, $senha, $confirmacao);

        // ---- Persistência -------------------------------------------------
        if (empty($erros)) {
            $this->usuarioService->cadastrar($usuario, $senha);

            $this->redirect('usuarios', ['mensagem' => 'Usuário cadastrado com sucesso!']);
        }

        $this->render('usuarios/form', [
            'tituloPagina' => 'Novo usuário',
            'usuario'      => $usuario,
            'erros'        => $erros,
            'acao'         => url('usuarios/criar'),
            'rotuloBotao'  => 'Cadastrar usuário',
        ]);
    }

    /**
     * GET /usuarios/excluir/{id}
     * Remove um usuário, com proteções básicas.
     */
    public function excluir(string $id): void
    {
        $id      = (int) $id;
        $usuario = $this->usuarioModel->buscarPorId($id);

        if ($usuario === null) {
            $this->redirect('usuarios', ['erro' => 'Usuário não encontrado.']);
        }

        // Não permite excluir o próprio usuário logado
        $logado = Auth::usuario();
        if ($logado !== null && $logado['id'] === $id) {
            $this->redirect('usuarios', ['erro' => 'Você não pode excluir o próprio usuário.']);
        }

        // Não permite ficar sem nenhum usuário no sistema
        if ($this->usuarioModel->contar() <= 1) {
            $this->redirect('usuarios', ['erro' => 'Não é possível excluir o último usuário do sistema.']);
        }

        $this->usuarioModel->excluir($id);

        $this->redirect('usuarios', ['mensagem' => 'Usuário excluído com sucesso!']);
    }
}
