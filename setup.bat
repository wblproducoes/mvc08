@echo off
echo ========================================
echo Sistema Administrativo MVC - Setup
echo ========================================
echo.

echo [1/4] Verificando Composer...
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERRO: Composer nao encontrado!
    echo Instale o Composer: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo OK - Composer encontrado

echo.
echo [2/4] Instalando dependencias...
composer install
if %ERRORLEVEL% NEQ 0 (
    echo ERRO: Falha ao instalar dependencias
    pause
    exit /b 1
)

echo.
echo [3/4] Criando arquivo .env...
if not exist .env (
    copy .env.example .env
    echo Arquivo .env criado! Configure suas credenciais.
) else (
    echo Arquivo .env ja existe.
)

echo.
echo [4/4] Criando diretorios...
if not exist storage\logs mkdir storage\logs
if not exist storage\cache mkdir storage\cache
if not exist storage\sessions mkdir storage\sessions
if not exist public\uploads mkdir public\uploads

echo.
echo ========================================
echo Setup concluido!
echo ========================================
echo.
echo Proximos passos:
echo 1. Edite o arquivo .env com suas configuracoes
echo 2. Crie o banco de dados e importe database/schema.sql
echo 3. Execute: php -S localhost:8000 -t public
echo 4. Acesse: http://localhost:8000
echo 5. Login: admin / Senha: admin123
echo.
pause
