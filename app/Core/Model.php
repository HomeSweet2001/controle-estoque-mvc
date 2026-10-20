<?php

namespace App\Core;

use App\Config\Database;
use PDO;

/**
 * Model base.
 *
 * Fornece a conexão com o banco de dados para todos os Models
 * concretos da aplicação.
 */
abstract class Model
{
    /** Conexão PDO compartilhada. */
    protected PDO $db;

    /** Nome da tabela principal do Model. */
    protected string $tabela = '';

    public function __construct()
    {
        $this->db = Database::conexao();
    }
}
