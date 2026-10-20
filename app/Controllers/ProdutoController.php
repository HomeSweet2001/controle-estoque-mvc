<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Produto;
use App\Services\ImagemService;

/**
 * Controller de Produtos.
 *
 * Recebe as requisições do roteador, aciona o Model/Serviços e
 * entrega os dados para as Views. Todas as ações operam apenas sobre
 * o estoque do usuário autenticado.
 */
class ProdutoController extends Controller
{
    private Produto $produtoModel;
    private ImagemService $imagemService;
    private int $usuarioId;

    public function __construct()
    {
        // Todas as ações de produtos exigem usuário autenticado
        $this->exigirLogin();

        $this->produtoModel  = new Produto();
        $this->imagemService = new ImagemService();

        // Usuário dono do estoque manipulado nesta requisição
        $usuario = Auth::usuario();
        $this->usuarioId = (int) $usuario['id'];
    }

    /**
     * GET /
     * Lista os produtos do usuário com filtros, busca e ordenação.
     */
    public function index(): void
    {
        $situacoes = ['tudo', 'dentro', 'fora', 'nao_pereciveis'];

        $filtro = $_GET['filtro'] ?? 'tudo';
        if (!in_array($filtro, $situacoes, true)) {
            $filtro = 'tudo';
        }

        $ordensPermitidas = ['nome', 'preco', 'validade'];
        $ordem = $_GET['ordem'] ?? 'nome';
        if (!in_array($ordem, $ordensPermitidas, true)) {
            $ordem = 'nome';
        }

        $busca = trim($_GET['busca'] ?? '');

        $produtos = $this->produtoModel->todos($this->usuarioId, $filtro, $busca, $ordem);
        $resumo   = $this->produtoModel->resumo($this->usuarioId);

        // Parâmetros preservados ao trocar de filtro (busca + ordenação)
        $paramsBase = array_filter([
            'busca' => $busca !== '' ? $busca : null,
            'ordem' => $ordem !== 'nome' ? $ordem : null,
        ], static fn($valor) => $valor !== null);

        $linksFiltro = [];
        foreach ($situacoes as $chave) {
            $linksFiltro[$chave] = url('') . '?' . http_build_query(
                array_merge($paramsBase, ['filtro' => $chave])
            );
        }

        // Link "Limpar": remove a busca e a ordenação, mantendo o filtro ativo
        $linkLimpar = url('') . '?filtro=' . $filtro;

        $rotulos = [
            'tudo'           => 'Tudo',
            'dentro'         => 'Dentro da validade',
            'fora'           => 'Fora da validade',
            'nao_pereciveis' => 'Não perecíveis',
        ];

        $this->render('produtos/index', [
            'tituloPagina' => 'Produtos',
            'produtos'     => $produtos,
            'resumo'       => $resumo,
            'filtro'       => $filtro,
            'busca'        => $busca,
            'ordem'        => $ordem,
            'linksFiltro'  => $linksFiltro,
            'linkLimpar'   => $linkLimpar,
            'rotulos'      => $rotulos,
            'mensagem'     => $_GET['mensagem'] ?? '',
        ]);
    }

    /**
     * GET /produtos/criar
     * Exibe o formulário de cadastro.
     */
    public function criar(): void
    {
        $this->render('produtos/form', [
            'tituloPagina' => 'Novo produto',
            'produto'      => $this->produtoVazio(),
            'erros'        => [],
            'acao'         => url('produtos/criar'),
            'rotuloBotao'  => 'Cadastrar produto',
            'tituloForm'   => 'Cadastrar novo produto',
            'subtitulo'    => 'Preencha os dados abaixo para adicionar um item ao estoque.',
        ]);
    }

    /**
     * POST /produtos/criar
     * Valida e grava um novo produto.
     */
    public function salvar(): void
    {
        $produto = $this->produtoVazio();
        $erros   = [];

        [$produto, $erros] = $this->validar($_POST, $produto, $erros);

        // Upload da imagem
        $upload = $this->imagemService->salvar($_FILES['imagem'] ?? []);
        if ($upload['erro'] !== null) {
            $erros[] = $upload['erro'];
        }

        if (empty($erros)) {
            $produto['imagem'] = $upload['arquivo'];
            $this->produtoModel->criar($produto);

            $this->redirect('', ['mensagem' => 'Produto cadastrado com sucesso!']);
        }

        // Em caso de erro, devolve os dados digitados ao formulário
        $this->render('produtos/form', [
            'tituloPagina' => 'Novo produto',
            'produto'      => $produto,
            'erros'        => $erros,
            'acao'         => url('produtos/criar'),
            'rotuloBotao'  => 'Cadastrar produto',
            'tituloForm'   => 'Cadastrar novo produto',
            'subtitulo'    => 'Preencha os dados abaixo para adicionar um item ao estoque.',
        ]);
    }

    /**
     * GET /produtos/editar/{id}
     * Exibe o formulário de edição.
     */
    public function editar(string $id): void
    {
        $produto = $this->produtoModel->buscar((int) $id, $this->usuarioId);

        if ($produto === null) {
            $this->redirect('', ['mensagem' => 'Produto não encontrado.']);
        }

        $this->render('produtos/form', [
            'tituloPagina' => 'Editar produto',
            'produto'      => $produto,
            'erros'        => [],
            'acao'         => url('produtos/editar/' . $produto['id']),
            'rotuloBotao'  => 'Salvar alterações',
            'tituloForm'   => 'Editar produto',
            'subtitulo'    => 'Atualize as informações de ' . $produto['nome'] . '.',
        ]);
    }

    /**
     * POST /produtos/editar/{id}
     * Valida e atualiza um produto existente.
     */
    public function atualizar(string $id): void
    {
        $produto = $this->produtoModel->buscar((int) $id, $this->usuarioId);

        if ($produto === null) {
            $this->redirect('', ['mensagem' => 'Produto não encontrado.']);
        }

        $erros = [];
        [$produto, $erros] = $this->validar($_POST, $produto, $erros);

        $nomeImagem = $produto['imagem'];

        // Remoção explícita da imagem atual
        if (!empty($_POST['remover_imagem'])) {
            $this->imagemService->remover($nomeImagem);
            $nomeImagem = null;
        }

        // Envio de nova imagem substitui a anterior
        $upload = $this->imagemService->salvar($_FILES['imagem'] ?? []);
        if ($upload['erro'] !== null) {
            $erros[] = $upload['erro'];
        } elseif ($upload['arquivo'] !== null) {
            $this->imagemService->remover($nomeImagem);
            $nomeImagem = $upload['arquivo'];
        }

        if (empty($erros)) {
            $produto['imagem'] = $nomeImagem;
            $this->produtoModel->atualizar((int) $produto['id'], $this->usuarioId, $produto);

            $this->redirect('', ['mensagem' => 'Produto atualizado com sucesso!']);
        }

        $produto['imagem'] = $nomeImagem;

        $this->render('produtos/form', [
            'tituloPagina' => 'Editar produto',
            'produto'      => $produto,
            'erros'        => $erros,
            'acao'         => url('produtos/editar/' . $produto['id']),
            'rotuloBotao'  => 'Salvar alterações',
            'tituloForm'   => 'Editar produto',
            'subtitulo'    => 'Atualize as informações de ' . $produto['nome'] . '.',
        ]);
    }

    /**
     * GET /produtos/excluir/{id}
     * Remove um produto e sua imagem.
     */
    public function excluir(string $id): void
    {
        $produto = $this->produtoModel->buscar((int) $id, $this->usuarioId);

        if ($produto === null) {
            $this->redirect('', ['mensagem' => 'Produto não encontrado.']);
        }

        $this->imagemService->remover($produto['imagem']);
        $this->produtoModel->excluir((int) $produto['id'], $this->usuarioId);

        $this->redirect('', ['mensagem' => 'Produto excluído com sucesso!']);
    }

    // -----------------------------------------------------------------------
    // Métodos auxiliares
    // -----------------------------------------------------------------------

    /**
     * Estrutura vazia de produto usada no formulário.
     *
     * @return array<string, mixed>
     */
    private function produtoVazio(): array
    {
        return [
            'id'            => null,
            'usuario_id'    => $this->usuarioId,
            'nome'          => '',
            'preco'         => '',
            'perecivel'     => 0,
            'data_validade' => '',
            'imagem'        => null,
        ];
    }

    /**
     * Valida e normaliza os dados recebidos do formulário.
     *
     * @param array<string, mixed> $entrada Dados de $_POST.
     * @param array<string, mixed> $produto Produto base (para edição).
     * @param array<int, string>   $erros   Erros já acumulados.
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    private function validar(array $entrada, array $produto, array $erros): array
    {
        $produto['nome']          = trim($entrada['nome'] ?? '');
        $produto['preco']         = str_replace(',', '.', trim($entrada['preco'] ?? ''));
        $produto['perecivel']     = isset($entrada['perecivel']) ? 1 : 0;
        $produto['data_validade'] = trim($entrada['data_validade'] ?? '');

        if ($produto['nome'] === '') {
            $erros[] = 'O nome do produto é obrigatório.';
        }

        if ($produto['preco'] === '' || !is_numeric($produto['preco']) || (float) $produto['preco'] < 0) {
            $erros[] = 'Informe um preço válido (maior ou igual a zero).';
        } else {
            $produto['preco'] = (float) $produto['preco'];
        }

        if ($produto['perecivel'] === 1) {
            if ($produto['data_validade'] === '') {
                $erros[] = 'A data de validade é obrigatória para produtos perecíveis.';
            } elseif (!strtotime($produto['data_validade'])) {
                $erros[] = 'Informe uma data de validade válida.';
            }
        } else {
            $produto['data_validade'] = null;
        }

        return [$produto, $erros];
    }
}
