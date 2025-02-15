#!/bin/bash

set -e  # Para o script caso algum comando falhe

echo "🚀 Iniciando atualização do ambiente..."

# Acessa o diretório do projeto
cd /var/www/LibroVault || { echo "❌ Diretório não encontrado!"; exit 1; }

echo "⏳ Colocando a aplicação em modo de manutenção..."
php artisan down || true  # Continua mesmo se o artisan down falhar

echo "📥 Atualizando código do repositório..."
git fetch --all
git reset --hard origin/master

echo "📦 Instalando dependências do backend..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

echo "🔄 Aplicando migrações no banco de dados..."
php artisan migrate --force

echo "🔗 Regenerando rotas do Ziggy..."
php artisan ziggy:generate --types

echo "🗑️ Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link

echo "📦 Instalando dependências do frontend..."
yarn install --check-files --silent

echo "⚡ Gerando build do frontend..."
yarn run vite build

echo "✅ Reativando a aplicação..."
php artisan up

echo "🎉 Atualização concluída com sucesso!"
