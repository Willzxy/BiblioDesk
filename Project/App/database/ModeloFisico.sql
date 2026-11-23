CREATE DATABASE IF NOT EXISTS bd_bibliodesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_bibliodesk;

DROP TABLE IF EXISTS tb_emprestimos;
DROP TABLE IF EXISTS tb_livros;
DROP TABLE IF EXISTS tb_usuarios;

CREATE TABLE tb_livros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_isbn VARCHAR(30) NOT NULL UNIQUE,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(120) NOT NULL,
    categoria VARCHAR(60) NOT NULL,
    quantidade_total INT NOT NULL DEFAULT 1,
    quantidade_disponivel INT NOT NULL DEFAULT 1,
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tb_usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(120) NOT NULL,
    documento VARCHAR(30) NOT NULL UNIQUE,
    telefone VARCHAR(25) NOT NULL,
    observacao VARCHAR(255) DEFAULT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tb_emprestimos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    data_emprestimo DATE NOT NULL,
    data_prevista DATE NOT NULL,
    data_devolucao DATE DEFAULT NULL,
    status ENUM('ATIVO', 'DEVOLVIDO') NOT NULL DEFAULT 'ATIVO',
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id),
    FOREIGN KEY (id_livro) REFERENCES tb_livros(id)
);

INSERT INTO tb_livros (codigo_isbn, titulo, autor, categoria, quantidade_total, quantidade_disponivel) VALUES
('978-8535914849', 'Dom Casmurro', 'Machado de Assis', 'Literatura Brasileira', 4, 2),
('978-0132350884', 'Código Limpo', 'Robert C. Martin', 'Tecnologia', 3, 1),
('978-8595081512', 'O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 'Ficção', 5, 4),
('978-8535914840', '1984', 'George Orwell', 'Ficção Científica', 2, 1),
('978-8535914061', 'Capitães da Areia', 'Jorge Amado', 'Literatura Brasileira', 2, 0),
('978-8575225639', 'Entendendo Algoritmos', 'Aditya Y. Bhargava', 'Tecnologia', 3, 3),
('978-8532530783', 'Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 'Fantasia', 4, 3),
('978-8543102146', 'O Poder do Hábito', 'Charles Duhigg', 'Desenvolvimento Pessoal', 3, 3),
('978-8535920550', 'Sapiens: Uma Breve História da Humanidade', 'Yuval Noah Harari', 'História', 2, 1),
('978-8576082675', 'Arquitetura Limpa', 'Robert C. Martin', 'Tecnologia', 2, 2),
('978-8535909555', 'A Revolução dos Bichos', 'George Orwell', 'Ficção Política', 3, 3),
('978-8595084742', 'O Hobbit', 'J.R.R. Tolkien', 'Fantasia', 2, 2);

INSERT INTO tb_livros (codigo_isbn, titulo, autor, categoria, quantidade_total, quantidade_disponivel) VALUES
('978-8535911121', 'Memórias Póstumas de Brás Cubas', 'Machado de Assis', 'Literatura Brasileira', 3, 0),
('978-8535911671', 'Quincas Borba', 'Machado de Assis', 'Literatura Brasileira', 2, 2),
('978-8535914412', 'O Alienista', 'Machado de Assis', 'Literatura Brasileira', 4, 3),
('978-8550800653', 'O Codificador Limpo', 'Robert C. Martin', 'Tecnologia', 2, 2),
('978-8550804606', 'Desenvolvimento Ágil Limpo', 'Robert C. Martin', 'Tecnologia', 2, 1),
('978-8582603710', 'O Programador Pragmático', 'Andrew Hunt e David Thomas', 'Tecnologia', 3, 0),
('978-8576081739', 'Use a Cabeça! Padrões de Projetos', 'Eric Freeman', 'Tecnologia', 4, 3),
('978-8573076103', 'Padrões de Projetos: Soluções Reutilizáveis', 'Erich Gamma', 'Tecnologia', 2, 2),
('978-8575227268', 'Estruturas de Dados e Algoritmos com JavaScript', 'Loiane Groner', 'Tecnologia', 3, 3),
('978-8595086357', 'O Senhor dos Anéis: A Sociedade do Anel', 'J.R.R. Tolkien', 'Fantasia', 3, 0),
('978-8595086364', 'O Senhor dos Anéis: As Duas Torres', 'J.R.R. Tolkien', 'Fantasia', 3, 2),
('978-8595086371', 'O Senhor dos Anéis: O Retorno do Rei', 'J.R.R. Tolkien', 'Fantasia', 3, 3),
('978-8532530790', 'Harry Potter e a Câmara Secreta', 'J.K. Rowling', 'Fantasia', 4, 4),
('978-8532530806', 'Harry Potter e o Prisioneiro de Azkaban', 'J.K. Rowling', 'Fantasia', 4, 2),
('978-8576573135', 'Duna', 'Frank Herbert', 'Ficção Científica', 2, 0),
('978-8576570486', 'Fundação', 'Isaac Asimov', 'Ficção Científica', 3, 3),
('978-8576572008', 'Eu, Robô', 'Isaac Asimov', 'Ficção Científica', 2, 1),
('978-8595081536', 'E Não Sobrou Nenhum', 'Agatha Christie', 'Suspense', 3, 3),
('978-8595080638', 'Assassinato no Expresso do Oriente', 'Agatha Christie', 'Suspense', 2, 2),
('978-8581050485', 'O Iluminado', 'Stephen King', 'Terror', 2, 1),
('978-8535902778', 'Grande Sertão: Veredas', 'João Guimarães Rosa', 'Literatura Brasileira', 2, 2),
('978-8535914863', 'Vidas Secas', 'Graciliano Ramos', 'Literatura Brasileira', 3, 3),
('978-8535922837', 'Rápido e Devagar: Duas Formas de Pensar', 'Daniel Kahneman', 'Psicologia', 3, 2),
('978-8543104331', 'Mindset: A Nova Psicologia do Sucesso', 'Carol S. Dweck', 'Desenvolvimento Pessoal', 3, 3);

INSERT INTO tb_usuarios (nome, documento, telefone, observacao, ativo) VALUES
('Mariana Oliveira', '123.456.789-01', '(11) 99911-2233', 'Estudante de Engenharia', TRUE),
('Lucas Mendes', '234.567.890-12', '(11) 99822-3344', NULL, TRUE),
('Fernanda Costa', '345.678.901-23', '(11) 99733-4455', 'Prefere contato via WhatsApp', TRUE),
('Rafael Santos', '456.789.012-34', '(11) 99644-5566', NULL, TRUE),
('Juliana Almeida', '567.890.123-45', '(11) 99555-6677', 'Leitora frequente', TRUE),
('Gabriel Ferreira', '678.901.234-56', '(11) 99466-7788', 'Turma B - Noturno', TRUE),
('Camila Rodrigues', '789.012.345-67', '(11) 99377-8899', NULL, TRUE),
('Bruno Carvalho', '890.123.456-78', '(11) 99288-9900', 'Professor convidado', TRUE),
('Larissa Martins', '901.234.567-89', '(11) 99199-0011', 'Cadastro suspenso temporariamente', FALSE),
('Thiago Pereira', '012.345.678-90', '(11) 99010-1122', NULL, TRUE);

INSERT INTO tb_emprestimos (id_usuario, id_livro, data_emprestimo, data_prevista, data_devolucao, status) VALUES
(1, 1, DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_SUB(CURDATE(), INTERVAL 7 DAY), NULL, 'ATIVO'),
(2, 2, DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 4 DAY), NULL, 'ATIVO'),
(6, 7, DATE_SUB(CURDATE(), INTERVAL 9 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL, 'ATIVO'),
(3, 4, DATE_SUB(CURDATE(), INTERVAL 7 DAY), CURDATE(), NULL, 'ATIVO'),
(7, 9, DATE_SUB(CURDATE(), INTERVAL 7 DAY), CURDATE(), NULL, 'ATIVO'),
(4, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL, 'ATIVO'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), NULL, 'ATIVO'),
(5, 5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), NULL, 'ATIVO'),
(8, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), NULL, 'ATIVO'),
(10, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), NULL, 'ATIVO'),
(2, 3, DATE_SUB(CURDATE(), INTERVAL 22 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 16 DAY), 'DEVOLVIDO'),
(3, 6, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), 'DEVOLVIDO'),
(4, 1, DATE_SUB(CURDATE(), INTERVAL 18 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), 'DEVOLVIDO'),
(5, 8, DATE_SUB(CURDATE(), INTERVAL 16 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'DEVOLVIDO'),
(6, 10, DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'DEVOLVIDO'),
(8, 11, DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'DEVOLVIDO');