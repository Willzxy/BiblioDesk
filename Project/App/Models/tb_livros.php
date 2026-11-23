<?php

namespace App\Models;

require_once '../MinhaFramework/Models/Models.php';

use MF\Models;

class tb_livros extends Models
{
    public function contarTotalExemplares(): int
    {
        try {
            if (!$this->db) {
                return 0;
            }

            $resultado = $this->db->query("SELECT COALESCE(SUM(quantidade_total), 0) AS total FROM tb_livros");
            if ($resultado && $row = $resultado->fetch_assoc()) {
                return (int) $row['total'];
            }
        } catch (\Throwable $th) {
            return 0;
        }

        return 0;
    }

    public function listarTodos(string $busca = ''): array
    {
        $livros = [];

        try {
            if (!$this->db) {
                return $livros;
            }

            if ($busca !== '') {
                $termo = '%' . $busca . '%';
                $stmt = $this->db->prepare("SELECT * FROM tb_livros WHERE titulo LIKE ? OR autor LIKE ? OR codigo_isbn LIKE ? ORDER BY titulo ASC");
                $stmt->bind_param('sss', $termo, $termo, $termo);
                $stmt->execute();
                $resultado = $stmt->get_result();
            } else {
                $resultado = $this->db->query("SELECT * FROM tb_livros ORDER BY titulo ASC");
            }

            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $livros[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $livros;
        }

        return $livros;
    }

    public function listarDisponiveis(): array
    {
        $livros = [];

        try {
            if (!$this->db) {
                return $livros;
            }

            $resultado = $this->db->query("SELECT * FROM tb_livros WHERE quantidade_disponivel > 0 ORDER BY titulo ASC");
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $livros[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $livros;
        }

        return $livros;
    }

    public function buscarRapido(string $termo, int $limite = 8): array
    {
        $livros = [];

        try {
            if (!$this->db) {
                return $livros;
            }

            $termoLimpo = trim($termo);
            if (mb_strlen($termoLimpo) < 2) {
                return $livros;
            }

            $limiteSeguro = max(1, min(10, $limite));
            $like = '%' . $termoLimpo . '%';

            $stmt = $this->db->prepare(
                "SELECT id, codigo_isbn, titulo, autor, categoria, quantidade_total, quantidade_disponivel 
                 FROM tb_livros 
                 WHERE titulo LIKE ? OR autor LIKE ? OR codigo_isbn LIKE ? 
                 ORDER BY titulo ASC 
                 LIMIT ?"
            );
            $stmt->bind_param('sssi', $like, $like, $like, $limiteSeguro);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $livros[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $livros;
        }

        return $livros;
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            if (!$this->db) {
                return null;
            }

            $stmt = $this->db->prepare("SELECT * FROM tb_livros WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado && $row = $resultado->fetch_assoc()) {
                return $row;
            }
        } catch (\Throwable $th) {
            return null;
        }

        return null;
    }

    public function cadastrar(array $dados): bool
    {
        try {
            if (!$this->db) {
                return false;
            }

            $codigoIsbn = trim($dados['codigo_isbn'] ?? '');
            $titulo = trim($dados['titulo'] ?? '');
            $autor = trim($dados['autor'] ?? '');
            $categoria = trim($dados['categoria'] ?? '');
            $quantidadeTotal = max(1, (int) ($dados['quantidade_total'] ?? 1));
            $quantidadeDisponivel = $quantidadeTotal;

            $stmt = $this->db->prepare(
                "INSERT INTO tb_livros (codigo_isbn, titulo, autor, categoria, quantidade_total, quantidade_disponivel) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('ssssii', $codigoIsbn, $titulo, $autor, $categoria, $quantidadeTotal, $quantidadeDisponivel);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function atualizar(int $id, array $dados): bool
    {
        try {
            if (!$this->db) {
                return false;
            }

            $codigoIsbn = trim($dados['codigo_isbn'] ?? '');
            $titulo = trim($dados['titulo'] ?? '');
            $autor = trim($dados['autor'] ?? '');
            $categoria = trim($dados['categoria'] ?? '');
            $quantidadeTotal = max(1, (int) ($dados['quantidade_total'] ?? 1));
            $quantidadeDisponivel = max(0, min($quantidadeTotal, (int) ($dados['quantidade_disponivel'] ?? $quantidadeTotal)));

            $stmt = $this->db->prepare(
                "UPDATE tb_livros SET codigo_isbn = ?, titulo = ?, autor = ?, categoria = ?, quantidade_total = ?, quantidade_disponivel = ? WHERE id = ?"
            );
            $stmt->bind_param('ssssiii', $codigoIsbn, $titulo, $autor, $categoria, $quantidadeTotal, $quantidadeDisponivel, $id);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        if (!$this->db) {
            return false;
        }

        $this->db->begin_transaction();

        try {
            $stmtAtivos = $this->db->prepare("SELECT COUNT(*) AS total FROM tb_emprestimos WHERE id_livro = ? AND status = 'ATIVO'");
            $stmtAtivos->bind_param('i', $id);
            $stmtAtivos->execute();
            $resultado = $stmtAtivos->get_result();
            $row = $resultado ? $resultado->fetch_assoc() : null;

            if ($row && (int) $row['total'] > 0) {
                $this->db->rollback();
                return false;
            }

            $stmtHistorico = $this->db->prepare("DELETE FROM tb_emprestimos WHERE id_livro = ? AND status = 'DEVOLVIDO'");
            $stmtHistorico->bind_param('i', $id);
            if (!$stmtHistorico->execute()) {
                $this->db->rollback();
                return false;
            }

            $stmtLivro = $this->db->prepare("DELETE FROM tb_livros WHERE id = ?");
            $stmtLivro->bind_param('i', $id);
            if (!$stmtLivro->execute() || $stmtLivro->affected_rows === 0) {
                $this->db->rollback();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $th) {
            $this->db->rollback();
            return false;
        }
    }

    public function alterarDisponibilidade(int $id, int $delta): bool
    {
        try {
            if (!$this->db) {
                return false;
            }

            $stmt = $this->db->prepare(
                "UPDATE tb_livros SET quantidade_disponivel = quantidade_disponivel + ? WHERE id = ? AND (quantidade_disponivel + ?) >= 0"
            );
            $stmt->bind_param('iii', $delta, $id, $delta);

            return $stmt->execute() && $stmt->affected_rows > 0;
        } catch (\Throwable $th) {
            return false;
        }
    }
}