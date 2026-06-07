#!/bin/bash

set -euo pipefail

ENV_FILE=".env"
FRESH=false
DB_FRESH=false
BUILD=false
INSTALL_FRONTEND=false
RUN_SEED=true

usage() {
  cat <<'EOF'
Uso: ./script.sh [opções]

Opções:
  --fresh             recria containers, remove volumes e roda migrate:fresh --seed
  --db-fresh          apenas limpa o banco com migrate:fresh --seed
  --build             força rebuild da imagem PHP
  --frontend          roda npm install e npm run build
  --seed              roda php artisan db:seed após as migrations
  -h, --help          mostra esta ajuda

Exemplos:
  ./script.sh
  ./script.sh --build
  ./script.sh --fresh
  ./script.sh --db-fresh
  ./script.sh --frontend
EOF
}

while [ $# -gt 0 ]; do
  case "$1" in
    --fresh)
      FRESH=true
      BUILD=true
      INSTALL_FRONTEND=true
      RUN_SEED=true
      ;;
    --db-fresh)
      DB_FRESH=true
      RUN_SEED=true
      ;;
    --build)
      BUILD=true
      ;;
    --frontend)
      INSTALL_FRONTEND=true
      ;;
    --seed)
      RUN_SEED=true
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Opção inválida: $1"
      echo
      usage
      exit 1
      ;;
  esac
  shift
done

get_env_value() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | head -n 1 | cut -d '=' -f2- | sed 's/^"//; s/"$//'
}

file_missing_or_older_than() {
  local target="$1"
  local source="$2"
  [ ! -e "$target" ] || [ "$source" -nt "$target" ]
}

if [ ! -f "$ENV_FILE" ]; then
  echo "Arquivo .env não encontrado. Copiando de .env.example..."
  cp .env.example .env
fi

DB_HOST=$(get_env_value "DB_HOST")
DB_PORT=$(get_env_value "DB_PORT")
DB_DATABASE=$(get_env_value "DB_DATABASE")
DB_USERNAME=$(get_env_value "DB_USERNAME")
DB_PASSWORD=$(get_env_value "DB_PASSWORD")
APP_KEY=$(get_env_value "APP_KEY")

if [ "$FRESH" = true ]; then
  echo "Resetando ambiente Docker e volumes..."
  docker compose down -v --remove-orphans
fi

if [ "$BUILD" = true ]; then
  echo "Reconstruindo imagem PHP..."
  docker compose build php
fi

echo "Subindo infraestrutura principal..."
docker compose up -d nginx php db mailpit

if [ ! -f vendor/autoload.php ]; then
  echo "Instalando dependências PHP..."
  docker compose exec php composer install
elif file_missing_or_older_than vendor/composer/installed.json composer.lock; then
  echo "composer.lock mudou. Atualizando dependências PHP..."
  docker compose exec php composer install
else
  echo "Dependências PHP já estão prontas. Pulando composer install."
fi

echo "Aguardando o banco de dados ficar disponível..."
until docker compose exec php php -r "
try {
    new PDO(
        'mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}',
        '${DB_USERNAME}',
        '${DB_PASSWORD}'
    );
    exit(0);
} catch (Throwable \$e) {
    exit(1);
}
" >/dev/null 2>&1; do
  echo "Aguardando MariaDB inicializar..."
  sleep 3
done

if [ -z "${APP_KEY:-}" ]; then
  echo "Gerando APP_KEY..."
  docker compose exec php php artisan key:generate --force
else
  echo "APP_KEY já definida. Pulando geração."
fi

if [ ! -L public/storage ]; then
  echo "Criando link público de storage..."
  docker compose exec php php artisan storage:link
else
  echo "Link public/storage já existe. Pulando."
fi

if [ "$FRESH" = true ] || [ "$DB_FRESH" = true ]; then
  echo "Executando migrações do zero com seed..."
  docker compose exec php php artisan migrate:fresh --seed --force
else
  echo "Executando migrações pendentes..."
  docker compose exec php php artisan migrate --force

  if [ "$RUN_SEED" = true ]; then
    echo "Executando seeders..."
    docker compose exec php php artisan db:seed --force
  fi
fi

echo "Limpando caches da aplicação..."
docker compose exec php php artisan optimize:clear

echo "Gerando actions do Wayfinder..."
docker compose exec php php artisan wayfinder:generate --with-form

if [ "$INSTALL_FRONTEND" = true ]; then
  if command -v npm >/dev/null 2>&1; then
    if [ ! -d node_modules ] || file_missing_or_older_than node_modules package-lock.json; then
      echo "Instalando dependências do frontend..."
      npm install
    else
      echo "Dependências do frontend já estão prontas. Pulando npm install."
    fi

    echo "Gerando build do frontend..."
    npm run build
  else
    echo "npm não encontrado na máquina local."
    echo "Instale Node.js/npm para rodar os comandos do frontend."
  fi
else
  echo "Frontend não solicitado. Pulando npm install e build."
fi

echo
echo "Ambiente disponível em:"
echo "- App: http://localhost:8000"
echo "- Mailpit: http://localhost:8025"
echo
echo "Sugestões:"
echo "- ./script.sh            -> sobe, roda migrations e seeders"
echo "- ./script.sh --build    -> recompila a imagem PHP"
echo "- ./script.sh --fresh    -> recria tudo do zero"
echo "- ./script.sh --db-fresh -> limpa só o banco com migrate:fresh --seed"
echo "- ./script.sh --frontend -> instala dependências JS e gera build"
