#!/bin/bash
{
LOG_FILE="/home/wwecno/pruebas/storage/logs/deploy_log.txt"
ZIP_FILE="/home/wwecno/pruebas/moduloNomina.zip"
PROYECTO="/home/wwecno/pruebas"

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') | $1" >> "$LOG_FILE"
}

run_or_exit() {
    log "🔸 Ejecutando: $1"
    eval "$1" >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log "❌ Error en: $1"
        exit 1
    fi
}

log "🚀 === DEPLOY INICIADO ==="
log "Usuario: $(whoami)"
log "Ubicación: $(pwd)"

cd "$PROYECTO" || { log "❌ No se pudo acceder a $PROYECTO"; exit 1; }

# Paso 1: Descomprimir ZIP
run_or_exit "/usr/bin/unzip -o $ZIP_FILE"

log "✅ Descompresión completada"

# Paso 2: Limpiar caché
run_or_exit "/usr/bin/php artisan optimize:clear"

# Paso 3: Generar cachés
run_or_exit "/usr/bin/php artisan optimize"

# Paso 4: Migraciones
run_or_exit "/usr/bin/php artisan migrate --force"

log "✅ Migración ejecutada correctamente"
log "🎉 === DEPLOY FINALIZADO ==="
} >> /home/wwecno/pruebas/deploy_log2.txt 2>&1