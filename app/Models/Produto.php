<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model responsável pela tabela "produtos".
 *
 * Todos os métodos recebem o ID do usuário dono do estoque, garantindo
 * que cada usuário acesse apenas os seus próprios produtos.
 */
class Produto extends Model
{
    protected string $tabela = 'produtos';

    /**
     * Situações disponíveis para o filtro da listagem.
     */
    private const SITUACOES = [
        'tudo'           => '',
        'dentro'         => 'perecivel = 1 AND data_validade IS NOT NULL AND data_validade >= CURDATE()',
        'fora'           => 'perecivel = 1 AND data_validade IS NOT NULL AND data_validade < CURDATE()',
        'nao_pereciveis' => 'perecivel = 0',
    ];

    /**
     * Colunas permitidas para ordenação (evita SQL injection no ORDER BY).
     */
    private const ORDENACOES = [
        'nome'     => 'nome',
        'preco'    => 'preco',
        'validade' => 'data_validade',
    ];

    /**
     * Retorna os produtos do usuário aplicando filtro, busca e ordenação.
     *
     * @return array<int, array<string, mixed>>
     */
    public function todos(int $usuarioId, string $situacao = 'tudo', string $busca = '', string $ordem = 'nome'): array
    {
        $campoOrdem = self::ORDENACOES[$ordem] ?? 'nome';
        $condicao   = self::SITUACOES[$situacao] ?? '';

        $where  = ['usuario_id = :usuario_id'];
        $params = [':usuario_id' => $usuarioId];

        if ($condicao !== '') {
            $where[] = '(' . $condicao . ')';
        }

        if ($busca !== '') {
            $where[] = 'nome LIKE :busca';
            $params[':busca'] = '%' . $busca . '%';
        }

        $sql = "SELECT * FROM {$this->tabela}
                WHERE " . implode(' AND ', $where) . "
                ORDER BY {$campoOrdem} ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Busca um produto do usuário pelo ID.
     *
     * @return array<string, mixed>|null
     */
    public function buscar(int $id, int $usuarioId): ?array
    {
        $sql = "SELECT * FROM {$this->tabela}
                WHERE id = :id AND usuario_id = :usuario_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':usuario_id' => $usuarioId]);

        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    /**
     * Insere um novo produto e retorna o ID gerado.
     */
    public function criar(array $dados): int
    {
        $sql = "INSERT INTO {$this->tabela} (usuario_id, nome, preco, imagem, perecivel, data_validade)
                VALUES (:usuario_id, :nome, :preco, :imagem, :perecivel, :data_validade)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id'    => $dados['usuario_id'],
            ':nome'          => $dados['nome'],
            ':preco'         => $dados['preco'],
            ':imagem'        => $dados['imagem'],
            ':perecivel'     => $dados['perecivel'],
            ':data_validade' => $dados['data_validade'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um produto do usuário.
     */
    public function atualizar(int $id, int $usuarioId, array $dados): bool
    {
        $sql = "UPDATE {$this->tabela}
                   SET nome = :nome,
                       preco = :preco,
                       imagem = :imagem,
                       perecivel = :perecivel,
                       data_validade = :data_validade
                 WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nome'          => $dados['nome'],
            ':preco'         => $dados['preco'],
            ':imagem'        => $dados['imagem'],
            ':perecivel'     => $dados['perecivel'],
            ':data_validade' => $dados['data_validade'],
            ':id'            => $id,
            ':usuario_id'    => $usuarioId,
        ]);
    }

    /**
     * Remove um produto do usuário pelo ID.
     */
    public function excluir(int $id, int $usuarioId): bool
    {
        $sql = "DELETE FROM {$this->tabela} WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id, ':usuario_id' => $usuarioId]);
    }

    /**
     * Retorna os totais do estoque do usuário (painel de estatísticas).
     *
     * @return array{total:int, dentro:int, fora:int, nao_pereciveis:int, valor_total:float}
     */
    public function resumo(int $usuarioId): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(CASE WHEN perecivel = 1 AND data_validade IS NOT NULL AND data_validade >= CURDATE() THEN 1 ELSE 0 END), 0) AS dentro,
                    COALESCE(SUM(CASE WHEN perecivel = 1 AND data_validade IS NOT NULL AND data_validade < CURDATE() THEN 1 ELSE 0 END), 0) AS fora,
                    COALESCE(SUM(CASE WHEN perecivel = 0 THEN 1 ELSE 0 END), 0) AS nao_pereciveis,
                    COALESCE(SUM(preco), 0) AS valor_total
                FROM {$this->tabela}
                WHERE usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        $resumo = $stmt->fetch();

        return [
            'total'          => (int) $resumo['total'],
            'dentro'         => (int) $resumo['dentro'],
            'fora'           => (int) $resumo['fora'],
            'nao_pereciveis' => (int) $resumo['nao_pereciveis'],
            'valor_total'    => (float) $resumo['valor_total'],
        ];
    }
}
