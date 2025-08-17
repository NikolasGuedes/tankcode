# TankCode - Guia Rápido de Inicialização

Projeto Laravel + Inertia.js + Vue 3 + Vite + TailwindCSS.

---

## Pré-requisitos

- WSL2 (Windows) ou Linux
- Docker & Docker Compose
- PHP >= 8.2
- Node.js >= 20 (recomendado via [NVM](https://github.com/nvm-sh/nvm))
- Composer
- MySQL / MariaDB (ou via Docker)

---

## Setup Rápido

### 1. Clonar repositório
```bash
git clone <URL_DO_REPOSITORIO>
cd tankcode
2. Node.js via NVM
bash
Copiar
Editar
nvm install 20
nvm use 20
node -v
npm -v
3. Inicializar ambiente Docker
bash
Copiar
Editar
chmod +x script.sh
./script.sh
Isso irá: derrubar containers antigos, buildar e subir novos, instalar dependências e rodar migrations.

4. Acessar projeto
arduino
Copiar
Editar
http://localhost:8080
Frontend
Instalar dependências
bash
Copiar
Editar
npm install
Rodar dev server
bash
Copiar
Editar
npm run dev
Build para produção
bash
Copiar
Editar
npm run build
Troubleshooting
Se vite: not found:

bash
Copiar
Editar
rm -rf node_modules package-lock.json
npm install
nvm use 20
Para problemas com PHP/extensões:

bash
Copiar
Editar
docker compose exec php bash
apt-get update && apt-get install -y <pacotes-necessarios>
