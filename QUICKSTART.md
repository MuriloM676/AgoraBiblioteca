# 🚀 Guia Rápido de Instalação

## Pré-requisitos (Instale antes de começar)

1. **PHP 8.2+** → https://windows.php.net/download/
2. **Composer** → https://getcomposer.org/download/
3. **Node.js 18+** → https://nodejs.org/
4. **Docker Desktop** → https://www.docker.com/products/docker-desktop/

---

## ⚡ Instalação Rápida (Recomendado)

### Opção 1: Script Automático

Abra o PowerShell **como Administrador** e execute:

```powershell
cd C:\Users\muril\Downloads\AgoraBiblioteca
.\setup.ps1
```

Este script vai:
- ✅ Verificar todos os pré-requisitos
- ✅ Iniciar MySQL via Docker
- ✅ Instalar todas as dependências
- ✅ Configurar o banco de dados
- ✅ Criar dados de exemplo

---

### Opção 2: Instalação Manual

```powershell
# 1. Navegar até a pasta
cd C:\Users\muril\Downloads\AgoraBiblioteca

# 2. Iniciar MySQL via Docker
docker-compose up -d

# 3. Aguardar MySQL inicializar
Start-Sleep -Seconds 15

# 4. Instalar dependências PHP
composer install

# 5. Instalar dependências Node.js
npm install

# 6. Gerar chave da aplicação
php artisan key:generate

# 7. Criar banco e popular dados
php artisan migrate:fresh --seed

# 8. Criar link do storage
php artisan storage:link

# 9. Compilar assets
npm run build
```

---

## 🎯 Iniciar o Servidor

```powershell
php artisan serve
```

Acesse: **http://localhost:8000/admin**

---

## 🔑 Credenciais Padrão

### Administrador
- **E-mail:** admin@biblioteca.com
- **Senha:** admin123

### Usuário Comum
- **E-mail:** joao@example.com
- **Senha:** senha123

---

## 🐳 Gerenciar Docker

```powershell
# Ver status dos containers
docker-compose ps

# Parar containers
docker-compose down

# Reiniciar containers
docker-compose restart

# Ver logs do MySQL
docker-compose logs mysql
```

---

## 🌐 Serviços Disponíveis

| Serviço | URL | Credenciais |
|---------|-----|-------------|
| **Aplicação** | http://localhost:8000/admin | admin@biblioteca.com / admin123 |
| **phpMyAdmin** | http://localhost:8080 | root / root_password |
| **MySQL** | localhost:3306 | biblioteca_user / biblioteca_pass |

---

## 🔧 Comandos Úteis

```powershell
# Limpar cache
php artisan optimize:clear

# Recriar banco de dados
php artisan migrate:fresh --seed

# Executar testes
php artisan test

# Verificar notificações
php artisan emprestimos:notificar

# Compilar assets em desenvolvimento (watch mode)
npm run dev
```

---

## ❗ Problemas Comuns

### Porta 3306 já em uso
```powershell
# Alterar porta no docker-compose.yml para 3307
# E atualizar DB_PORT=3307 no .env
```

### Erro "Class not found"
```powershell
composer dump-autoload
```

### Erro de permissão no storage
```powershell
# PowerShell como Admin
icacls "storage" /grant Everyone:F /t
icacls "bootstrap\cache" /grant Everyone:F /t
```

---

## 📖 Documentação Completa

- **README.md** - Documentação geral do projeto
- **INSTALL.md** - Guia detalhado de instalação
- **DOCKER.md** - Comandos e troubleshooting do Docker
- **PROJETO_COMPLETO.md** - Resumo completo do projeto

---

## ✅ Checklist

- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] Node.js instalado
- [ ] Docker Desktop instalado e rodando
- [ ] Containers Docker iniciados (`docker-compose up -d`)
- [ ] Dependências instaladas (`composer install && npm install`)
- [ ] Chave gerada (`php artisan key:generate`)
- [ ] Banco configurado (`php artisan migrate:fresh --seed`)
- [ ] Storage linked (`php artisan storage:link`)
- [ ] Assets compilados (`npm run build`)
- [ ] Servidor rodando (`php artisan serve`)

---

**🎉 Pronto! Seu sistema está configurado e funcionando!**

Acesse: http://localhost:8000/admin
