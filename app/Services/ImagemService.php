<?php

namespace App\Services;

/**
 * Serviço responsável por validar, salvar e remover as imagens dos produtos.
 *
 * Mantém a lógica de upload fora do Controller (separação de responsabilidades).
 */
class ImagemService
{
    /** Tipos MIME aceitos => extensão de destino. */
    private const TIPOS_PERMITIDOS = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
    ];

    /** Diretório físico onde as imagens são gravadas. */
    private string $diretorio;

    public function __construct(?string $diretorio = null)
    {
        $this->diretorio = $diretorio ?? UPLOAD_DIR;
    }

    /**
     * Processa o arquivo enviado pelo formulário.
     *
     * @param array $arquivo Item de $_FILES (ex.: $_FILES['imagem']).
     * @return array{erro: ?string, arquivo: ?string}
     *         "arquivo" é o nome do arquivo salvo, ou null se nenhum foi enviado.
     */
    public function salvar(array $arquivo): array
    {
        // Campo opcional: nenhum arquivo enviado
        if (!isset($arquivo['error']) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            return ['erro' => null, 'arquivo' => null];
        }

        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Ocorreu um erro ao enviar a imagem. Tente novamente.', 'arquivo' => null];
        }

        if ($arquivo['size'] > TAMANHO_MAX_IMAGEM) {
            return ['erro' => 'A imagem não pode ser maior que 2 MB.', 'arquivo' => null];
        }

        // Valida o tipo real do arquivo (não confia apenas na extensão)
        $info = @getimagesize($arquivo['tmp_name']);
        if ($info === false || !isset(self::TIPOS_PERMITIDOS[$info['mime']])) {
            return ['erro' => 'Formato inválido. Envie uma imagem PNG ou JPG.', 'arquivo' => null];
        }

        $extensao    = self::TIPOS_PERMITIDOS[$info['mime']];
        $nomeArquivo = 'produto_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;

        if (!is_dir($this->diretorio)) {
            mkdir($this->diretorio, 0777, true);
        }

        if (!move_uploaded_file($arquivo['tmp_name'], $this->diretorio . $nomeArquivo)) {
            return ['erro' => 'Não foi possível salvar a imagem no servidor.', 'arquivo' => null];
        }

        return ['erro' => null, 'arquivo' => $nomeArquivo];
    }

    /**
     * Remove uma imagem do diretório de uploads.
     */
    public function remover(?string $nomeArquivo): void
    {
        if (empty($nomeArquivo)) {
            return;
        }

        $caminho = $this->diretorio . basename($nomeArquivo);
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }
}
