<?php
/** @var array  $usuario */
/** @var array  $erros */
/** @var string $acao */
/** @var string $rotuloBotao */
?>

<div class="cabecalho-pagina">
    <h1>Novo usuário</h1>
    <p class="texto-suave">Cadastre um novo usuário para acessar o sistema.</p>
</div>

<?php if (!empty($erros)): ?>
    <div class="alerta alerta--erro">
        <strong>Corrija os itens abaixo:</strong>
        <ul>
            <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulario" method="post" action="<?= e($acao) ?>" novalidate>
    <div class="formulario__linha">
        <div class="campo campo--largo">
            <label for="nome">Nome completo <span class="obrigatorio">*</span></label>
            <input type="text" id="nome" name="nome" maxlength="100" required
                   value="<?= e($usuario['nome'] ?? '') ?>"
                   placeholder="Ex.: Maria Silva">
        </div>
    </div>

    <div class="formulario__linha">
        <div class="campo campo--largo">
            <label for="login">Usuário (login) <span class="obrigatorio">*</span></label>
            <input type="text" id="login" name="login" maxlength="50" required
                   autocomplete="off"
                   value="<?= e($usuario['login'] ?? '') ?>"
                   placeholder="Ex.: maria.silva">
            <small class="ajuda">Mínimo de 3 caracteres. Deve ser único no sistema.</small>
        </div>
    </div>

    <div class="formulario__linha">
        <div class="campo">
            <label for="senha">Senha <span class="obrigatorio">*</span></label>
            <input type="password" id="senha" name="senha" required
                   autocomplete="new-password"
                   placeholder="Mínimo de 3 caracteres">
        </div>

        <div class="campo">
            <label for="confirmacao">Confirmar senha <span class="obrigatorio">*</span></label>
            <input type="password" id="confirmacao" name="confirmacao" required
                   autocomplete="new-password"
                   placeholder="Repita a senha">
        </div>
    </div>

    <div class="formulario__acoes">
        <button type="submit" class="botao botao--primario"><?= e($rotuloBotao) ?></button>
        <a href="<?= url('usuarios') ?>" class="botao botao--texto">Cancelar</a>
    </div>
</form>
