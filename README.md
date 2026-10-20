# 📦 Sistema de Controle de Estoque — Arquitetura MVC

Sistema básico de controle de estoque desenvolvido em **PHP vanilla** (sem frameworks) com **MySQL**, organizado segundo a arquitetura **MVC (Model–View–Controller)**. Inclui front controller, roteador de URLs amigáveis e autoloader PSR-4.

---

## ✨ Funcionalidades

- **Tela de login** com autenticação por sessão; todas as rotas de produtos são protegidas.
- **Autocadastro público**: link "Cadastre-se" na tela de login permite criar uma conta; após o cadastro o usuário é enviado ao login.
- **Estoque por usuário**: cada usuário enxerga e gerencia **apenas o seu próprio estoque** (os produtos são vinculados ao usuário que os cadastrou).
- **Gestão de usuários**: cadastro, listagem e exclusão de usuários (área autenticada).
- **Cadastro de produtos** com nome, preço e imagem (PNG ou JPG).
- **Controle de perecibilidade**: ao marcar "produto perecível", o campo **data de validade** é habilitado automaticamente e passa a ser obrigatório.
- **Filtros por situação**: quatro botões — **Tudo**, **Dentro da validade**, **Fora da validade** e **Não perecíveis** — cada um com a contagem de itens.
- **Busca** de produtos pelo nome e **ordenação** por nome, preço ou data de validade.
- **Painel de estatísticas**: total de produtos, dentro da validade, fora da validade e valor total em estoque.
- **Destaque visual** para produtos com validade vencida.
- **Edição e exclusão** (a exclusão remove também a imagem do servidor).
- **Layout responsivo** — a tabela vira cartões empilhados em telas pequenas.
- **Acentuação correta** em toda a interface (pt-BR, `utf8mb4`).

---

## 🧰 Requisitos

- **XAMPP** (ou stack equivalente: Apache + PHP 7.4+ + MySQL/MariaDB).
- Extensão **PDO MySQL** (já habilitada por padrão no XAMPP).
- **mod_rewrite** habilitado no Apache (também já vem habilitado no XAMPP) para as URLs amigáveis.

---

## 🚀 Como instalar e rodar (XAMPP)

### 1. Copie o projeto para o htdocs

- **Windows:** `C:\xampp\htdocs\controle-estoque`
- **Linux:** `/opt/lampp/htdocs/controle-estoque`
- **macOS:** `/Applications/XAMPP/htdocs/controle-estoque`

### 2. Inicie o Apache e o MySQL

Abra o **XAMPP Control Panel** e clique em **Start** em **Apache** e **MySQL**.

### 3. Importe o banco de dados

1. Acesse o **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Clique em **Importar**.
3. Escolha o arquivo **`database/data.sql`**.
4. Clique em **Executar**.

O `data.sql` cria automaticamente o banco `controle_estoque`, as tabelas `usuarios` e `produtos`, e o usuário padrão `admin`. Não é preciso criar nada manualmente.

> ⚠️ **Atenção:** o `data.sql` contém `DROP TABLE IF EXISTS` para garantir que o schema fique sempre correto. Ao reimportá-lo, **os dados existentes (usuários e produtos) são apagados**. Isso é proposital durante o desenvolvimento.

### 4. Acesse a aplicação

```
http://localhost/controle-estoque/
```

A aplicação abre na **tela de login**. Use as credenciais padrão:

| Usuário | Senha |
|---------|-------|
| `admin` | `123` |

> Após o login você é redirecionado para a listagem de produtos.

> O roteamento amigável é feito pelo `.htaccess` na raiz. Se você tiver problemas, confirme que o `mod_rewrite` está ativo e que o Apache permite `AllowOverride All` para o diretório do `htdocs` (configuração padrão do XAMPP).

---

## 🏗️ Arquitetura MVC

O projeto segue o fluxo clássico de uma aplicação MVC com front controller:

```
Requisição HTTP
      │
      ▼
index.php  (Front Controller — único ponto de entrada)
      │  resolve a URI e consulta o roteador
      ▼
Router  ──►  Controller (ProdutoController)
                    │  regras de negócio e validação
        ┌───────────┼─────────────┐
        ▼           ▼             ▼
      Model      Service        View
   (Produto)  (ImagemService)  (produtos/*.php)
   acesso a      upload de      camada de
    dados         imagens       apresentação
```

- **Model (`app/Models`)** — acesso e persistência de dados (PDO com prepared statements).
- **View (`app/Views`)** — apenas apresentação (HTML + PHP de template). Não contém regra de negócio.
- **Controller (`app/Controllers`)** — recebe a requisição, valida dados, aciona Model/Serviços e escolhe a View.
- **Core (`app/Core`)** — infraestrutura reutilizável: `Controller` base, `Model` base, `Router` e `helpers`.
- **Services (`app/Services`)** — lógica auxiliar isolada (ex.: upload de imagens).
- **Config (`app/Config`)** — configuração e conexão com o banco (`Database`, singleton PDO).

### Estrutura de pastas

```
controle-estoque/
├── index.php                      # Front Controller (único arquivo acessível)
├── .htaccess                      # Roteamento de URLs amigáveis
├── app/
│   ├── Config/
│   │   ├── config.php             # Constantes (conexão, upload)
│   │   └── Database.php           # Conexão PDO (Singleton)
│   ├── Core/
│   │   ├── Auth.php               # Autenticação (sessão, login/logout)
│   │   ├── Controller.php         # Controller base (render / redirect / exigirLogin)
│   │   ├── Model.php              # Model base (conexão)
│   │   ├── Router.php             # Roteador de URLs
│   │   └── helpers.php            # Funções globais (e, url, asset, ...)
│   ├── Controllers/
│   │   ├── AuthController.php     # Login e logout
│   │   ├── ProdutoController.php  # Ações dos produtos
│   │   └── UsuarioController.php  # Gestão de usuários
│   ├── Models/
│   │   ├── Produto.php            # Acesso à tabela "produtos"
│   │   └── Usuario.php            # Acesso à tabela "usuarios"
│   ├── Services/
│   │   ├── ImagemService.php      # Upload/remoção de imagens
│   │   └── UsuarioService.php     # Validação e cadastro de usuários
│   └── Views/
│       ├── auth/
│       │   ├── login.php          # Formulário de login
│       │   └── registrar.php      # Autocadastro (criar conta)
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── produtos/
│       │   ├── index.php          # Listagem + filtros + estatísticas
│       │   └── form.php           # Formulário (cadastro/edição)
│       ├── usuarios/
│       │   ├── index.php          # Listagem de usuários
│       │   └── form.php           # Cadastro de usuário
│       └── erros/
│           └── 404.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── uploads/                       # Imagens enviadas dos produtos
├── database/
│   └── data.sql                   # Criação do banco e da tabela
└── README.md
```

### Rotas disponíveis

| Método | Rota                       | Ação                              |
|--------|----------------------------|-----------------------------------|
| GET    | `/login`                   | Formulário de login (público)     |
| POST   | `/login`                   | Valida as credenciais             |
| GET    | `/registrar`               | Formulário de autocadastro (público) |
| POST   | `/registrar`               | Cria a conta e já autentica       |
| GET    | `/logout`                  | Encerra a sessão                  |
| GET    | `/`                        | Listagem de produtos              |
| GET    | `/produtos`                | Listagem de produtos              |
| GET    | `/produtos/criar`          | Formulário de cadastro            |
| POST   | `/produtos/criar`          | Grava um novo produto             |
| GET    | `/produtos/editar/{id}`    | Formulário de edição              |
| POST   | `/produtos/editar/{id}`    | Atualiza um produto               |
| GET    | `/produtos/excluir/{id}`   | Remove um produto                 |
| GET    | `/usuarios`                | Lista os usuários                 |
| GET    | `/usuarios/criar`          | Formulário de novo usuário        |
| POST   | `/usuarios/criar`          | Grava um novo usuário             |
| GET    | `/usuarios/excluir/{id}`   | Remove um usuário                 |

> As rotas de produtos e de usuários exigem login. O `Controller` base oferece o método `exigirLogin()`, chamado no construtor dos controllers protegidos, que redireciona para `/login` quando não há sessão ativa.

---

## 🔐 Autenticação

O controle de acesso fica em `app/Core/Auth.php` e usa **sessões PHP** (`$_SESSION`):

- `Auth::iniciar()` — inicia a sessão (chamado no front controller).
- `Auth::autenticado()` — verifica se há usuário logado.
- `Auth::entrar($usuario)` — registra o usuário na sessão (renovando o ID da sessão).
- `Auth::sair()` — encerra a sessão (logout).

As senhas **nunca são gravadas em texto puro**: o `data.sql` insere um **hash bcrypt** e a verificação usa `password_verify()`, conforme boas práticas do PHP. Novos usuários cadastrados pela tela **Usuários** também têm a senha convertida em hash (`password_hash`).

### Gestão de usuários

Há duas formas de criar usuários:

1. **Autocadastro (público):** na tela de login, o link **"Cadastre-se"** leva ao formulário de criação de conta. Após o cadastro, o usuário é **redirecionado para o login** com uma mensagem de sucesso (o login não é automático).
2. **Gestão interna:** com o usuário logado, o menu **Usuários** permite listar, cadastrar e excluir usuários.

A validação e o cadastro ficam centralizados em `app/Services/UsuarioService.php`, reutilizado pelos dois fluxos.

**Validações aplicadas:**

- Nome e login obrigatórios; login com no mínimo 3 caracteres e **único**;
- Senha obrigatória, mínimo de 3 caracteres, com **confirmação** obrigatória;
- **Proteções na exclusão**: não é possível excluir o próprio usuário logado nem o último usuário do sistema.

> O usuário `admin` (criado pelo `data.sql`) serve como acesso inicial. Novos usuários podem ser criados pelo autocadastro ou pela gestão interna.

### Credenciais padrão

| Usuário | Senha |
|---------|-------|
| `admin` | `123` |

### Como trocar a senha

> ⚠️ **Aviso:** `admin` / `123` é uma senha propositalmente fraca, indicada apenas para estudo local. Em qualquer uso real, troque-a.

Para gerar um novo hash, crie um arquivo temporário com:

```php
<?php echo password_hash('sua_nova_senha', PASSWORD_DEFAULT);
```

Acesse-o pelo navegador, copie o hash gerado e atualize o registro na tabela `usuarios`:

```sql
UPDATE usuarios SET senha = 'COLE_O_HASH_AQUI' WHERE login = 'admin';
```

---

## 🔌 Configuração da string de conexão

A conexão fica no arquivo **`app/Config/config.php`**. Ajuste as constantes conforme o seu ambiente:

```php
define('DB_HOST', 'localhost');        // Host do MySQL
define('DB_PORT', '3306');             // Porta do MySQL
define('DB_NAME', 'controle_estoque'); // Nome do banco (criado pelo data.sql)
define('DB_USER', 'root');             // Usuário do MySQL
define('DB_PASS', '');                 // Senha do MySQL
define('DB_CHARSET', 'utf8mb4');       // Codificação (suporta acentuação)
```

### Valores padrão do XAMPP

| Constante   | Valor padrão        |
|-------------|---------------------|
| `DB_HOST`   | `localhost`         |
| `DB_PORT`   | `3306`              |
| `DB_NAME`   | `controle_estoque`  |
| `DB_USER`   | `root`              |
| `DB_PASS`   | *(vazia)*           |

Se o seu MySQL tiver senha, altere apenas `DB_PASS`:

```php
define('DB_PASS', 'sua_senha_aqui');
```

### Como a conexão é usada

A classe **`App\Config\Database`** monta a DSN e cria uma única conexão PDO (padrão Singleton), compartilhada por todos os Models:

```php
$dsn = "mysql:host=localhost;port=3306;dbname=controle_estoque;charset=utf8mb4";
$pdo = new PDO($dsn, 'root', '', $opcoes);
```

O `charset=utf8mb4` garante o armazenamento e a exibição corretos de acentos e caracteres especiais.

---

## 🗄️ Estrutura da tabela `produtos`

| Coluna          | Tipo           | Descrição                                            |
|-----------------|----------------|------------------------------------------------------|
| `id`            | INT (PK, AI)   | Identificador único do produto                       |
| `usuario_id`    | INT (FK)       | Dono do produto (referencia `usuarios.id`)           |
| `nome`          | VARCHAR(150)   | Nome do produto                                      |
| `preco`         | DECIMAL(10,2)  | Preço em reais                                       |
| `imagem`        | VARCHAR(255)   | Nome do arquivo da imagem (salvo em `uploads/`)      |
| `perecivel`     | TINYINT(1)     | `1` = perecível, `0` = não perecível                 |
| `data_validade` | DATE           | Data de validade (apenas para perecíveis)            |
| `criado_em`     | TIMESTAMP      | Data/hora de criação (automática)                    |
| `atualizado_em` | TIMESTAMP      | Data/hora da última alteração (automática)           |

> A chave estrangeira `usuario_id` usa `ON DELETE CASCADE`: ao excluir um usuário, seus produtos também são removidos.

## 🔒 Isolamento do estoque por usuário

Cada produto pertence a um usuário (`usuario_id`) e **todas** as consultas do `Produto` filtram pelo usuário autenticado. Na prática:

- Ao logar, o usuário vê apenas os produtos que ele mesmo cadastrou (estoque começa vazio);
- Editar, excluir ou acessar por URL um produto de outro usuário retorna "Produto não encontrado";
- O painel de estatísticas também considera apenas o estoque do usuário logado.

O `ProdutoController` obtém o ID do usuário via `Auth::usuario()` e o repassa ao Model em cada operação.

---

## 📌 Observações importantes

- **Imagens:** apenas **PNG** e **JPG**, com no máximo **2 MB**. O tipo real é validado com `getimagesize` (não só a extensão).
- **Segurança:** consultas com **PDO + prepared statements** (anti SQL injection) e saída escapada com `htmlspecialchars` via helper `e()` (anti XSS). As pastas `app/` e `database/` são bloqueadas para acesso direto por `.htaccess`.
- **Permissão da pasta `uploads/`:** em servidores Linux pode ser necessário `chmod 775 uploads`.
- **Produtos não perecíveis:** o campo de validade é ignorado e gravado como `NULL`.

---

## 🧪 Fluxo de uso

1. Acesse a listagem em `/`.
2. Use os botões de filtro:
   - **Tudo** — todos os produtos.
   - **Dentro da validade** — perecíveis com validade hoje ou futura.
   - **Fora da validade** — perecíveis já vencidos.
   - **Não perecíveis** — produtos sem data de validade.
3. Clique em **+ Novo produto** para cadastrar.
4. Marque **"Este produto é perecível"** para liberar o campo de validade.
5. Envie uma imagem (opcional), no formato PNG ou JPG.
6. Use **Buscar**, **Editar** e **Excluir** para gerenciar os itens.

> Os filtros podem ser combinados com a busca por nome e com a ordenação.

---

## 📄 Licença

Projeto de uso livre para estudos e aprendizado.
