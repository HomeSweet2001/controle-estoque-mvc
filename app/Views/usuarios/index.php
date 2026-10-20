<?php
/** @var array $usuarios */
/** @var array|null $usuarioAtual */
/** @var string $mensagem */
/** @var string $erro */
?>

<?php if (!empty($mensagem)): ?>
    <div class="alerta alerta--sucesso"><?= e($mensagem) ?></div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="alerta alerta--erro"><?= e($erro) ?></div>
<?php endif; ?>

<div class="cabecalho-pagina cabecalho-pagina--flex">
    <div>
        <h1>Usuários</h1>
        <p class="texto-suave">Gerencie quem pode acessar o sistema.</p>
    </div>
    <a href="<?= url('usuarios/criar') ?>" class="botao botao--primario">+ Novo usuário</a>
</div>

<div class="tabela-responsiva">
    <table class="tabela-produtos">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Criado em</th>
                <th class="coluna-acoes">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <?php $ehUsuarioAtual = $usuarioAtual !== null && (int) $usuarioAtual['id'] === (int) $u['id']; ?>
                <tr>
                    <td data-rotulo="Nome" class="celula-nome">
                        <?= e($u['nome']) ?>
                        <?php if ($ehUsuarioAtual): ?>
                            <span class="etiqueta etiqueta--nao-perecivel">você</span>
                        <?php endif; ?>
                    </td>
                    <td data-rotulo="Usuário"><?= e($u['login']) ?></td>
                    <td data-rotulo="Criado em"><?= formatarData($u['criado_em']) ?></td>
                    <td data-rotulo="Ações" class="coluna-acoes">
                        <div class="acoes">
                            <?php if ($ehUsuarioAtual): ?>
                                <span class="texto-suave">—</span>
                            <?php else: ?>
                                <a href="<?= url('usuarios/excluir/' . $u['id']) ?>"
                                   class="botao botao--perigo botao--pequeno"
                                   onclick="return confirm('Deseja realmente excluir o usuário &quot;<?= e($u['nome']) ?>&quot;?');">
                                    Excluir
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
