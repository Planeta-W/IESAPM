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
    var avisoMaterial = document.getElementById('leadMaterialAviso');
    var textoBotao = botao.textContent;
    var cadastrado = false; // dados já enviados (nesta visita ou em visita anterior)
    var enviando = false;

    function mostrarMensagem(texto, tipo, aviso) {
        msg.textContent = texto;
        if (aviso) { msg.setAttribute('data-aviso', '1'); } else { msg.removeAttribute('data-aviso'); }
        msg.className = 'small mb-3 text-' + tipo;
        msg.hidden = false;
    }

    function valor(nome) {
        return (form.elements[nome].value || '').trim();
    }

    // Primeira pendência do formulário (nome, e-mail, celular, profissão, LGPD e Turnstile)
    function primeiraPendencia() {
        var token = form.elements['cf-turnstile-response'];
        if (valor('nome') === '') { return { campo: 'nome', texto: 'Preencha o seu nome para se inscrever.' }; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valor('email'))) { return { campo: 'email', texto: 'Informe um e-mail válido para se inscrever.' }; }
        if (celular.value.replace(/\D/g, '').length < 10) { return { campo: 'celular', texto: 'Informe o seu celular com DDD para se inscrever.' }; }
        if (valor('profissao') === '') { return { campo: 'profissao', texto: 'Informe a sua profissão para se inscrever.' }; }
        if (!form.elements['consentimento'].checked) { return { campo: 'consentimento', texto: 'Aceite a Política de Privacidade para se inscrever.' }; }
        if (!(token && token.value)) { return { campo: null, texto: 'Confirme a verificação de segurança para se inscrever.' }; }
        return null;
    }

    // Botão "apagado" enquanto o formulário está incompleto, mas clicável: o clique mostra o que falta
    function atualizarBotao() {
        if (!inscricao || cadastrado || enviando) { return; }
        var valido = primeiraPendencia() === null;
        botao.classList.toggle('opacity-50', !valido);
        botao.setAttribute('aria-disabled', valido ? 'false' : 'true');
        if (valido && msg.getAttribute('data-aviso')) { msg.hidden = true; }
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
        botao.classList.remove('opacity-50');
        botao.setAttribute('aria-disabled', 'false');
        botao.textContent = inscricao ? textoBotao : 'Enviado';
        mostrarMensagem(texto, 'success');
        mostrarMaterial(material);
        if (avisoMaterial) { avisoMaterial.hidden = true; }
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
        botao.disabled = false;
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
        if (inscricao) {
            var pendencia = primeiraPendencia();
            if (pendencia) {
                mostrarMensagem(pendencia.texto, 'danger', true);
                if (pendencia.campo) { form.elements[pendencia.campo].focus(); }
                return;
            }
        }

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
