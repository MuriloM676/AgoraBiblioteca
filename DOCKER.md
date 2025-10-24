# 🐳 Docker - Biblioteca Digital

## Serviços Configurados

### MySQL 8.0
- **Host:** 127.0.0.1 (localhost)
- **Porta:** 3306
- **Database:** biblioteca_digital
- **Usuário:** biblioteca_user
- **Senha:** biblioteca_pass
- **Root Password:** root_password

### phpMyAdmin
- **URL:** http://localhost:8080
- **Usuário:** root
- **Senha:** root_password

---

## 🚀 Comandos Docker

### Iniciar os containers
```bash
docker-compose up -d
```

### Parar os containers
```bash
docker-compose down
```

### Parar e remover volumes (ATENÇÃO: apaga dados do banco!)
```bash
docker-compose down -v
```

### Ver logs do MySQL
```bash
docker-compose logs mysql
```

### Ver logs em tempo real
```bash
docker-compose logs -f mysql
```

### Verificar status dos containers
```bash
docker-compose ps
```

### Reiniciar containers
```bash
docker-compose restart
```

### Acessar o terminal do MySQL
```bash
docker exec -it biblioteca_mysql mysql -u biblioteca_user -p
# Senha: biblioteca_pass
```

### Fazer backup do banco
```bash
docker exec biblioteca_mysql mysqldump -u biblioteca_user -pbiblioteca_pass biblioteca_digital > backup.sql
```

### Restaurar backup
```bash
docker exec -i biblioteca_mysql mysql -u biblioteca_user -pbiblioteca_pass biblioteca_digital < backup.sql
```

---

## 🔧 Troubleshooting

### Porta 3306 já está em uso
Se você já tem MySQL instalado localmente, pode alterar a porta no docker-compose.yml:
```yaml
ports:
  - "3307:3306"  # Usar porta 3307 no host
```

E atualizar o .env:
```env
DB_PORT=3307
```

### Container não inicia
Verificar logs:
```bash
docker-compose logs mysql
```

### Resetar completamente o ambiente
```bash
docker-compose down -v
docker-compose up -d
```

---

## 📊 Acesso ao phpMyAdmin

1. Abra o navegador em: http://localhost:8080
2. Faça login com:
   - **Servidor:** mysql
   - **Usuário:** root
   - **Senha:** root_password

Ou use o usuário da aplicação:
   - **Usuário:** biblioteca_user
   - **Senha:** biblioteca_pass

---

## 💾 Persistência de Dados

Os dados do MySQL são persistidos no volume Docker `mysql_data`. Mesmo que você pare os containers, os dados não serão perdidos, a menos que você execute `docker-compose down -v`.

---

## 🔐 Segurança

**IMPORTANTE:** As senhas fornecidas são para ambiente de desenvolvimento. Em produção, use senhas fortes e configure-as através de variáveis de ambiente.

Para produção, crie um arquivo `.env.docker`:
```env
MYSQL_ROOT_PASSWORD=senha_forte_aqui
MYSQL_PASSWORD=outra_senha_forte
```

E referencie no docker-compose.yml:
```yaml
env_file:
  - .env.docker
```
