<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once '../App/Models/tb_emprestimos.php';
require_once '../App/Models/tb_usuarios.php';
require_once '../App/Models/tb_livros.php';

use App\Models\tb_emprestimos;
use App\Models\tb_livros;
use App\Models\tb_usuarios;
use MF\Action;

class EmprestimosController extends Action
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function novo()
    {
        $usuariosModel = new tb_usuarios();
        $livrosModel = new tb_livros();

        $this->view->tituloPagina = 'Registrar Empréstimo';
        $this->view->paginaAtiva = 'novo-emprestimo';
        $this->view->usuarios = $usuariosModel->listarAtivos();
        $this->view->livros = $livrosModel->listarDisponiveis();
        $this->view->dataHoje = date('Y-m-d');
        $this->view->dataPrevistaPadrao = date('Y-m-d', strtotime('+7 days'));
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('emprestimos.novo');
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emprestimos/novo');
            exit;
        }

        $idUsuario = (int) ($_POST['id_usuario'] ?? 0);
        $idLivro = (int) ($_POST['id_livro'] ?? 0);
        $dataEmprestimo = trim($_POST['data_emprestimo'] ?? '');
        $dataPrevista = trim($_POST['data_prevista'] ?? '');

        if ($idUsuario <= 0 || $idLivro <= 0 || $dataEmprestimo === '' || $dataPrevista === '') {
            $_SESSION['flash_erro'] = 'Selecione o usuário, o livro e informe as datas corretamente.';
            $this->redirect('/emprestimos/novo');
            exit;
        }

        if ($dataPrevista < $dataEmprestimo) {
            $_SESSION['flash_erro'] = 'A data prevista de devolução não pode ser anterior à data do empréstimo.';
            $this->redirect('/emprestimos/novo');
            exit;
        }

        $emprestimosModel = new tb_emprestimos();
        $registrou = $emprestimosModel->registrar($idUsuario, $idLivro, $dataEmprestimo, $dataPrevista);

        if ($registrou) {
            $_SESSION['flash_sucesso'] = 'Empréstimo registrado com sucesso!';
            $this->redirect('/emprestimos');
            exit;
        }

        $_SESSION['flash_erro'] = 'Não foi possível registrar o empréstimo. Verifique a disponibilidade do livro.';
        $this->redirect('/emprestimos/novo');
        exit;
    }

    public function index()
    {
        $emprestimosModel = new tb_emprestimos();
        $filtro = $_GET['filtro'] ?? 'TODOS';
        $busca = trim($_GET['busca'] ?? '');
        $somenteAtrasados = ($filtro === 'ATRASADOS');

        $this->view->tituloPagina = 'Empréstimos Ativos e Devolução';
        $this->view->paginaAtiva = 'emprestimos';
        $this->view->filtro = $filtro;
        $this->view->busca = $busca;
        $this->view->emprestimos = $emprestimosModel->listarAtivos($somenteAtrasados, $busca);
        $this->view->totalAtivos = $emprestimosModel->contarAtivos();
        $this->view->totalAtrasados = $emprestimosModel->contarAtrasados();
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('emprestimos.index');
    }

    public function devolver()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emprestimos');
            exit;
        }

        $idEmprestimo = (int) ($_POST['id_emprestimo'] ?? 0);

        if ($idEmprestimo <= 0) {
            $_SESSION['flash_erro'] = 'Empréstimo inválido para devolução.';
            $this->redirect('/emprestimos');
            exit;
        }

        $emprestimosModel = new tb_emprestimos();
        $devolveu = $emprestimosModel->registrarDevolucao($idEmprestimo);

        if ($devolveu) {
            $_SESSION['flash_sucesso'] = 'Devolução registrada e exemplar devolvido ao estoque.';
        } else {
            $_SESSION['flash_erro'] = 'Não foi possível registrar a devolução deste empréstimo.';
        }

        $this->redirect('/emprestimos');
        exit;
    }
}