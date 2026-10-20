<?php
/** @var string $login */
/** @var array  $erros */
/** @var string $mensagem */
?>

<div class="login">
    <div class="login__cartao">
        <div class="login__cabecalho">
            <span class="login__icone">🔐</span>
            <h1>Acessar o sistema</h1>
            <p class="texto-suave">Entre com suas credenciais para gerenciar o estoque.</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta alerta--sucesso"><?= e($mensagem) ?></div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="alerta alerta--erro">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="formulario formulario--login" method="post" action="<?= url('login') ?>" novalidate>
            <div class="campo">
                <label for="login">Usuário</label>
                <input type="text" id="login" name="login" required autofocus
                       autocomplete="username"
                       value="<?= e($login) ?>"
                       placeholder="Digite seu usuário">
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required
                       autocomplete="current-password"
                       placeholder="Digite sua senha">
            </div>

            <button type="submit" class="botao botao--primario botao--bloco">Entrar</button>
        </form>

        <p class="login__rodape">
            Não tem uma conta? <a href="<?= url('registrar') ?>">Cadastre-se</a>
        </p>
    </div>
</div>
