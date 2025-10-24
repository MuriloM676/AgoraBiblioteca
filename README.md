# Biblioteca Digital

Sistema completo de gerenciamento de biblioteca desenvolvido com Laravel 11 e Filament 3.

## 📚 Sobre o Projeto

Sistema web para gerenciamento de bibliotecas com funcionalidades completas de controle de livros, usuários, empréstimos e reservas. Possui painel administrativo moderno construído com Filament e sistema de notificações automatizadas.

## ✨ Funcionalidades

### Módulo de Livros
- ✅ Cadastro completo (título, autor, categoria, ISBN, sinopse, ano, editora, páginas, idioma)
- ✅ Upload de capa e arquivo PDF
- ✅ Geração automática de QR Code para cada livro
- ✅ Controle de estoque (quantidade total e disponível)
- ✅ Filtros por categoria, status, autor e disponibilidade
- ✅ Busca avançada

### Módulo de Empréstimos
- ✅ Registro de empréstimos com datas de retirada e devolução
- ✅ Sistema de renovação (máximo 2 renovações)
- ✅ Cálculo automático de multas por atraso (R$ 2,00/dia)
- ✅ Controle de status (ativo, devolvido, atrasado, cancelado)
- ✅ Histórico completo de empréstimos
- ✅ Notificações automáticas por e-mail

### Módulo de Reservas
- ✅ Sistema de reservas com prazo de expiração (3 dias)
- ✅ Conversão automática de reserva em empréstimo
- ✅ Controle de status (pendente, confirmada, cancelada, expirada)
- ✅ Notificações de confirmação

### Módulo de Usuários
- ✅ Cadastro completo (nome, e-mail, CPF, telefone, endereço, matrícula)
- ✅ Sistema de roles e permissões (Admin e Usuário)
- ✅ Controle de usuários ativos/inativos
- ✅ Histórico de empréstimos por usuário
- ✅ Relatórios de atividades

### Dashboard Administrativa
- ✅ Cards com indicadores principais
  - Total de livros cadastrados
  - Livros disponíveis
  - Empréstimos ativos
  - Empréstimos atrasados
  - Reservas pendentes
  - Usuários ativos
- ✅ Gráfico de empréstimos dos últimos 7 dias
- ✅ Gráfico de livros por categoria
- ✅ Tabela de empréstimos atrasados
- ✅ Widgets interativos com Filament

### Sistema de Notificações
- ✅ E-mail quando o prazo está próximo do vencimento
- ✅ E-mail quando o empréstimo está atrasado
- ✅ Notificações no banco de dados
- ✅ Sistema de notificações configurável

### Auditoria e Logs
- ✅ Registro de todas as atividades importantes
- ✅ Log de criação, edição e exclusão
- ✅ Rastreamento de mudanças com Spatie Activity Log
- ✅ Histórico completo de operações

## 🛠️ Tecnologias

- **Laravel 11** - Framework PHP
- **Filament 3** - Painel administrativo
- **MySQL** - Banco de dados
- **Spatie Permission** - Controle de permissões
- **Spatie Activity Log** - Logs de auditoria
- **Simple QR Code** - Geração de QR Codes
- **Maatwebsite Excel** - Exportação de relatórios

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- MySQL 8.0 ou superior
- Node.js e NPM (para assets)

## 🚀 Instalação

### 1. Clone o repositório ou navegue até a pasta

```bash
cd c:\Users\muril\Downloads\AgoraBiblioteca
```

### 2. Instale as dependências

```bash
composer install
npm install
```

### 3. Configure o arquivo .env

Copie o arquivo `.env.example` para `.env` e configure as variáveis:

```bash
copy .env.example .env
```

Configure o banco de dados no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca_digital
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

Configure o e-mail (opcional, para notificações):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@biblioteca.com
```

### 4. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 5. Crie o banco de dados

Crie um banco de dados MySQL chamado `biblioteca_digital`:

```sql
CREATE DATABASE biblioteca_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Execute as migrations e seeders

```bash
php artisan migrate --seed
```

Este comando irá:
- Criar todas as tabelas necessárias
- Criar roles e permissões
- Criar usuários de teste
- Popular o banco com dados de exemplo

### 7. Crie o link simbólico para storage

```bash
php artisan storage:link
```

### 8. Compile os assets

```bash
npm run build
```

### 9. Inicie o servidor

```bash
php artisan serve
```

Acesse: http://localhost:8000/admin

## 👤 Usuários de Teste

### Administrador
- **E-mail:** admin@biblioteca.com
- **Senha:** admin123
- **Permissões:** Acesso total ao sistema

### Usuário Comum
- **E-mail:** joao@example.com
- **Senha:** senha123
- **Permissões:** Visualizar livros, criar e gerenciar suas próprias reservas

## 📁 Estrutura do Projeto

```
app/
├── Filament/
│   ├── Resources/      # Resources do Filament
│   │   ├── CategoriaResource.php
│   │   ├── LivroResource.php
│   │   ├── EmprestimoResource.php
│   │   ├── ReservaResource.php
│   │   └── UserResource.php
│   └── Widgets/        # Widgets da dashboard
│       ├── StatsOverview.php
│       ├── EmprestimosChart.php
│       ├── LivrosPorCategoriaChart.php
│       └── EmprestimosAtrasadosTable.php
├── Models/             # Models Eloquent
│   ├── User.php
│   ├── Categoria.php
│   ├── Livro.php
│   ├── Emprestimo.php
│   └── Reserva.php
├── Notifications/      # Notificações
│   ├── EmprestimoProximoVencimento.php
│   └── EmprestimoAtrasado.php
├── Policies/           # Policies de autorização
│   ├── LivroPolicy.php
│   ├── EmprestimoPolicy.php
│   └── ReservaPolicy.php
└── Services/           # Services (lógica de negócio)

database/
├── factories/          # Factories para testes
├── migrations/         # Migrations do banco
└── seeders/           # Seeders para popular dados

```

## 🔐 Permissões e Roles

### Admin
- Gerenciar todos os módulos
- Criar, editar e excluir livros
- Gerenciar empréstimos e reservas
- Gerenciar usuários
- Visualizar dashboard e relatórios
- Acesso total ao sistema

### User
- Visualizar catálogo de livros
- Criar reservas
- Visualizar próprio histórico
- Cancelar próprias reservas

## 📊 Comandos Úteis

### Criar um novo usuário admin
```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Seu Nome',
    'email' => 'seuemail@example.com',
    'password' => bcrypt('suasenha'),
    'is_active' => true,
]);
$user->assignRole('admin');
```

### Executar notificações de empréstimos atrasados
```bash
php artisan schedule:work
```

### Limpar cache
```bash
php artisan optimize:clear
```

### Recriar banco de dados
```bash
php artisan migrate:fresh --seed
```

## 🧪 Testes

Para executar os testes:

```bash
php artisan test
```

## 📝 Próximas Funcionalidades

- [ ] Área pública para pesquisa de livros
- [ ] Sistema de avaliação de livros
- [ ] Integração com APIs de livros (Google Books)
- [ ] Relatórios em PDF
- [ ] Chat de suporte
- [ ] App mobile
- [ ] Sistema de multas online

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## 📄 Licença

Este projeto está sob a licença MIT.

## 👨‍💻 Autor

Desenvolvido com Laravel e Filament

## 📞 Suporte

Para suporte, envie um e-mail para suporte@biblioteca.com

---

⭐ Se este projeto foi útil, considere dar uma estrela!
