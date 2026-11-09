/*=================================================
FORMULÁRIO DE LEAD DA PÁGINA DE CURSO
=================================================*/
(function () {
    var form = document.getElementById('formLeadCurso');
    if (!form) { return; }

    var modo = form.getAttribute('data-modo');
    var msg = document.getElementById('leadCursoMensagem');
    var botao = form.querySelector('button[type="submit"]');
    var celular = document.getElementById('lead-celular');
    var cursoId = form.querySelector('[name="curso_id"]').value;
    var chave = 'leadCurso_' + cursoId;
    var btnInscricao = document.getElementById('btnInscricao');
    var areaMaterial = document.getElementById('leadMaterial');

    function mostrarMensagem(texto, tipo) {
        msg.textContent = texto;
        msg.className = 'small mb-3 text-' + tipo;
        msg.hidden = false;
    }

    // Máscara de celular: (11) 91234-5678
    celular.addEventListener('input', function () {
        var d = celular.value.replace(/\D/g, '').slice(0, 11);
        if (d.length > 10) {
            celular.value = d.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        } else if (d.length > 6) {
            celular.value = d.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (d.length > 2) {
            celular.value = d.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
        } else {
            celular.value = d;
        }
    });

    function liberar(dados) {
        if (modo === 'inscricao' && btnInscricao) {
            btnInscricao.classList.remove('disabled');
            btnInscricao.removeAttribute('aria-disabled');
            btnInscricao.removeAttribute('tabindex');
        }
        if (dados && dados.material && areaMaterial) {
            var link = areaMaterial.querySelector('a');
            link.href = dados.material;
            areaMaterial.hidden = false;
        }
    }

    function concluir(texto, dados) {
        form.querySelectorAll('input, button').forEach(function (el) { el.disabled = true; });
        form.querySelectorAll('.cf-turnstile').forEach(function (el) { el.hidden = true; });
        botao.textContent = 'Enviado';
        mostrarMensagem(texto, 'success');
        liberar(dados);
    }

    // Visitante que já preencheu: libera sem pedir de novo
    try {
        var salvo = JSON.parse(localStorage.getItem(chave));
        if (salvo && salvo.texto) { concluir(salvo.texto, salvo.dados); }
    } catch (e) { /* localStorage indisponível */ }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.hidden = true;

        botao.disabled = true;
        var textoOriginal = botao.textContent;
        botao.textContent = 'Enviando...';

        fetch(leadCurso.ajaxUrl, { method: 'POST', body: new FormData(form) })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success) {
                    try { localStorage.setItem(chave, JSON.stringify({ texto: res.message, dados: { material: res.material } })); } catch (e) { }
                    concluir(res.message, res);
                    return;
                }
                mostrarMensagem(res.message || 'Não foi possível enviar.', 'danger');
                botao.disabled = false;
                botao.textContent = textoOriginal;
                if (window.turnstile) { window.turnstile.reset(); }
            })
            .catch(function () {
                mostrarMensagem('Falha de conexão. Tente novamente.', 'danger');
                botao.disabled = false;
                botao.textContent = textoOriginal;
                if (window.turnstile) { window.turnstile.reset(); }
            });
    });
})();
