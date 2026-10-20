/**
 * Controle do campo de validade no formulário de produto.
 * O campo só é exibido/obrigatório quando o produto é marcado como perecível.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var checkbox = document.getElementById('perecivel');
        var campoValidade = document.getElementById('campo-validade');
        var inputValidade = document.getElementById('data_validade');

        if (!checkbox || !campoValidade || !inputValidade) {
            return;
        }

        function atualizar() {
            if (checkbox.checked) {
                campoValidade.hidden = false;
                inputValidade.required = true;
            } else {
                campoValidade.hidden = true;
                inputValidade.required = false;
                inputValidade.value = '';
            }
        }

        checkbox.addEventListener('change', atualizar);
        atualizar();
    });
})();
