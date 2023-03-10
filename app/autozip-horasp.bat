@echo off
set destination=C:\laragon\www\horasp.zip
"C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\horasp\vendor" -x!"C:\laragon\www\horasp\storage"  %destination% "C:\laragon\www\horasp\app" "C:\laragon\www\horasp\resources" "C:\laragon\www\horasp\routes" "C:\laragon\www\horasp\database"
pause