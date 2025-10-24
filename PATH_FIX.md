# ⚠️ IMPORTANTE - Variáveis de Ambiente

## 🔴 Situação Atual

Você executou os comandos `php -v` e `composer` no **CMD** (C:\Users\muril>), mas está tentando usá-los no **PowerShell** do projeto.

O PowerShell precisa ser **reiniciado** para reconhecer as variáveis de ambiente do PHP e Composer.

---

## ✅ SOLUÇÃO RÁPIDA

### Opção 1: Reiniciar PowerShell (RECOMENDADO)

1. **Feche** este PowerShell
2. **Abra um novo PowerShell**
3. Navegue até a pasta:
   ```powershell
   cd C:\Users\muril\Downloads\AgoraBiblioteca
   ```
4. Execute o script de setup:
   ```powershell
   .\setup.ps1
   ```

---

### Opção 2: Usar CMD ao invés de PowerShell

1. Abra o **Prompt de Comando (CMD)**
2. Navegue até a pasta:
   ```cmd
   cd C:\Users\muril\Downloads\AgoraBiblioteca
   ```
3. Execute os comandos manualmente:
   ```cmd
   composer install
   npm install
   php artisan key:generate
   php artisan migrate:fresh --seed
   php artisan storage:link
   npm run build
   php artisan serve
   ```

---

### Opção 3: Atualizar PATH no PowerShell Atual

Execute no PowerShell atual:

```powershell
# Adicionar PHP ao PATH da sessão atual
$env:Path += ";C:\php"

# Adicionar Composer ao PATH da sessão atual
$env:Path += ";C:\Users\muril\AppData\Roaming\Composer\vendor\bin"

# Ou onde o Composer estiver instalado
$env:Path += ";C:\ProgramData\ComposerSetup\bin"

# Testar
php -v
composer --version
```

---

## 🚀 Após Resolver o PATH

Execute um dos comandos abaixo:

### Instalação Automática
```powershell
.\setup.ps1
```

### Instalação Manual
```powershell
# 1. Instalar dependências
composer install
npm install

# 2. Gerar chave
php artisan key:generate

# 3. Criar banco e popular
php artisan migrate:fresh --seed

# 4. Link storage
php artisan storage:link

# 5. Compilar assets
npm run build

# 6. Iniciar servidor
php artisan serve
```

---

## 📍 Localizações Comuns do Composer

O Composer pode estar em:
- `C:\ProgramData\ComposerSetup\bin\composer.bat`
- `C:\Users\muril\AppData\Roaming\Composer\vendor\bin\composer.bat`
- `C:\php\composer.bat`

Para encontrar, execute no **CMD**:
```cmd
where composer
```

---

## ✅ Verificar se Está Funcionando

Após reiniciar o PowerShell ou atualizar o PATH, teste:

```powershell
php -v
composer --version
node --version
docker --version
```

Todos devem retornar suas versões.

---

## 🎯 STATUS ATUAL

- [x] Docker rodando (MySQL na porta 3306)
- [x] Banco de dados criado (biblioteca_digital)
- [x] PHP instalado (8.4.14)
- [x] Composer instalado (2.8.12)
- [ ] PATH atualizado no PowerShell (Pendente)
- [ ] Dependências instaladas (Próximo passo)

---

**🔄 AÇÃO NECESSÁRIA: Feche e reabra o PowerShell, depois execute `.\setup.ps1`**
