<?php
/** @var array $produtos */
/** @var array $resumo */
/** @var string $filtro */
/** @var string $busca */
/** @var string $ordem */
/** @var array $linksFiltro */
/** @var string $linkLimpar */
/** @var array $rotulos */
/** @var string $mensagem */
?>

<?php if (!empty($mensagem)): ?>
    <div class="alerta alerta--sucesso"><?= e($mensagem) ?></div>
<?php endif; ?>

<section class="painel-estatisticas">
    <article class="cartao-estatistica">
        <span class="cartao-estatistica__rotulo">Total de produtos</span>
        <strong class="cartao-estatistica__valor"><?= $resumo['total'] ?></strong>
    </article>
    <article class="cartao-estatistica">
        <span class="cartao-estatistica__rotulo">Dentro da validade</span>
        <strong class="cartao-estatistica__valor"><?= $resumo['dentro'] ?></strong>
    </article>
    <article class="cartao-estatistica cartao-estatistica--alerta">
        <span class="cartao-estatistica__rotulo">Fora da validade</span>
        <strong class="cartao-estatistica__valor"><?= $resumo['fora'] ?></strong>
    </article>
    <article class="cartao-estatistica">
        <span class="cartao-estatistica__rotulo">Valor em estoque</span>
        <strong class="cartao-estatistica__valor"><?= formatarPreco($resumo['valor_total']) ?></strong>
    </article>
</section>

<!-- Botões de filtro por situação -->
<nav class="filtros" aria-label="Filtrar produtos por situação">
    <?php
    $contadores = [
        'tudo'           => $resumo['total'],
        'dentro'         => $resumo['dentro'],
        'fora'           => $resumo['fora'],
        'nao_pereciveis' => $resumo['nao_pereciveis'],
    ];
    ?>
    <?php foreach ($rotulos as $chave => $rotulo): ?>
        <a href="<?= e($linksFiltro[$chave]) ?>"
           class="filtro <?= $filtro === $chave ? 'filtro--ativo' : '' ?>">
            <?= e($rotulo) ?> <span class="filtro__contador"><?= $contadores[$chave] ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<section class="barra-ferramentas">
    <form class="form-busca" method="get" action="<?= url('') ?>">
        <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
        <input
            type="text"
            name="busca"
            placeholder="Buscar produto pelo nome..."
            value="<?= e($busca) ?>"
        >
        <select name="ordem" onchange="this.form.submit()">
            <option value="nome"     <?= $ordem === 'nome' ? 'selected' : '' ?>>Ordenar por nome</option>
            <option value="preco"    <?= $ordem === 'preco' ? 'selected' : '' ?>>Ordenar por preço</option>
            <option value="validade" <?= $ordem === 'validade' ? 'selected' : '' ?>>Ordenar por validade</option>
        </select>
        <button type="submit" class="botao botao--primario">Buscar</button>
        <?php if ($busca !== ''): ?>
            <a class="botao botao--texto" href="<?= e($linkLimpar) ?>">Limpar</a>
        <?php endif; ?>
    </form>
</section>

<?php if (empty($produtos)): ?>
    <div class="estado-vazio">
        <p class="estado-vazio__icone">📭</p>
        <h2>Nenhum produto encontrado</h2>
        <p>
            <?php if ($busca !== ''): ?>
                Não encontramos produtos com esse nome no filtro “<?= e($rotulos[$filtro]) ?>”.
            <?php elseif ($filtro !== 'tudo'): ?>
                Não há produtos na categoria “<?= e($rotulos[$filtro]) ?>” no momento.
            <?php else: ?>
                Comece cadastrando o primeiro produto do seu estoque.
            <?php endif; ?>
        </p>
        <a href="<?= url('produtos/criar') ?>" class="botao botao--primario">Cadastrar produto</a>
    </div>
<?php else: ?>
    <!-- Tabela (desktop) -->
    <div class="tabela-responsiva">
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Tipo</th>
                    <th>Validade</th>
                    <th class="coluna-acoes">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <?php
                        $perecivel = (int) $produto['perecivel'] === 1;
                        $vencido   = $perecivel && validadeVencida($produto['data_validade']);
                    ?>
                    <tr>
                        <td data-rotulo="Imagem">
                            <?php if (!empty($produto['imagem'])): ?>
                                <img class="miniatura"
                                     src="<?= asset(UPLOAD_URL . $produto['imagem']) ?>"
                                     alt="Imagem de <?= e($produto['nome']) ?>">
                            <?php else: ?>
                                <div class="miniatura miniatura--vazia">sem foto</div>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Nome" class="celula-nome"><?= e($produto['nome']) ?></td>
                        <td data-rotulo="Preço"><?= formatarPreco($produto['preco']) ?></td>
                        <td data-rotulo="Tipo">
                            <?php if ($perecivel): ?>
                                <span class="etiqueta etiqueta--perecivel">Perecível</span>
                            <?php else: ?>
                                <span class="etiqueta etiqueta--nao-perecivel">Não perecível</span>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Validade">
                            <?php if ($perecivel): ?>
                                <span class="<?= $vencido ? 'texto-vencido' : '' ?>">
                                    <?= formatarData($produto['data_validade']) ?>
                                    <?php if ($vencido): ?> (vencido)<?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="texto-suave">Não se aplica</span>
                            <?php endif; ?>
                        </td>
                        <td data-rotulo="Ações" class="coluna-acoes">
                            <div class="acoes">
                                <a href="<?= url('produtos/editar/' . $produto['id']) ?>"
                                   class="botao botao--secundario botao--pequeno">Editar</a>
                                <a href="<?= url('produtos/excluir/' . $produto['id']) ?>"
                                   class="botao botao--perigo botao--pequeno"
                                   onclick="return confirm('Deseja realmente excluir o produto &quot;<?= e($produto['nome']) ?>&quot;?');">
                                    Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
