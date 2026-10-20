<?php
/** @var array  $produto */
/** @var array  $erros */
/** @var string $acao */
/** @var string $rotuloBotao */
/** @var string $tituloForm */
/** @var string $subtitulo */

$perecivelMarcado = (int) ($produto['perecivel'] ?? 0) === 1;
?>

<div class="cabecalho-pagina">
    <h1><?= e($tituloForm) ?></h1>
    <p class="texto-suave"><?= e($subtitulo) ?></p>
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

<form class="formulario" method="post" action="<?= e($acao) ?>" enctype="multipart/form-data" novalidate>
    <div class="formulario__linha">
        <div class="campo campo--largo">
            <label for="nome">Nome do produto <span class="obrigatorio">*</span></label>
            <input type="text" id="nome" name="nome" maxlength="150" required
                   value="<?= e($produto['nome'] ?? '') ?>"
                   placeholder="Ex.: Arroz Tipo 1 - 5kg">
        </div>
    </div>

    <div class="formulario__linha">
        <div class="campo">
            <label for="preco">Preço (R$) <span class="obrigatorio">*</span></label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required
                   value="<?= e($produto['preco'] ?? '') ?>"
                   placeholder="0,00">
        </div>

        <div class="campo">
            <label for="imagem">Imagem (PNG ou JPG)</label>
            <input type="file" id="imagem" name="imagem" accept="image/png,image/jpeg">
            <small class="ajuda">Tamanho máximo de 2 MB.</small>

            <?php if (!empty($produto['imagem'])): ?>
                <div class="imagem-atual">
                    <img src="<?= asset(UPLOAD_URL . $produto['imagem']) ?>" alt="Imagem atual do produto">
                    <label class="remover-imagem">
                        <input type="checkbox" name="remover_imagem" value="1">
                        Remover imagem atual
                    </label>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="formulario__linha">
        <div class="campo">
            <label class="rotulo-checkbox">
                <input type="checkbox" id="perecivel" name="perecivel" value="1"
                       <?= $perecivelMarcado ? 'checked' : '' ?>>
                <span>Este produto é perecível</span>
            </label>
            <small class="ajuda">Marque para habilitar o campo de data de validade.</small>
        </div>

        <div class="campo" id="campo-validade" <?= $perecivelMarcado ? '' : 'hidden' ?>>
            <label for="data_validade">Data de validade <span class="obrigatorio">*</span></label>
            <input type="date" id="data_validade" name="data_validade"
                   value="<?= e($produto['data_validade'] ?? '') ?>">
        </div>
    </div>

    <div class="formulario__acoes">
        <button type="submit" class="botao botao--primario"><?= e($rotuloBotao) ?></button>
        <a href="<?= url('') ?>" class="botao botao--texto">Cancelar</a>
    </div>
</form>

<script src="<?= asset('assets/js/app.js') ?>"></script>
