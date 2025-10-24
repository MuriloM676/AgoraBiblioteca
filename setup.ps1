# Script de Setup do Projeto Biblioteca Digital
# Execute este script no PowerShell como Administrador

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Biblioteca Digital - Setup Script   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se está rodando como Admin
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "AVISO: Este script deve ser executado como Administrador para algumas operações." -ForegroundColor Yellow
    Write-Host "Pressione qualquer tecla para continuar mesmo assim..."
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}

# Função para verificar instalação
function Test-CommandExists {
    param($command)
    $null = Get-Command $command -ErrorAction SilentlyContinue
    return $?
}

Write-Host "`n[1/6] Verificando pré-requisitos..." -ForegroundColor Yellow

# Verificar PHP
if (Test-CommandExists "php") {
    $phpVersion = php -r "echo PHP_VERSION;"
    Write-Host "  ✓ PHP instalado: $phpVersion" -ForegroundColor Green
} else {
    Write-Host "  ✗ PHP não encontrado!" -ForegroundColor Red
    Write-Host "    Baixe em: https://windows.php.net/download/" -ForegroundColor Yellow
    exit 1
}

# Verificar Composer
if (Test-CommandExists "composer") {
    Write-Host "  ✓ Composer instalado" -ForegroundColor Green
} else {
    Write-Host "  ✗ Composer não encontrado!" -ForegroundColor Red
    Write-Host "    Baixe em: https://getcomposer.org/download/" -ForegroundColor Yellow
    exit 1
}

# Verificar Node.js
if (Test-CommandExists "node") {
    $nodeVersion = node --version
    Write-Host "  ✓ Node.js instalado: $nodeVersion" -ForegroundColor Green
} else {
    Write-Host "  ✗ Node.js não encontrado!" -ForegroundColor Red
    Write-Host "    Baixe em: https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}

# Verificar Docker
if (Test-CommandExists "docker") {
    Write-Host "  ✓ Docker instalado" -ForegroundColor Green
} else {
    Write-Host "  ✗ Docker não encontrado!" -ForegroundColor Red
    Write-Host "    Baixe em: https://www.docker.com/products/docker-desktop/" -ForegroundColor Yellow
    exit 1
}

Write-Host "`n[2/6] Iniciando containers Docker..." -ForegroundColor Yellow
docker-compose up -d
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Containers iniciados" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao iniciar containers" -ForegroundColor Red
    exit 1
}

Write-Host "`n[3/6] Aguardando MySQL inicializar (15 segundos)..." -ForegroundColor Yellow
Start-Sleep -Seconds 15
Write-Host "  ✓ MySQL pronto" -ForegroundColor Green

Write-Host "`n[4/6] Instalando dependências do Composer..." -ForegroundColor Yellow
composer install --no-interaction
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Dependências do Composer instaladas" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao instalar dependências do Composer" -ForegroundColor Red
    exit 1
}

Write-Host "`n[5/6] Instalando dependências do Node.js..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Dependências do Node.js instaladas" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao instalar dependências do Node.js" -ForegroundColor Red
    exit 1
}

Write-Host "`n[6/6] Configurando Laravel..." -ForegroundColor Yellow

# Gerar chave da aplicação
php artisan key:generate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Chave da aplicação gerada" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao gerar chave" -ForegroundColor Red
}

# Executar migrations e seeders
Write-Host "`n  Executando migrations e seeders..." -ForegroundColor Cyan
php artisan migrate:fresh --seed --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Banco de dados configurado" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao configurar banco de dados" -ForegroundColor Red
    Write-Host "    Verifique as credenciais no arquivo .env" -ForegroundColor Yellow
}

# Criar link simbólico do storage
php artisan storage:link
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Link do storage criado" -ForegroundColor Green
} else {
    Write-Host "  ⚠ Aviso: Link do storage pode já existir" -ForegroundColor Yellow
}

# Compilar assets
Write-Host "`n  Compilando assets..." -ForegroundColor Cyan
npm run build
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ Assets compilados" -ForegroundColor Green
} else {
    Write-Host "  ✗ Erro ao compilar assets" -ForegroundColor Red
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Setup Concluído com Sucesso! 🎉     " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan

Write-Host "`n📌 Informações Importantes:" -ForegroundColor Cyan
Write-Host "  • MySQL rodando em: localhost:3306" -ForegroundColor White
Write-Host "  • phpMyAdmin: http://localhost:8080" -ForegroundColor White
Write-Host "  • Banco: biblioteca_digital" -ForegroundColor White
Write-Host "  • Usuário DB: biblioteca_user" -ForegroundColor White
Write-Host "  • Senha DB: biblioteca_pass" -ForegroundColor White

Write-Host "`n🚀 Para iniciar o servidor:" -ForegroundColor Cyan
Write-Host "  php artisan serve" -ForegroundColor Yellow

Write-Host "`n🔑 Credenciais do Sistema:" -ForegroundColor Cyan
Write-Host "  Admin:" -ForegroundColor White
Write-Host "    Email: admin@biblioteca.com" -ForegroundColor Yellow
Write-Host "    Senha: admin123" -ForegroundColor Yellow
Write-Host "`n  Usuário:" -ForegroundColor White
Write-Host "    Email: joao@example.com" -ForegroundColor Yellow
Write-Host "    Senha: senha123" -ForegroundColor Yellow

Write-Host "`n📚 Acesse o painel em:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000/admin" -ForegroundColor Yellow

Write-Host "`n"
