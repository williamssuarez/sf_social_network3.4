@echo off
rem Obtén la fecha actual en un formato que puedas usar en el nombre del archivo
for /f "tokens=1-4 delims=/ " %%a in ('echo %date%') do (
    set day=%%a
    set month=%%b
    set year=%%c
)

rem Asegúrate de que el formato de fecha sea "YYYY-MM-DD"
set formatted_date=%year%-%month%-%day%

rem path to mysql server bin folder
cd "C:\xampp\mysql\bin"

rem credentials to connect to mysql server
set mysql_user=root
set mysql_password=

rem backup file name generation with the current date
set backup_path=C:\Users\Operador\PhpstormProjects\curso_symfony4\backup
set backup_name=socialnetwork-backup-%formatted_date%

rem backup creation
mysqldump --user=%mysql_user% --password=%mysql_password% --databases curso_social_network --routines --events --result-file="%backup_path%\%backup_name%.sql"

if %ERRORLEVEL% neq 0 (
    (echo Backup failed: error during dump creation) >> "%backup_path%\mysql_backup_log.txt"
) else (echo Backup successful) >> "%backup_path%\mysql_backup_log.txt"
