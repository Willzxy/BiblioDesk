<?php

namespace App\Models;

require_once '../MinhaFramework/Models/Models.php';

use MF\Models;

class tb_usuarios extends Models
{
    public function contarTotal(): int
    {
        try {
            if (!$this->db) {
                return 0;
            }

            $resultado = $this->db->query("SELECT COUNT(*) AS total FROM tb_usuarios");
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
        $usuarios = [];

        try {
            if (!$this->db) {
                return $usuarios;
            }

            $baseSql = "SELECT 
                            u.*,
                            (SELECT COUNT(*) FROM tb_emprestimos e WHERE e.id_usuario = u.id AND e.status = 'ATIVO') AS emprestimos_ativos,
                            (SELECT COUNT(*) FROM tb_emprestimos e WHERE e.id_usuario = u.id AND e.status = 'ATIVO' AND e.data_prevista < CURDATE()) AS emprestimos_atrasados
                        FROM tb_usuarios u";

            if ($busca !== '') {
                $termo = '%' . $busca . '%';
                $stmt = $this->db->prepare($baseSql . " WHERE u.nome LIKE ? OR u.documento LIKE ? ORDER BY u.nome ASC");
                $stmt->bind_param('ss', $termo, $termo);
                $stmt->execute();
                $resultado = $stmt->get_result();
            } else {
                $resultado = $this->db->query($baseSql . " ORDER BY u.nome ASC");
            }

            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $usuarios[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $usuarios;
        }

        return $usuarios;
    }

    public function listarAtivos(): array
    {
        $usuarios = [];

        try {
            if (!$this->db) {
                return $usuarios;
            }

            $resultado = $this->db->query("SELECT * FROM tb_usuarios WHERE ativo = TRUE ORDER BY nome ASC");
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $usuarios[] = $row;
                }
            }
        } catch (\Throwable $th) {
            return $usuarios;
        }

        return $usuarios;
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            if (!$this->db) {
                return null;
            }

            $stmt = $this->db->prepare("SELECT * FROM tb_usuarios WHERE id = ?");
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

            $nome = trim($dados['nome'] ?? '');
            $documento = trim($dados['documento'] ?? '');
            $telefone = trim($dados['telefone'] ?? '');
            $observacao = trim($dados['observacao'] ?? '');
            $observacao = $observacao === '' ? null : $observacao;
            $ativo = isset($dados['ativo']) ? (int) (bool) $dados['ativo'] : 1;

            $stmt = $this->db->prepare(
                "INSERT INTO tb_usuarios (nome, documento, telefone, observacao, ativo) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('ssssi', $nome, $documento, $telefone, $observacao, $ativo);

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

            $nome = trim($dados['nome'] ?? '');
            $documento = trim($dados['documento'] ?? '');
            $telefone = trim($dados['telefone'] ?? '');
            $observacao = trim($dados['observacao'] ?? '');
            $observacao = $observacao === '' ? null : $observacao;
            $ativo = isset($dados['ativo']) ? (int) (bool) $dados['ativo'] : 1;

            $stmt = $this->db->prepare(
                "UPDATE tb_usuarios SET nome = ?, documento = ?, telefone = ?, observacao = ?, ativo = ? WHERE id = ?"
            );
            $stmt->bind_param('ssssii', $nome, $documento, $telefone, $observacao, $ativo, $id);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        try {
            if (!$this->db) {
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM tb_usuarios WHERE id = ?");
            $stmt->bind_param('i', $id);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }
}