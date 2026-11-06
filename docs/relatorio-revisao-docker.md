# Base Docker WordPress: revisão e próximos passos

27/10/2026

## Resumo

A base Docker para WordPress foi revisada, corrigida e testada de ponta a ponta: está pronta para uso nos projetos. Foram 7 ajustes principais e 8 melhorias menores, todos validados num ambiente isolado com MySQL temporário.

A base serve para desenvolver só o tema de um site WordPress, com banco MySQL externo. A estrutura já era boa (porta presa em `127.0.0.1`, `.env` fora do webroot, sinais repassados ao Apache). Os problemas encontrados eram de permissão, leitura do `.env`, persistência de dados e versões não fixadas.

## Correções de problemas

Cinco problemas podiam causar erro ou comportamento inesperado; todos foram corrigidos.

| # | Problema | Efeito | Correção |
| --- | --- | --- | --- |
| 1 | Plugins instalados pelo WP-CLI ficavam com dono `root` | O wp-admin não conseguia atualizar nem remover plugins e pedia credenciais de FTP | O entrypoint passa `wp-content/plugins` para `www-data` após a instalação |
| 2 | `$` em senhas e salts do `.env` | O Docker Compose trocava `$algo` por uma variável vazia; senha do banco alterada sem aviso | Senhas e salts entre aspas simples; aviso no `.env.example` e no README |
| 3 | Healthcheck e espera usavam `/wp-json` | Com permalinks "Simples" dá 404: container sempre unhealthy e entrypoint preso | Troca para `/?rest_route=/`, que funciona com qualquer permalink |
| 4 | Redirect de uploads com 301 e sem checagem | O navegador guardava o redirect em cache; com URL vazia gerava redirect quebrado | Redirect 302, criado só quando `PRODUCTION_UPLOADS_URL` está preenchido |
| 5 | `.env` lido duas vezes (Compose e phpdotenv) | Leitura duplicada, Composer rodando a cada subida e `vendor/` exposto no webroot | `wp-config.php` lê com `getenv()`; Composer, phpdotenv e `server/composer.json` removidos |

## Mudanças estruturais

O WordPress agora persiste entre recriações do container, e as versões ficaram fixas em WordPress 7.1.3 com PHP 8.5.

**Volume nomeado `wp_data`.** Antes, a pasta do WordPress ficava num volume anônimo. A cada `docker compose down` e `up`, o core era copiado de novo, todos os plugins eram baixados de novo, uploads locais e `debug.log` sumiam e sobrava um volume órfão no disco. Agora tudo isso fica guardado; só `docker compose down -v` apaga.

**Versões fixas.** O Dockerfile usa `WP_VERSION=7.1.3` e `PHP_VERSION=8.5` (antes, cada rebuild pegava o WordPress mais recente). Como o core fica no volume, trocar de versão exige `down -v` antes do rebuild.

**Atualizações automáticas desligadas.** O `wp-config.php` define `AUTOMATIC_UPDATER_DISABLED`. Sem isso, o WordPress poderia se atualizar sozinho no volume, desfazendo a versão fixada e rodando a atualização no banco de produção. Atualizações manuais pelo wp-admin continuam funcionando.

## Melhorias menores

| Arquivo | Mudança |
| --- | --- |
| `Dockerfile` | WP-CLI copiado da imagem oficial `wordpress:cli-2.12.0` (versão em `WP_CLI_VERSION`), sem download via curl |
| `Dockerfile` | Removidos `apt-get` (curl) e `a2enmod rewrite`, que a imagem base já traz |
| `.dockerignore` (novo) | `.env`, `themes/`, `node_modules` e `.git` fora do contexto de build |
| `docker-compose.yml` | `php.ini` montado em `conf.d/zz-custom.ini`, o local padrão da imagem |
| `docker-compose.yml` | `extra_hosts` para `host.docker.internal` funcionar também no Docker do Linux |
| `server/php.ini` | Além do upload de 64M: `memory_limit` 256M e `max_execution_time` 120s |
| `server/entrypoint.sh` | Pula plugins de arquivo único (ex.: `hello.php`) e não baixa de novo plugins que já estão no volume |
| `.gitattributes` | Linha duplicada removida |
| `.github/workflows/deploy.yml` | `protocol: ftps` deixado comentado, pronto para ativar |

## Testes realizados

Tudo foi testado num ambiente isolado (projeto `wptest`, porta 8089) com MySQL 8.4 temporário e senha contendo `$`; o ambiente foi apagado no final.

| Verificação | Resultado |
| --- | --- |
| Build da imagem | OK: WordPress 7.1.3, PHP 8.5.12, WP-CLI 2.12.0 |
| curl e mod_rewrite presentes na imagem base | OK |
| Senha `te$t123` e salt `a$b$c` com aspas simples | OK: chegaram intactos e o banco conectou |
| `wp-config.php` com `getenv()` | OK, sem `vendor/` no webroot |
| Espera pelo WordPress enquanto o MySQL inicia | OK: seguiu sozinho quando o banco ficou pronto |
| Healthcheck | OK: `healthy` |
| `/wp-json` com permalink padrão | OK: 200 |
| Upload ausente localmente | OK: 302 para a URL de produção |
| `down` e `up` sem `-v` | OK: core e plugins mantidos no volume |
| Sincronização de plugins | OK: pulou o existente, ignorou `hello.php`, instalou o que faltava |
| Dono dos plugins | OK: todos `www-data` |
| `php.ini` (via web) | OK: 64M, 256M, 120s |
| Atualizações automáticas | OK: desligadas |
| `host.docker.internal` | OK: resolve no container |

Ponto em aberto: o WP-CLI 2.12.0 mostra um aviso `Deprecated` com o PHP 8.5. É só visual e não afeta nada; não existe versão mais nova do WP-CLI ainda.

## Como migrar projetos que já usam a base

1. Copiar os arquivos atualizados da base para o projeto (`Dockerfile`, `.dockerignore`, `docker-compose.yml`, `server/`, `.gitattributes`) e apagar `server/composer.json`.
2. No `.env`, colocar `DB_PASS` e os salts entre aspas simples.
3. Ajustar `WP_VERSION` e `PHP_VERSION` no `Dockerfile` para as versões da produção do projeto.
4. Rodar `docker compose down` e depois `docker compose up -d --build`. Na primeira subida o volume começa vazio e os plugins são baixados uma última vez.
5. Rodar `docker volume prune` para apagar os volumes anônimos antigos.
6. Reinstalar uma vez os plugins premium; depois eles ficam no volume.

## Próximos passos

**Pendências**

- [ ] Versionar a base no Git: a pasta ainda não é um repositório.
- [ ] Migrar os projetos existentes seguindo o roteiro acima.
- [ ] Em cada projeto, igualar `WP_VERSION` e `PHP_VERSION` às versões da produção.
- [ ] Testar FTPS no deploy de um projeto e, se a hospedagem aceitar, descomentar `protocol: ftps` na base.
- [ ] Quando sair um WP-CLI novo, atualizar `WP_CLI_VERSION` para eliminar o aviso `Deprecated`.

**Sugestões para avaliar**

- Banco MySQL local opcional (via profile do Compose), para não trabalhar direto no banco de produção. Hoje, qualquer ação no wp-admin local altera o site real.
- Pasta para plugins premium montada no container, para não precisar reinstalá-los manualmente a cada projeto novo.
- Instalar os plugins nas mesmas versões da produção, em vez da versão mais recente do wordpress.org.
