<?php

namespace App\Models;

require_once '../MinhaFramework/Models/Models.php';

use MF\Models;

class tb_emprestimos extends Models
{
    public function contarAtivos(): int
    {
        try {
            if (!$this->db) {
                return 0;
            }

            $resultado = $this->db->query("SELECT COUNT(*) AS total FROM tb_emprestimos WHERE status = 'ATIVO'");
            if ($resultado && $row = $resultado->fetch_assoc()) {
                return (int) $row['total'];
            }
        } catch (\Throwable $th) {
            return 0;
        }

        return 0;
    }

    public function contarAtrasados(): int
    {
        try {
            if (!$this->db) {
                return 0;
            }

            $resultado = $this->db->query("SELECT COUNT(*) AS total FROM tb_emprestimos WHERE status = 'ATIVO' AND data_prevista < CURDATE()");
            if ($resultado && $row = $resultado->fetch_assoc()) {
                return (int) $row['total'];
            }
        } catch (\Throwable $th) {
            return 0;
        }

        return 0;
    }

    public function listarPendencias(): array
    {
        $pendencias = [];

        try {
            if (!$this->db) {
                return $pendencias;
            }

            $sql = "SELECT 
                        e.id,
                        u.nome AS usuario_nome,
                        l.titulo AS livro_titulo,
                        e.data_prevista,
                        DATEDIFF(CURDATE(), e.data_prevista) AS dias_atraso
                    FROM tb_emprestimos e
                    INNER JOIN tb_usuarios u ON u.id = e.id_usuario
                    INNER JOIN tb_livros l ON l.id = e.id_livro
                    WHERE e.status = 'ATIVO' AND e.data_prevista <= CURDATE()
                    ORDER BY e.data_prevista ASC
                    LIMIT 8";

            $resultado = $this->db->query($sql);
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $pendencias[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $pendencias;
        }

        return $pendencias;
    }

    public function listarAtivos(bool $somenteAtrasados = false, string $busca = ''): array
    {
        $emprestimos = [];

        try {
            if (!$this->db) {
                return $emprestimos;
            }

            $sql = "SELECT 
                        e.id,
                        e.id_usuario,
                        e.id_livro,
                        u.nome AS usuario_nome,
                        u.documento AS usuario_documento,
                        l.titulo AS livro_titulo,
                        l.codigo_isbn AS livro_codigo,
                        e.data_emprestimo,
                        e.data_prevista,
                        DATEDIFF(CURDATE(), e.data_prevista) AS dias_atraso
                    FROM tb_emprestimos e
                    INNER JOIN tb_usuarios u ON u.id = e.id_usuario
                    INNER JOIN tb_livros l ON l.id = e.id_livro
                    WHERE e.status = 'ATIVO'";

            if ($somenteAtrasados) {
                $sql .= " AND e.data_prevista < CURDATE()";
            }

            if ($busca !== '') {
                $termo = '%' . $busca . '%';
                $sql .= " AND (u.nome LIKE ? OR l.titulo LIKE ?)";
                $sql .= " ORDER BY e.data_prevista ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('ss', $termo, $termo);
                $stmt->execute();
                $resultado = $stmt->get_result();
            } else {
                $sql .= " ORDER BY e.data_prevista ASC";
                $resultado = $this->db->query($sql);
            }

            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $emprestimos[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $emprestimos;
        }

        return $emprestimos;
    }

    public function listarHistorico(string $filtro = 'TODOS', string $busca = ''): array
    {
        $historico = [];

        try {
            if (!$this->db) {
                return $historico;
            }

            $sql = "SELECT 
                        e.id,
                        u.nome AS usuario_nome,
                        l.titulo AS livro_titulo,
                        e.data_emprestimo,
                        e.data_prevista,
                        e.data_devolucao,
                        DATEDIFF(e.data_devolucao, e.data_prevista) AS dias_diferenca
                    FROM tb_emprestimos e
                    INNER JOIN tb_usuarios u ON u.id = e.id_usuario
                    INNER JOIN tb_livros l ON l.id = e.id_livro
                    WHERE e.status = 'DEVOLVIDO'";

            if ($filtro === 'NO_PRAZO') {
                $sql .= " AND e.data_devolucao <= e.data_prevista";
            } elseif ($filtro === 'COM_ATRASO') {
                $sql .= " AND e.data_devolucao > e.data_prevista";
            }

            if ($busca !== '') {
                $termo = '%' . $busca . '%';
                $sql .= " AND (u.nome LIKE ? OR l.titulo LIKE ?)";
                $sql .= " ORDER BY e.data_devolucao DESC, e.id DESC";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('ss', $termo, $termo);
                $stmt->execute();
                $resultado = $stmt->get_result();
            } else {
                $sql .= " ORDER BY e.data_devolucao DESC, e.id DESC";
                $resultado = $this->db->query($sql);
            }

            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $historico[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $historico;
        }

        return $historico;
    }

    public function registrar(int $idUsuario, int $idLivro, string $dataEmprestimo, string $dataPrevista): bool
    {
        if (!$this->db) {
            return false;
        }

        $this->db->begin_transaction();

        try {
            $stmtEstoque = $this->db->prepare("UPDATE tb_livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ? AND quantidade_disponivel > 0");
            $stmtEstoque->bind_param('i', $idLivro);
            $stmtEstoque->execute();

            if ($stmtEstoque->affected_rows === 0) {
                $this->db->rollback();
                return false;
            }

            $stmtEmprestimo = $this->db->prepare(
                "INSERT INTO tb_emprestimos (id_usuario, id_livro, data_emprestimo, data_prevista, status) VALUES (?, ?, ?, ?, 'ATIVO')"
            );
            $stmtEmprestimo->bind_param('iiss', $idUsuario, $idLivro, $dataEmprestimo, $dataPrevista);

            if (!$stmtEmprestimo->execute()) {
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

    public function registrarDevolucao(int $idEmprestimo): bool
    {
        if (!$this->db) {
            return false;
        }

        $this->db->begin_transaction();

        try {
            $stmtBusca = $this->db->prepare("SELECT id_livro FROM tb_emprestimos WHERE id = ? AND status = 'ATIVO' FOR UPDATE");
            $stmtBusca->bind_param('i', $idEmprestimo);
            $stmtBusca->execute();
            $resultado = $stmtBusca->get_result();
            $emprestimo = $resultado ? $resultado->fetch_assoc() : null;

            if (!$emprestimo) {
                $this->db->rollback();
                return false;
            }

            $idLivro = (int) $emprestimo['id_livro'];

            $stmtDevolve = $this->db->prepare("UPDATE tb_emprestimos SET status = 'DEVOLVIDO', data_devolucao = CURDATE() WHERE id = ?");
            $stmtDevolve->bind_param('i', $idEmprestimo);

            if (!$stmtDevolve->execute()) {
                $this->db->rollback();
                return false;
            }

            $stmtEstoque = $this->db->prepare("UPDATE tb_livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?");
            $stmtEstoque->bind_param('i', $idLivro);

            if (!$stmtEstoque->execute()) {
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
}