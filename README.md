# menu.linksbio.me
### Sistema de Cardápio Digital

Sistema completo de cardápio digital para lanchonetes com foco mobile, desenvolvido em PHP usando Slim Framework 4.

## 📱 Características

- **Design Mobile First** - Interface otimizada para dispositivos móveis
- **Personalização de Açaí** - Sistema estilo iFood para escolher tamanhos e ingredientes
- **Painel Administrativo** - CRUD completo para produtos, categorias e ingredientes
- **Responsivo** - Utiliza TailwindCSS para interface moderna
- **Arquitetura Limpa** - Código organizado e orientado a objetos

## 🛠️ Tecnologias

- **Backend**: PHP 8.0+ com Slim Framework 4
- **Banco de Dados**: MySQL 8.0+
- **Frontend**: HTML5 + TailwindCSS + JavaScript Vanilla
- **Dependências**: Composer para gerenciamento de pacotes

## ⚙️ Instalação

### 1. Pré-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Composer
- Servidor web (Apache/Nginx)

### 2. Clone o projeto

```bash
git clone <url-do-repositorio>
cd menu.linksbio.me
```

### 3. Instale as dependências

```bash
composer install
```

### 4. Configure o banco de dados

1. Crie um banco de dados MySQL ou PostgreSQL:
```sql
CREATE DATABASE db_menu_digital;
```

2. Execute o script de criação das tabelas:
```bash
mysql -u root -p acaiteria_cardapio < database/setup.sql
```

### 5. Configure as variáveis de ambiente

1. Copie o arquivo de exemplo:
```bash
copy .env.example .env
```

2. Edite o arquivo `.env` com suas configurações e adicione mais variaveis se necessário:
```
DB_HOST=localhost
DB_NAME=acaiteria_cardapio
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
APP_ENV=development
```

### 6. Configure o servidor web

#### Apache
Certifique-se de que o mod_rewrite está habilitado e aponte o DocumentRoot para a pasta `public/`.

Exemplo de VirtualHost:
```apache
<VirtualHost *:80>
    ServerName acaiteria.local
    DocumentRoot /caminho/para/cardapio_base/public
    
    <Directory /caminho/para/cardapio_base/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name acaiteria.local;
    root /caminho/para/cardapio_base/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. Teste a instalação

1. Acesse o cardápio público (domínio exemplo): `http://acaiteria.local/`
2. Acesse o painel admin: `http://acaiteria.local/admin`

## 🎯 Funcionalidades

### Cardápio Público (`/`)
- Lista todos os produtos por categoria
- Filtros por categoria
- Modal de personalização para açaís
- Cálculo automático de preços
- Interface mobile responsiva

### Painel Administrativo (`/admin`)
- **Dashboard** com estatísticas gerais
- **Produtos** - CRUD completo com configurações específicas para açaí
- **Categorias** - Visualização das categorias
- **Ingredientes** - Visualização dos ingredientes disponíveis

## Estrutura do Banco

### Tabelas Principais

- **categories** - Categorias dos produtos
- **products** - Produtos do cardápio com configurações especiais para açaí
- **ingredients** - Ingredientes disponíveis para personalização

### Campos Especiais para Açaí

- `size_ml` - Tamanho do açaí em mililitros
- `max_ingredients` - Limite de ingredientes por açaí
- `size_order` - Ordem de apresentação dos tamanhos

## 🔧 Personalização

### Adicionando Novos Tipos de Ingredientes

1. Insira novos ingredientes no banco:
```sql
INSERT INTO ingredients (name, type, additional_price) 
VALUES ('Novo Ingrediente', 'novo_tipo', 2.50);
```

2. O sistema agrupará automaticamente por tipo no modal de personalização.

### Configurando Novos Tamanhos de Açaí

1. Adicione um novo produto de açaí:
```sql
INSERT INTO products (name, description, price, category_id, size_ml, max_ingredients, size_order) 
VALUES ('Açaí Gigante', 'Para os famintos!', 35.00, 1, 1500, 15, 5);
```



##### 📁 Estrutura 

```
menu.linksbio.me/
├── config/              # Configurações da aplicação
├── database/            # Scripts SQL
├── public/              # Ponto de entrada web
├── src/                 # Código fonte PHP
│   ├── Controllers/     # Controladores
│   ├── Models/          # Modelos de dados
│   └── Services/        # Serviços auxiliares
├── templates/           # Templates PHP
│   ├── admin/           # Templates do painel admin
│   └── menu/            # Templates do cardápio público
└── vendor/              # Dependências do Composer
```


**Desenvolvido por [Júnior Santos](https://linksbio.me/juniorsantos)**
