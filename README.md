# LibroVault - Sistema de Indexação de Livros
<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
<a href="https://vuejs.org" target="_blank"><img src="https://vuejs.org/images/logo.png" width="150" alt="Vue.js Logo"></a>
</p>

# 📚 LibroVault

LibroVault é um sistema de **estante virtual de livros** que permite armazenar, visualizar e pesquisar conteúdos utilizando **busca semântica baseada em embeddings**.

---

## 🚀 Tecnologias Utilizadas

- **Backend:** Laravel 11.31 (PHP 8.2)
- **Frontend:** Vue 3 (pronto, mas não detalhado aqui)
- **Banco de Dados:** MySQL
- **Processamento Semântico:** Microsserviço Python com `sentence-transformers`
- **Filas e Jobs:** Laravel Queues (para processamento assíncrono de PDFs)
- **Extração de Texto de PDFs:** `smalot/pdfparser`
- **Testes Automatizados:** PEST

---

## 📌 Funcionalidades

✅ **Gerenciamento de Livros**: Cadastro, edição, exclusão e visualização de livros.  
✅ **Upload de PDFs**: Associar um arquivo PDF a um livro para posterior processamento.  
✅ **Processamento Assíncrono**: O PDF é dividido em páginas, enviadas ao microsserviço Python para extração de embeddings.  
✅ **Busca Semântica**: O usuário faz uma pergunta e recebe as páginas mais relevantes ranqueadas por similaridade.  
✅ **Armazenamento de Embeddings**: Cada página tem um JSON de embedding armazenado localmente.  
✅ **Sistema de Permissões**:
   - `admin`: Acesso total.
   - `standard`: Gerencia apenas seus próprios livros.
   - `plus`: Pode visualizar livros de todos, mas gerencia apenas os próprios.  

✅ **Fila de Processamento**: O Laravel gerencia as operações pesadas via queues.  

---

## ⚙️ Configuração do Projeto

### 📌 1️⃣ Requisitos

- PHP 8.2+
- Composer
- MySQL
- Node.js (para o Vue, caso necessário)
- Microsserviço Python em execução (`API_SEMANTIC_URL`)

### 📌 2️⃣ Clonar o Repositório

```sh
git clone https://github.com/seu-usuario/librovault.git
cd librovault
```

### 📌 3️⃣ Instalar Dependências

```sh
composer install
npm install
```

### 📌 4️⃣ Configurar o Banco de Dados
Edite o arquivo `.env` com as credenciais do seu banco de dados.

```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=librovault
DB_USERNAME=root
DB_PASSWORD=rootpass
```

### 📌 5️⃣ Rodar as Migrations

```sh
php artisan migrate
```

### 📌 6️⃣ Gerar Chave da Aplicação

```sh
php artisan key:generate
```

### 📌 7️⃣ Criar link para o storage

```sh
php artisan storage:link
```

### 📌 7️⃣ Configurar o Microsserviço Python
O .env já contém um valor padrão para a API semântica:

```sh
API_SEMANTIC_URL=http://127.0.0.1:5000/
API_SEMANTIC_TIMEOUT=60
```
Certifique-se de que o [microsserviço Python](https://github.com/Hugobsan/LibroVault-Python) está rodando nessa URL.

### 📌 8️⃣ Rodar o Projeto

```sh
php artisan serve
```

```sh
php artisan queue:work
```

```sh
npm run dev # ou npm run build
```
---

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

