<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model responsável pela tabela "usuarios".
 */
class Usuario extends Model
{
    protected string $tabela = 'usuarios';

    /**
     * Busca um usuário pelo login.
     *
     * @return array<string, mixed>|null
     */
    public function buscarPorLogin(string $login): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabela} WHERE login = :login LIMIT 1");
        $stmt->execute([':login' => $login]);

        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /**
     * Busca um usuário pelo ID.
     *
     * @return array<string, mixed>|null
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabela} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /**
     * Lista todos os usuários (sem expor o hash da senha).
     *
     * @return array<int, array<string, mixed>>
     */
    public function todos(): array
    {
        $sql = "SELECT id, nome, login, criado_em FROM {$this->tabela} ORDER BY nome ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Verifica se um login já está em uso.
     *
     * @param int|null $ignorarId Ignora este ID (útil em edições).
     */
    public function loginExiste(string $login, ?int $ignorarId = null): bool
    {
        $sql    = "SELECT COUNT(*) FROM {$this->tabela} WHERE login = :login";
        $params = [':login' => $login];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $ignorarId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Cria um novo usuário, gravando a senha como hash bcrypt.
     *
     * @param array{nome:string, login:string, senha:string} $dados
     */
    public function criar(array $dados): int
    {
        $sql = "INSERT INTO {$this->tabela} (nome, login, senha)
                VALUES (:nome, :login, :senha)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'  => $dados['nome'],
            ':login' => $dados['login'],
            ':senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Remove um usuário pelo ID.
     */
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabela} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retorna a quantidade total de usuários cadastrados.
     */
    public function contar(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->tabela}")->fetchColumn();
    }

    /**
     * Verifica se a senha informada corresponde ao hash armazenado.
     */
    public function verificarSenha(string $senha, string $hash): bool
    {
        return password_verify($senha, $hash);
    }
}
