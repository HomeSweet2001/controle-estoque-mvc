<?php
$tituloPagina = 'Página não encontrada';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?> · Controle de Estoque</title>
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<header class="topo">
    <div class="container topo__conteudo">
        <a class="topo__logo" href="<?= url('') ?>">
            <span class="topo__icone">📦</span>
            <span>Controle de Estoque</span>
        </a>
    </div>
</header>
<main class="container conteudo">
    <div class="estado-vazio">
        <p class="estado-vazio__icone">🔍</p>
        <h2>Página não encontrada</h2>
        <p>O endereço que você tentou acessar não existe neste sistema.</p>
        <a href="<?= url('') ?>" class="botao botao--primario">Voltar para os produtos</a>
    </div>
</main>
<footer class="rodape">
    <div class="container">
        <p>Controle de Estoque &middot; Arquitetura MVC &middot; PHP + MySQL (vanilla)</p>
    </div>
</footer>
</body>
</html>
