# 📦 Guia Completo de Instalação - Biblioteca Digital

## 🔧 Requisitos do Sistema

Antes de começar, certifique-se de ter instalado:

### Windows
- **PHP 8.2+** - [Download](https://windows.php.net/download/)
- **Composer** - [Download](https://getcomposer.org/download/)
- **MySQL 8.0+** - [Download](https://dev.mysql.com/downloads/mysql/)
- **Node.js 18+** - [Download](https://nodejs.org/)
- **Git** (opcional) - [Download](https://git-scm.com/download/win)

### Extensões PHP Necessárias
Certifique-se que as seguintes extensões estão habilitadas no `php.ini`:
```ini
extension=pdo_mysql
extension=mbstring
extension=fileinfo
extension=gd
extension=zip
extension=curl
extension=openssl
```

## 📥 Passo a Passo da Instalação

### 1. Instalar PHP, Composer e MySQL

#### Instalando PHP no Windows
1. Baixe o PHP 8.2+ (Thread Safe) do site oficial
2. Extraia para `C:\php`
3. Adicione `C:\php` às variáveis de ambiente PATH
4. Copie `php.ini-development` para `php.ini`
5. Edite `php.ini` e descomente as extensões necessárias

#### Instalando Composer
1. Baixe e execute o instalador do Composer
2. Durante a instalação, aponte para o executável do PHP (`C:\php\php.exe`)
3. Após instalar, abra um novo terminal e execute: `composer --version`

#### Instalando MySQL
1. Baixe e execute o instalador do MySQL
2. Durante a instalação, defina a senha do usuário root
3. Anote a senha, você precisará dela

### 2. Preparar o Projeto

Abra o PowerShell ou CMD e navegue até a pasta do projeto:

```bash
cd C:\Users\muril\Downloads\AgoraBiblioteca
```

### 3. Instalar Dependências do PHP

```bash
composer install
```

Este comando pode levar alguns minutos. Ele irá baixar todas as dependências do Laravel e Filament.

### 4. Configurar Variáveis de Ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
copy .env.example .env
```

Edite o arquivo `.env` e configure:

```env
APP_NAME="Biblioteca Digital"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca_digital
DB_USERNAME=root
DB_PASSWORD=SUA_SENHA_MYSQL_AQUI
```

### 5. Gerar Chave da Aplicação

```bash
php artisan key:generate
```

### 6. Criar o Banco de Dados

Abra o MySQL Workbench ou linha de comando do MySQL e execute:

```sql
CREATE DATABASE biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Ou via linha de comando:

```bash
mysql -u root -p
```

Digite a senha e execute:
```sql
CREATE DATABASE biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 7. Executar Migrations e Seeders

Este comando criará todas as tabelas e populará o banco com dados de exemplo:

```bash
php artisan migrate --seed
```

**Importante:** Se houver erro, verifique se:
- O MySQL está rodando
- As credenciais no `.env` estão corretas
- O banco de dados foi criado

### 8. Criar Link Simbólico para Storage

```bash
php artisan storage:link
```

Este comando cria um link da pasta `storage/app/public` para `public/storage`, permitindo acesso aos arquivos enviados.

### 9. Instalar Dependências do Node.js

```bash
npm install
```

### 10. Compilar Assets

Para desenvolvimento:
```bash
npm run dev
```

Ou para produção:
```bash
npm run build
```

### 11. Iniciar o Servidor

Em um terminal, execute:

```bash
php artisan serve
```

O sistema estará disponível em: **http://localhost:8000**

## 🔑 Acesso ao Sistema

### Painel Administrativo
URL: http://localhost:8000/admin

**Credenciais do Administrador:**
- E-mail: `admin@biblioteca.com`
- Senha: `admin123`

**Credenciais de Usuário Comum:**
- E-mail: `joao@example.com`
- Senha: `senha123`

## ⚙️ Configurações Adicionais

### Configurar E-mail (Opcional)

Para ativar notificações por e-mail, edite o `.env`:

#### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@biblioteca.com
MAIL_FROM_NAME="Biblioteca Digital"
```

**Nota:** Para Gmail, você precisa criar uma "Senha de App" em:
https://myaccount.google.com/apppasswords

### Executar Tarefas Agendadas

Para notificações automáticas de empréstimos, execute em outro terminal:

```bash
php artisan schedule:work
```

Ou configure no Agendador de Tarefas do Windows para executar:
```bash
php C:\Users\muril\Downloads\AgoraBiblioteca\artisan schedule:run
```

### Executar Fila de Jobs (Opcional)

Se quiser processar jobs em background:

```bash
php artisan queue:work
```

## 🧪 Executar Testes

```bash
php artisan test
```

## 🔄 Comandos Úteis

### Limpar Cache
```bash
php artisan optimize:clear
```

### Recriar Banco de Dados
```bash
php artisan migrate:fresh --seed
```

### Verificar Notificações Manualmente
```bash
php artisan emprestimos:notificar
```

### Criar Novo Usuário Admin
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Novo Admin',
    'email' => 'novoadmin@biblioteca.com',
    'password' => bcrypt('senha123'),
    'is_active' => true,
]);
$user->assignRole('admin');
exit
```

## 🐛 Resolução de Problemas

### Erro: "Class not found"
```bash
composer dump-autoload
```

### Erro: "SQLSTATE[HY000] [2002]"
- Verifique se o MySQL está rodando
- Verifique as credenciais no `.env`

### Erro: "Permission denied" no storage
```bash
# Windows (PowerShell como Admin)
icacls "storage" /grant Everyone:F /t
icacls "bootstrap\cache" /grant Everyone:F /t
```

### Porta 8000 já em uso
```bash
php artisan serve --port=8001
```

### Erro ao compilar assets
```bash
npm cache clean --force
rm -rf node_modules
npm install
```

## 📚 Recursos Adicionais

- [Documentação Laravel](https://laravel.com/docs)
- [Documentação Filament](https://filamentphp.com/docs)
- [Documentação Spatie Permission](https://spatie.be/docs/laravel-permission)

## 🆘 Suporte

Se encontrar problemas:

1. Verifique os logs em `storage/logs/laravel.log`
2. Execute `php artisan optimize:clear`
3. Consulte a documentação oficial do Laravel
4. Verifique se todas as extensões PHP estão habilitadas

## ✅ Checklist Final

- [ ] PHP 8.2+ instalado e funcionando
- [ ] Composer instalado
- [ ] MySQL instalado e rodando
- [ ] Banco de dados criado
- [ ] Dependências instaladas (`composer install`)
- [ ] Arquivo `.env` configurado
- [ ] Chave gerada (`php artisan key:generate`)
- [ ] Migrations executadas (`php artisan migrate --seed`)
- [ ] Storage link criado (`php artisan storage:link`)
- [ ] Assets compilados (`npm run build`)
- [ ] Servidor iniciado (`php artisan serve`)
- [ ] Login funcionando em http://localhost:8000/admin

**Parabéns! 🎉 Seu sistema está pronto para uso!**
