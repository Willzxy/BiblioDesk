# BiblioDesk

Sistema web para controle interno de pequenas bibliotecas, permitindo o gerenciamento de catálogo de livros, cadastro de leitores, registro de empréstimos, devoluções e histórico.

> **Projeto Universitário** desenvolvido em conjunto para as disciplinas de **Arquitetura de Projetos** e **Programação Orientada a Objetos II**.

---

## Tecnologias Utilizadas

* **Back-end:** PHP (arquitetura MVC customizada)
* **Banco de Dados:** MySQL (`mysqli`)
* **Front-end:** HTML5, Bootstrap 5 e Tailwind CSS

---

## Como Executar o Projeto

### 1. Pré-requisitos

* PHP 8.0 ou superior instalado (com a extensão `mysqli` habilitada)
* Servidor MySQL ou MariaDB em execução

### 2. Configuração do Banco de Dados

1. Acesse a pasta `Project/App/database/` e baixe/execute o arquivo `script.sql` no seu gerenciador MySQL para criar o banco de dados (`bd_bibliodesk`), as tabelas e os registros iniciais.
2. Abra o arquivo `Project/App/database/Connection.php` e atualize os dados de conexão para o ambiente da sua máquina:
   * `$host` (ex.: `localhost:3306`)
   * `$bd_name` (`bd_bibliodesk`)
   * `$usuario` (usuário do seu MySQL)
   * `$senha` (senha do seu MySQL)

### 3. Iniciando o Servidor Local

Pelo terminal, entre na pasta `Project` e inicie o servidor embutido do PHP apontando a raiz para a pasta `public`:

```bash
cd Project
php -S localhost:8080 -t public