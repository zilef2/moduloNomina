#!/bin/bash

{
  echo "=== DEPLOY INICIADO ==="
  echo "Fecha: $(date)"
  echo "Usuario: $(whoami)"
  echo "Ubicación: $(pwd)"
  echo "-----------"

  /usr/bin/unzip -o moduloNomina.zip
  /usr/bin/php artisan optimize:clear
  /usr/bin/php artisan optimize
  /usr/bin/php artisan migrate

  echo "=== DEPLOY FINALIZADO ==="
} >> /home/wwecno/pruebas/storage/logs/deploy_log.txt 2>&1
