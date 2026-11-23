# BiblioDesk

## Tecnologias Utilizadas

* **Back-end:** PHP (arquitetura MVC customizada)
* **Banco de Dados:** MySQL (`mysqli`)
* **Front-end:** HTML5, Bootstrap 5, Tailwind CSS e JavaScript (`Fetch API`)

---

## Padrões de Projeto Aplicados

### 1. Singleton

* **Nome/Propósito:** **Singleton** — Garantir que a classe de conexão com o banco de dados (`Connection`) possua apenas uma única instância ativa por requisição e fornecer um ponto global de acesso a ela (`getInstance()`).
* **Motivação (Problema/Solução real):**
  * **Problema:** Cada model instanciado (`tb_livros`, `tb_usuarios`, `tb_emprestimos`) abria uma nova conexão `mysqli` separada com o MySQL — no Dashboard, por exemplo, três conexões simultâneas eram abertas na mesma requisição.
  * **Solução:** O construtor de `Connection` foi tornado privado (bloqueando clonagem e desserialização) e a classe base `Models` passou a reutilizar a mesma conexão via `Connection::getInstance()->getDB()`.

### 2. Factory Method

* **Nome/Propósito:** **Factory Method** — Definir uma interface abstrata para a criação de objetos (`ModelFactory::criarModel()`), delegando às subclasses concretas a decisão de qual model instanciar.
* **Motivação (Problema/Solução real):**
  * **Problema:** Os controllers estavam diretamente acoplados às classes concretas das tabelas, executando `require_once` e `new` em cada ação, o que dificultava manutenção e evolução do sistema.
  * **Solução:** A criação dos models foi encapsulada nas fábricas concretas (`LivroModelFactory`, `UsuarioModelFactory` e `EmprestimoModelFactory`), desacoplando os controllers da instanciação direta das classes de persistência.

### 3. Observer

* **Nome/Propósito:** **Observer** — Definir uma dependência um-para-muitos entre objetos para que, quando um evento ocorrer em `EmprestimoSubject`, todos os observadores registrados sejam notificados automaticamente.
* **Motivação (Problema/Solução real):**
  * **Problema:** Ao registrar um empréstimo ou devolução, o sistema precisa disparar ações paralelas — registrar histórico de auditoria em arquivo de log e alertar o bibliotecário quando um livro atinge estoque zerado — sem poluir ou acoplar essas regras no model `tb_emprestimos`.
  * **Solução:** O `EmprestimoSubject` notifica automaticamente o `AuditoriaLogObserver` (que grava os eventos em `auditoria.log`) e o `EstoqueAlertaObserver` (que exibe alerta ao zerar exemplares disponíveis) nos eventos `EMPRESTIMO_REGISTRADO` e `DEVOLUCAO_REGISTRADA`.
