#!/bin/bash
set -e

echo "Iniciando despliegue..."

# Variables
PROJECT_DIR="/home/wwecno/pruebas"
REPO_URL="https://github.com/zilef2/moduloNomina.git"
BRANCH="main"

# Clonar o actualizar el repositorio
if [ ! -d "$PROJECT_DIR" ]; then
#    git clone -b $BRANCH $REPO_URL $PROJECT_DIR
else
    cd $PROJECT_DIR
    git pull origin $BRANCH
fi


cd $PROJECT_DIR
#composer install --no-dev --optimize-autoloader
#npm install
npm run build


# Configurar permisos
chown -R root:root $PROJECT_DIR
chmod -R 705 $PROJECT_DIR/storage $PROJECT_DIR/bootstrap/cache

# Actualizar las migraciones
#php artisan migrate --force

echo "Despliegue completo."
