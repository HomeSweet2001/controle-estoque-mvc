<?php

namespace App\Services;

use App\Models\Usuario;

/**
 * Serviço com as regras de negócio relacionadas a usuários.
 *
 * Centraliza a validação e o cadastro para serem reutilizados tanto
 * pelo autocadastro (tela pública) quanto pela gestão interna de usuários.
 */
class UsuarioService
{
    private Usuario $usuarioModel;

    public function __construct(?Usuario $usuarioModel = null)
    {
        $this->usuarioModel = $usuarioModel ?? new Usuario();
    }

    /**
     * Valida os dados de um usuário.
     *
     * @param array{nome?:string, login?:string} $dados
     * @return array<int, string> Lista de mensagens de erro (vazia se válido).
     */
    public function validar(array $dados, string $senha, string $confirmacao): array
    {
        $erros = [];

        $nome  = trim($dados['nome'] ?? '');
        $login = trim($dados['login'] ?? '');

        if ($nome === '') {
            $erros[] = 'O nome é obrigatório.';
        }

        if ($login === '') {
            $erros[] = 'O usuário (login) é obrigatório.';
        } elseif (strlen($login) < 3) {
            $erros[] = 'O usuário deve ter pelo menos 3 caracteres.';
        }

        if ($senha === '') {
            $erros[] = 'A senha é obrigatória.';
        } elseif (strlen($senha) < 3) {
            $erros[] = 'A senha deve ter pelo menos 3 caracteres.';
        }

        if ($senha !== $confirmacao) {
            $erros[] = 'A confirmação da senha não confere.';
        }

        if (empty($erros) && $this->usuarioModel->loginExiste($login)) {
            $erros[] = 'Já existe um usuário com esse login.';
        }

        return $erros;
    }

    /**
     * Cadastra um novo usuário (senha gravada como hash).
     *
     * @param array{nome:string, login:string} $dados
     */
    public function cadastrar(array $dados, string $senha): int
    {
        return $this->usuarioModel->criar([
            'nome'  => trim($dados['nome']),
            'login' => trim($dados['login']),
            'senha' => $senha,
        ]);
    }
}
