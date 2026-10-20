<?php
/** @var string $tituloPagina */
$tituloPagina = $tituloPagina ?? 'Controle de Estoque';
$usuarioLogado = usuario_logado();
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

        <?php if ($usuarioLogado !== null): ?>
            <nav class="topo__menu">
                <a href="<?= url('produtos/criar') ?>" class="botao botao--primario botao--pequeno">+ Novo produto</a>
                <span class="topo__usuario">
                    <span class="topo__usuario-nome" title="<?= e($usuarioLogado['login']) ?>">
                        👤 <?= e($usuarioLogado['nome']) ?>
                    </span>
                    <a href="<?= url('logout') ?>" class="botao botao--texto botao--pequeno">Sair</a>
                </span>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container conteudo">
