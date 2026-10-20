<?php
/** @var array $usuario */
/** @var array $erros */
?>

<div class="login">
    <div class="login__cartao">
        <div class="login__cabecalho">
            <span class="login__icone">📝</span>
            <h1>Criar conta</h1>
            <p class="texto-suave">Preencha os dados abaixo para acessar o sistema.</p>
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

        <form class="formulario formulario--login" method="post" action="<?= url('registrar') ?>" novalidate>
            <div class="campo">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" maxlength="100" required autofocus
                       value="<?= e($usuario['nome'] ?? '') ?>"
                       placeholder="Ex.: Maria Silva">
            </div>

            <div class="campo">
                <label for="login">Usuário (login)</label>
                <input type="text" id="login" name="login" maxlength="50" required
                       autocomplete="off"
                       value="<?= e($usuario['login'] ?? '') ?>"
                       placeholder="Mínimo de 3 caracteres">
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required
                       autocomplete="new-password"
                       placeholder="Mínimo de 3 caracteres">
            </div>

            <div class="campo">
                <label for="confirmacao">Confirmar senha</label>
                <input type="password" id="confirmacao" name="confirmacao" required
                       autocomplete="new-password"
                       placeholder="Repita a senha">
            </div>

            <button type="submit" class="botao botao--primario botao--bloco">Cadastrar</button>
        </form>

        <p class="login__rodape">
            Já tem uma conta? <a href="<?= url('login') ?>">Entrar</a>
        </p>
    </div>
</div>
