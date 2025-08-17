#!/bin/bash

docker compose down -v 
docker compose up -d --build
docker compose exec php composer install

docker compose exec php php artisan migrate:fresh --seed

echo 
echo "Projeto disponivel em http://localhost:8000"
echo