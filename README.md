# Base Docker para WordPress

Ambiente local para desenvolver **só o tema** de um site WordPress, usando um banco MySQL externo.
O WordPress, os plugins e os uploads não ficam no repositório: são baixados na primeira subida e
ficam guardados no volume `wp_data` (sobrevive a `docker compose down`).

## Como usar em um projeto novo

1. Copie o conteúdo desta pasta para a raiz do projeto.
2. Renomeie `themes/meu-tema` para o nome do tema (ou coloque o tema existente em `themes/`).
3. Copie `.env.example` para `.env` e preencha:
   - `PROJECT_NAME`, `THEME_NAME` e, se a porta 80 já estiver em uso, `HTTP_PORT`
   - `DB_*` com o banco do projeto
   - os salts (gere em https://api.wordpress.org/secret-key/1.1/salt/)
   - `PRODUCTION_UPLOADS_URL` com a URL de uploads do site em produção

   Senhas e salts ficam entre **aspas simples** (`DB_PASS='abc$123'`): sem aspas ou com aspas
   duplas, o `docker compose` troca `$algo` por uma variável (normalmente vazia) e o valor muda.
4. Suba o ambiente:

   ```bash
   docker compose up -d --build
   docker logs -f <PROJECT_NAME>   # acompanhe até "Ambiente pronto"
   ```

5. Acesse `http://localhost` (ou `http://localhost:HTTP_PORT`).

## O que acontece na subida (`server/entrypoint.sh`)

1. Gera o `.htaccess` com as regras do WordPress e um redirect (302): uploads que não existem
   localmente são buscados na produção, então não é preciso baixar `wp-content/uploads`.
   Se `PRODUCTION_UPLOADS_URL` estiver vazio, o redirect não é criado.
2. Sobe o Apache e espera a REST API (`/?rest_route=/`) responder (avisa no log a cada 30s
   se demorar, normalmente problema de acesso ao banco).
3. Remove os temas `twenty*` e o Akismet.
4. Lê no banco quais plugins estão ativos e instala pelo wordpress.org os que ainda não estão
   no volume, deixando-os com dono `www-data` para o wp-admin conseguir atualizá-los.
   Plugins premium (fora do wordpress.org) precisam ser instalados manualmente.

## Estrutura

| Arquivo | Função |
|---|---|
| `Dockerfile` | Imagem `wordpress:7.1.3-php8.5-apache` (versões em `WP_VERSION`/`PHP_VERSION`) + WP-CLI (`WP_CLI_VERSION`) e `pdo_mysql` |
| `.dockerignore` | Deixa `.env`, `themes/` etc. fora do contexto de build (o Dockerfile não copia nada do projeto) |
| `docker-compose.yml` | Monta tema e configs, passa o `.env` como variáveis de ambiente, guarda o WordPress no volume `wp_data`; `host.docker.internal` funciona também no Linux; porta presa em `127.0.0.1` |
| `server/entrypoint.sh` | Passos da subida (acima) |
| `server/wp-config.php` | Lê tudo das variáveis de ambiente (`getenv`); debug ligado gravando em `wp-content/debug.log`; atualizações automáticas desligadas |
| `server/php.ini` | Upload de 64M, `memory_limit` 256M, `max_execution_time` 120s (montado em `conf.d/zz-custom.ini`) |
| `.github/workflows/deploy.yml` | Ao dar push na `main`, envia `themes/` por FTP (secrets `host`, `user`, `pass`); FTPS pronto para ativar |
| `.vscode/sftp_example.json` | Exemplo para a extensão SFTP do VS Code |

## Comandos úteis

```bash
docker compose up -d --build                    # sobe/reconstrói
docker compose down                             # para e remove o container (mantém o volume wp_data)
docker compose down -v                          # remove também o volume: recomeça do zero na próxima subida
docker volume prune                             # apaga volumes órfãos (sem container)
docker exec <PROJECT_NAME> tail -f wp-content/debug.log   # erros do PHP/WordPress
docker exec <PROJECT_NAME> wp plugin list --allow-root    # WP-CLI dentro do container
```

## Cuidados

- **Banco de produção:** se o `.env` apontar para o banco real, tudo que for feito no wp-admin
  local altera o site no ar. Se aparecer "Atualização do banco de dados necessária", **não clique**
  (a versão do WordPress local pode ser mais nova que a da produção).
- **Versão do WordPress/PHP:** fixada em `WP_VERSION`/`PHP_VERSION` no `Dockerfile`; o ideal é
  igualar à produção. Como o core fica no volume `wp_data`, depois de mudar a versão rode
  `docker compose down -v` e `docker compose up -d --build`, senão o WordPress antigo continua.
  As tags disponíveis estão em <https://hub.docker.com/_/wordpress/tags>.
- **Versões de plugins:** são instaladas as versões mais recentes do wordpress.org, que podem
  ser diferentes das usadas em produção.
- **`.sh` com LF:** o `.gitattributes` força LF nos scripts; com CRLF o entrypoint não roda no Linux.
