#!/bin/bash

set -e

ENV_FILE=".env"

get_env_value() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | head -n 1 | cut -d '=' -f2- | sed 's/^"//; s/"$//'
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

echo "Parando e removendo containers/volumes antigos..."
docker compose down -v --remove-orphans

echo "Construindo imagens atualizadas..."
docker compose build --no-cache

echo "Subindo infraestrutura principal (nginx, php, banco e mailpit)..."
docker compose up -d nginx php db mailpit

echo "Instalando dependências PHP..."
docker compose exec php composer install

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

echo "Gerando APP_KEY..."
docker compose exec php php artisan key:generate --force

echo "Limpando caches da aplicação..."
docker compose exec php php artisan optimize:clear

echo "Criando link público de storage..."
if docker compose exec php php artisan storage:link; then
  echo "Link de storage criado com sucesso."
else
  echo "Link de storage já existe ou não precisou ser recriado. Seguindo..."
fi

echo "Executando migrações com seed..."
docker compose exec php php artisan migrate:fresh --seed

echo "Gerando actions do Wayfinder..."
docker compose exec php php artisan wayfinder:generate --with-form

if command -v npm >/dev/null 2>&1; then
  echo "Instalando dependências do frontend no host..."
  npm install

  echo "Gerando build do frontend..."
  npm run build
else
  echo "npm não encontrado na máquina local."
  echo "Instale Node.js/npm para rodar os comandos do frontend:"
  echo "- npm install"
  echo "- npm run dev    (desenvolvimento)"
  echo "- npm run build  (produção)"
fi

echo
echo "Ambiente disponível em:"
echo "- App: http://localhost:8000"
echo "- Mailpit: http://localhost:8025"
echo
echo "Para desenvolvimento frontend, rode localmente: npm run dev"
echo
