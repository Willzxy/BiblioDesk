<?php

namespace App\Observers;

require_once __DIR__ . '/EmprestimoObserver.php';
require_once __DIR__ . '/../Factories/LivroModelFactory.php';

use App\Factories\LivroModelFactory;

class EstoqueAlertaObserver implements EmprestimoObserver{
    public function atualizar(string $evento, array $dados): void{
        if ($evento !== 'EMPRESTIMO_REGISTRADO') {
            return;
        }

        $idLivro = (int) ($dados['id_livro'] ?? 0);
        if ($idLivro <= 0) {
            return;
        }

        $livroFactory = new LivroModelFactory();
        $livrosModel = $livroFactory->criarModel();
        $livro = $livrosModel->buscarPorId($idLivro);

        if ($livro && (int) $livro['quantidade_disponivel'] === 0) {
            $mensagemAtual = $_SESSION['flash_sucesso'] ?? 'Empréstimo registrado com sucesso!';
            $_SESSION['flash_sucesso'] = $mensagemAtual . ' Atenção: o livro "' . $livro['titulo'] . '" atingiu estoque zerado.';
        }
    }
}