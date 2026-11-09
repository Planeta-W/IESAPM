/*=================================================
FORMULÁRIO DE LEAD DA PÁGINA DE CURSO

modo "espera":    envia o formulário e confirma o cadastro.
modo "inscricao": o botão "Inscreva-se" só ativa com o formulário válido;
                  ao clicar, envia os dados e leva o visitante ao link de inscrição.
=================================================*/
(function () {
    var form = document.getElementById('formLeadCurso');
    if (!form) { return; }

    var modo = form.getAttribute('data-modo');
    var urlInscricao = form.getAttribute('data-url');
    var abrirEmNovaAba = form.getAttribute('data-target') === '_blank';
    var inscricao = modo === 'inscricao';

    var msg = document.getElementById('leadCursoMensagem');
    var botao = form.querySelector('button[type="submit"]');
    var celular = document.getElementById('lead-celular');
    var cursoId = form.querySelector('[name="curso_id"]').value;
    var chave = 'leadCurso_' + cursoId;
    var areaMaterial = document.getElementById('leadMaterial');
    var textoBotao = botao.textContent;
    var cadastrado = false; // dados já enviados (nesta visita ou em visita anterior)
    var enviando = false;

    function mostrarMensagem(texto, tipo) {
        msg.textContent = texto;
        msg.className = 'small mb-3 text-' + tipo;
        msg.hidden = false;
    }

    function valor(nome) {
        return (form.elements[nome].value || '').trim();
    }

    // Formulário completo e válido (nome, e-mail, celular, profissão, LGPD e Turnstile)
    function formularioValido() {
        var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valor('email'));
        var celularOk = celular.value.replace(/\D/g, '').length >= 10;
        var token = form.elements['cf-turnstile-response'];
        return valor('nome') !== '' && emailOk && celularOk && valor('profissao') !== '' &&
            form.elements['consentimento'].checked && !!(token && token.value);
    }

    function atualizarBotao() {
        if (!inscricao || cadastrado || enviando) { return; }
        botao.disabled = !formularioValido();
    }

    // Callbacks do Turnstile (declarados no data-callback do widget)
    window.leadTurnstileOk = atualizarBotao;
    window.leadTurnstileExpirou = function () { setTimeout(atualizarBotao, 0); };

    form.addEventListener('input', atualizarBotao);
    form.addEventListener('change', atualizarBotao);

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

    function mostrarMaterial(url) {
        if (url && areaMaterial) {
            areaMaterial.querySelector('a').href = url;
            areaMaterial.hidden = false;
        }
    }

    // Trava os campos após o envio; no modo inscrição o botão continua ativo e leva à inscrição
    function concluir(texto, material) {
        cadastrado = true;
        form.querySelectorAll('input').forEach(function (el) { el.disabled = true; });
        form.querySelectorAll('.cf-turnstile').forEach(function (el) { el.hidden = true; });
        botao.disabled = !inscricao;
        botao.textContent = inscricao ? textoBotao : 'Enviado';
        mostrarMensagem(texto, 'success');
        mostrarMaterial(material);
    }

    // Visitante que já preencheu: não pede de novo
    try {
        var salvo = JSON.parse(localStorage.getItem(chave));
        if (salvo && salvo.texto) {
            concluir(inscricao ? 'Seus dados já foram enviados. Clique em Inscreva-se para continuar.' : salvo.texto, salvo.material);
        }
    } catch (e) { /* localStorage indisponível */ }

    function falha(texto, janela) {
        if (janela) { janela.close(); }
        enviando = false;
        mostrarMensagem(texto, 'danger');
        botao.textContent = textoBotao;
        if (window.turnstile) { window.turnstile.reset(); }
        atualizarBotao();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Já cadastrado: vai direto à inscrição, sem reenviar os dados
        if (inscricao && cadastrado) {
            if (abrirEmNovaAba) { window.open(urlInscricao, '_blank', 'noopener'); }
            else { window.location.href = urlInscricao; }
            return;
        }

        if (enviando) { return; }
        if (inscricao && !formularioValido()) { return; }

        msg.hidden = true;
        enviando = true;
        botao.disabled = true;
        botao.textContent = 'Enviando...';

        // Em nova aba, a janela precisa abrir agora (dentro do clique) para não ser bloqueada
        var janela = null;
        if (inscricao && abrirEmNovaAba) {
            janela = window.open('', '_blank');
        }

        fetch(leadCurso.ajaxUrl, { method: 'POST', body: new FormData(form) })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) {
                    falha(res.message || 'Não foi possível enviar.', janela);
                    return;
                }

                try { localStorage.setItem(chave, JSON.stringify({ texto: res.message, material: res.material })); } catch (e) { }
                enviando = false;
                concluir(res.message, res.material);

                if (!inscricao) { return; }

                if (abrirEmNovaAba && janela) {
                    janela.opener = null;
                    janela.location.href = urlInscricao;
                } else if (abrirEmNovaAba) {
                    // pop-up bloqueado: o visitante segue pelo botão
                    mostrarMensagem('Dados enviados! Clique em Inscreva-se para abrir a página de inscrição.', 'success');
                } else {
                    window.location.href = urlInscricao;
                }
            })
            .catch(function () {
                falha('Falha de conexão. Tente novamente.', janela);
            });
    });
})();
