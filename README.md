# TankCode

Projeto web desenvolvido com Laravel, Inertia.js, Vue 3, Vite e TailwindCSS.
Projeto web desenvolvido com Laravel, Inertia.js, Vue 3, Vite e TailwindCSS.

---

## Requisitos
## Requisitos

Antes de começar, certifique-se de ter instalado:

- **WSL2** (para Windows)
- **Ubuntu 22.04** ou outra distribuição compatível
- **Docker e Docker Compose**
- **PHP >= 8.2**
- **Node.js >= 20.x** (recomendado usar [NVM](https://github.com/nvm-sh/nvm))
- **Composer**
- **MySQL / MariaDB** (ou use via Docker)
Antes de começar, certifique-se de ter instalado:

- **WSL2** (para Windows)
- **Ubuntu 22.04** ou outra distribuição compatível
- **Docker e Docker Compose**
- **PHP >= 8.2**
- **Node.js >= 20.x** (recomendado usar [NVM](https://github.com/nvm-sh/nvm))
- **Composer**
- **MySQL / MariaDB** (ou use via Docker)

---

## Inicializando o ambiente no WSL2

1. **Clone o repositório:**
## Inicializando o ambiente no WSL2

1. **Clone o repositório:**
```bash
git clone <URL_DO_REPOSITORIO>
cd tankcode
Instale a versão correta do Node.js via NVM (Node Version Manager):

Instale a versão correta do Node.js via NVM (Node Version Manager):

bash
nvm install 20
nvm use 20
Verifique as versões instaladas:

bash
node -v
npm -v
php -v
composer -V
Usando Docker para o ambiente de desenvolvimento
Rodar o script de inicialização (recomendado):

bash
chmod +x script.sh
./script.sh
Este script irá:

Derrubar containers antigos (docker compose down -v)

Construir e subir os containers (docker compose up -d --build)

Instalar dependências PHP via Composer

Rodar as migrations e seeders (php artisan migrate:fresh --seed)

Acessar o projeto:

arduino
http://localhost:8080
Rodando manualmente sem script
Subir containers:

bash
docker compose up -d --build
Acessar o container PHP:

bash
docker compose exec php bash
Instalar dependências PHP:

bash
composer install
Rodar migrations:

bash
php artisan migrate:fresh --seed
Frontend
Instalar dependências Node:

bash
npm install
Rodar Vite (dev server):

bash
npm run dev
Build para produção:

bash
npm run build
Troubleshooting
Se o comando npm run dev mostrar vite: not found, remova node_modules e package-lock.json e reinstale dependências:

bash
rm -rf node_modules package-lock.json
npm install
Certifique-se de que está usando a versão do Node recomendada:

bash
nvm use 20
Se houver problemas com PHP ou extensões:

bash
docker compose exec php bash
apt-get update && apt-get install -y <pacotes-necessarios>