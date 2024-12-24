#!/bin/bash
set -e

echo "Iniciando despliegue..."

# Variables
PROJECT_DIR="/home/wwecno/pruebas"
REPO_URL="https://github.com/zilef2/moduloNomina.git"
BRANCH="master"


echo "Configurando Git para permitir el acceso al repositorio..."
git config --global --add safe.directory "$PROJECT_DIR"
#sudo chown -R root:root /home/wwecno/pruebas/node_modules
#chmod 755 /home/wwecno/pruebas/node_modules/.bin/vite
echo "Actualizando repositorio..."
#    cd $PROJECT_DIR
#    git fetch origin $BRANCH
#    git reset --soft origin/$BRANCH  # Actualiza sin perder cambios locales
cd $PROJECT_DIR
git pull origin $BRANCH


cd $PROJECT_DIR
#composer install --no-dev --optimize-autoloader
#npm install
#npm run build


# Configurar permisos
chown -R root:root $PROJECT_DIR
chmod -R 705 $PROJECT_DIR/storage $PROJECT_DIR/bootstrap/cache

# Actualizar las migraciones
#php artisan migrate --force

echo "Despliegue completo."
