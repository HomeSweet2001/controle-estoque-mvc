<?php
/**
 * Configurações gerais da aplicação.
 *
 * Aqui ficam as credenciais do banco de dados e os parâmetros de upload.
 * Ajuste os valores conforme o seu ambiente (XAMPP usa os padrões abaixo).
 */

// ---------------------------------------------------------------------------
// Banco de dados (string de conexão)
// ---------------------------------------------------------------------------
define('DB_HOST', 'localhost');        // Host do MySQL
define('DB_PORT', '3306');             // Porta do MySQL
define('DB_NAME', 'controle_estoque'); // Banco criado pelo database/data.sql
define('DB_USER', 'root');             // Usuário do MySQL
define('DB_PASS', '');                 // Senha do MySQL (XAMPP: vazia)
define('DB_CHARSET', 'utf8mb4');       // Codificação (suporta acentuação)

// ---------------------------------------------------------------------------
// Upload de imagens
// ---------------------------------------------------------------------------
// Diretório físico (no sistema de arquivos) onde as imagens são gravadas
define('UPLOAD_DIR', dirname(__DIR__, 2) . '/uploads/');

// Caminho público usado nas tags <img>
define('UPLOAD_URL', 'uploads/');

// Tamanho máximo aceito para cada imagem (2 MB)
define('TAMANHO_MAX_IMAGEM', 2 * 1024 * 1024);
