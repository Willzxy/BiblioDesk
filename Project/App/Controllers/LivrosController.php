<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once '../App/Models/tb_livros.php';

use App\Models\tb_livros;
use MF\Action;

class LivrosController extends Action
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $livrosModel = new tb_livros();
        $busca = trim($_GET['busca'] ?? '');

        $this->view->tituloPagina = 'Catálogo de Livros';
        $this->view->paginaAtiva = 'livros';
        $this->view->busca = $busca;
        $this->view->livros = $livrosModel->listarTodos($busca);
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('livros.index');
    }

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/livros');
            exit;
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $autor = trim($_POST['autor'] ?? '');
        $codigoIsbn = trim($_POST['codigo_isbn'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');

        if ($titulo === '' || $autor === '' || $codigoIsbn === '' || $categoria === '') {
            $_SESSION['flash_erro'] = 'Preencha todos os campos obrigatórios do livro.';
            $this->redirect('/livros');
            exit;
        }

        $livrosModel = new tb_livros();
        $salvou = $livrosModel->cadastrar($_POST);

        if ($salvou) {
            $_SESSION['flash_sucesso'] = 'Livro cadastrado com sucesso no catálogo.';
        } else {
            $_SESSION['flash_erro'] = 'Não foi possível cadastrar o livro. Verifique se o ISBN já existe.';
        }

        $this->redirect('/livros');
        exit;
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/livros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $autor = trim($_POST['autor'] ?? '');
        $codigoIsbn = trim($_POST['codigo_isbn'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');

        if ($id <= 0 || $titulo === '' || $autor === '' || $codigoIsbn === '' || $categoria === '') {
            $_SESSION['flash_erro'] = 'Dados inválidos para atualização do livro.';
            $this->redirect('/livros');
            exit;
        }

        $livrosModel = new tb_livros();
        $atualizou = $livrosModel->atualizar($id, $_POST);

        if ($atualizou) {
            $_SESSION['flash_sucesso'] = 'Dados do livro atualizados com sucesso.';
        } else {
            $_SESSION['flash_erro'] = 'Erro ao atualizar o livro. Verifique se o ISBN já está em uso.';
        }

        $this->redirect('/livros');
        exit;
    }

    public function excluir()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/livros');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_erro'] = 'Livro inválido para exclusão.';
            $this->redirect('/livros');
            exit;
        }

        $livrosModel = new tb_livros();
        $excluiu = $livrosModel->excluir($id);

        if ($excluiu) {
            $_SESSION['flash_sucesso'] = 'Livro removido do catálogo.';
        } else {
            $_SESSION['flash_erro'] = 'Não é possível excluir este livro porque existem empréstimos vinculados a ele.';
        }

        $this->redirect('/livros');
        exit;
    }
}