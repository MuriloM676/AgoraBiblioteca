@echo off
chcp 65001 >nul
echo ========================================
echo   Biblioteca Digital - Setup Script
echo ========================================
echo.

echo [1/8] Verificando pré-requisitos...

where php >nul 2>&1
if %errorlevel% neq 0 (
    echo   X PHP não encontrado no PATH!
    echo   Adicione o PHP ao PATH do sistema ou execute este script de onde o PHP está instalado
    pause
    exit /b 1
) else (
    echo   ✓ PHP encontrado
)

where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo   X Composer não encontrado no PATH!
    echo   Execute este comando em um terminal onde o Composer funciona
    pause
    exit /b 1
) else (
    echo   ✓ Composer encontrado
)

where node >nul 2>&1
if %errorlevel% neq 0 (
    echo   X Node.js não encontrado no PATH!
    pause
    exit /b 1
) else (
    echo   ✓ Node.js encontrado
)

where docker >nul 2>&1
if %errorlevel% neq 0 (
    echo   X Docker não encontrado!
    pause
    exit /b 1
) else (
    echo   ✓ Docker encontrado
)

echo.
echo [2/8] Iniciando containers Docker...
docker-compose up -d
if %errorlevel% neq 0 (
    echo   X Erro ao iniciar containers
    pause
    exit /b 1
)
echo   ✓ Containers iniciados

echo.
echo [3/8] Aguardando MySQL inicializar (15 segundos)...
timeout /t 15 /nobreak >nul
echo   ✓ MySQL pronto

echo.
echo [4/8] Instalando dependências do Composer...
composer install --no-interaction
if %errorlevel% neq 0 (
    echo   X Erro ao instalar dependências do Composer
    pause
    exit /b 1
)
echo   ✓ Dependências do Composer instaladas

echo.
echo [5/8] Instalando dependências do Node.js...
call npm install
if %errorlevel% neq 0 (
    echo   X Erro ao instalar dependências do Node.js
    pause
    exit /b 1
)
echo   ✓ Dependências do Node.js instaladas

echo.
echo [6/8] Gerando chave da aplicação...
php artisan key:generate --force
if %errorlevel% neq 0 (
    echo   X Erro ao gerar chave
)
echo   ✓ Chave gerada

echo.
echo [7/8] Executando migrations e seeders...
php artisan migrate:fresh --seed --force
if %errorlevel% neq 0 (
    echo   X Erro ao configurar banco de dados
    echo   Verifique as credenciais no arquivo .env
    pause
) else (
    echo   ✓ Banco de dados configurado
)

echo.
echo [8/8] Criando link do storage...
php artisan storage:link
echo   ✓ Link do storage criado

echo.
echo Compilando assets...
call npm run build
if %errorlevel% neq 0 (
    echo   X Erro ao compilar assets
) else (
    echo   ✓ Assets compilados
)

echo.
echo ========================================
echo   Setup Concluído com Sucesso! 🎉
echo ========================================
echo.
echo 📌 Informações Importantes:
echo   • MySQL: localhost:3306
echo   • phpMyAdmin: http://localhost:8080
echo   • Banco: biblioteca_digital
echo   • Usuário: biblioteca_user
echo   • Senha: biblioteca_pass
echo.
echo 🚀 Para iniciar o servidor:
echo   php artisan serve
echo.
echo 🔑 Credenciais do Sistema:
echo   Admin: admin@biblioteca.com / admin123
echo   User: joao@example.com / senha123
echo.
echo 📚 Acesse: http://localhost:8000/admin
echo.
pause
