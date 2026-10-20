<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Camada de acesso ao banco de dados.
 *
 * Implementa o padrão Singleton: garante uma única conexão PDO
 * compartilhada por toda a aplicação.
 */
class Database
{
    private static ?PDO $conexao = null;

    /** Impede a instanciação direta da classe. */
    private function __construct()
    {
    }

    /**
     * Retorna a conexão PDO (criando-a na primeira chamada).
     */
    public static function conexao(): PDO
    {
        if (self::$conexao === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            $opcoes = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$conexao = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
            } catch (PDOException $e) {
                http_response_code(500);
                die(
                    'Não foi possível conectar ao banco de dados. ' .
                    'Verifique se o MySQL está ativo no XAMPP e se o arquivo ' .
                    'database/data.sql foi importado. Detalhe técnico: ' .
                    htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                );
            }
        }

        return self::$conexao;
    }
}
