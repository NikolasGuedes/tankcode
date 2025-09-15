#!/bin/bash

echo "Parando e removendo containers existentes..."
docker compose down -v 

echo "Construindo e iniciando containers..."
docker compose up -d --build

echo "Instalando dependências PHP..."
docker compose exec php composer install

echo "Aguardando o banco de dados ficar disponível..."
# Aguardar até que o Laravel consiga conectar ao banco
until docker compose exec php php -r "
try {
    \$pdo = new PDO('mysql:host=db;dbname=tank-db', 'root', 'admin');
    echo 'Conexão bem-sucedida!';
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" >/dev/null 2>&1; do
  echo "Aguardando MariaDB inicializar..."
  sleep 3
done

echo "Banco de dados está pronto!"
sleep 2

echo "Executando migrações..."
docker compose exec php php artisan migrate:fresh --seed

echo "Instalando dependências do Node.js..."
npm install

echo "Compilando assets do frontend..."
npm run build

echo 
echo "Projeto disponível em http://localhost:8000"
echo