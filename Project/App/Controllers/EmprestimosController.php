<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once __DIR__ . '/../Factories/EmprestimoModelFactory.php';
require_once __DIR__ . '/../Observers/EmprestimoSubject.php';
require_once __DIR__ . '/../Observers/AuditoriaLogObserver.php';
require_once __DIR__ . '/../Observers/EstoqueAlertaObserver.php';

use App\Factories\EmprestimoModelFactory;
use App\Observers\AuditoriaLogObserver;
use App\Observers\EmprestimoSubject;
use App\Observers\EstoqueAlertaObserver;
use MF\Action;

class EmprestimosController extends Action{
    private EmprestimoModelFactory $emprestimoFactory;

    public function __construct(){
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->emprestimoFactory = new EmprestimoModelFactory();
    }

    private function criarSubject(): EmprestimoSubject{
        $subject = new EmprestimoSubject();
        $subject->anexar(new AuditoriaLogObserver());
        $subject->anexar(new EstoqueAlertaObserver());
        return $subject;
    }

    public function novo(){
        $this->view->tituloPagina = 'Registrar Empréstimo';
        $this->view->paginaAtiva = 'novo-emprestimo';
        $this->view->dataHoje = date('Y-m-d');
        $this->view->dataPrevistaPadrao = date('Y-m-d', strtotime('+7 days'));
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('emprestimos.novo');
    }

    public function registrar(){
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

        $emprestimosModel = $this->emprestimoFactory->criarModel();
        $registrou = $emprestimosModel->registrar($idUsuario, $idLivro, $dataEmprestimo, $dataPrevista);

        if ($registrou) {
            $_SESSION['flash_sucesso'] = 'Empréstimo registrado com sucesso!';
            $this->criarSubject()->notificar('EMPRESTIMO_REGISTRADO', [
                'id_usuario' => $idUsuario,
                'id_livro' => $idLivro,
                'data_emprestimo' => $dataEmprestimo,
                'data_prevista' => $dataPrevista
            ]);
            $this->redirect('/emprestimos');
            exit;
        }

        $_SESSION['flash_erro'] = 'Não foi possível registrar o empréstimo. Verifique a disponibilidade do livro.';
        $this->redirect('/emprestimos/novo');
        exit;
    }

    public function index(){
        $emprestimosModel = $this->emprestimoFactory->criarModel();
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

    public function devolver(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emprestimos');
            exit;
        }

        $idEmprestimo = (int) ($_POST['id_emprestimo'] ?? 0);
        $origem = trim($_POST['origem'] ?? '');
        $rotaDestino = ($origem === 'dashboard') ? '/dashboard' : '/emprestimos';

        if ($idEmprestimo <= 0) {
            $_SESSION['flash_erro'] = 'Empréstimo inválido para devolução.';
            $this->redirect($rotaDestino);
            exit;
        }

        $emprestimosModel = $this->emprestimoFactory->criarModel();
        $devolveu = $emprestimosModel->registrarDevolucao($idEmprestimo);

        if ($devolveu) {
            $_SESSION['flash_sucesso'] = 'Devolução registrada e exemplar devolvido ao estoque.';
            $this->criarSubject()->notificar('DEVOLUCAO_REGISTRADA', [
                'id_emprestimo' => $idEmprestimo,
                'origem' => $origem !== '' ? $origem : 'emprestimos'
            ]);
        } else {
            $_SESSION['flash_erro'] = 'Não foi possível registrar a devolução deste empréstimo.';
        }

        $this->redirect($rotaDestino);
        exit;
    }
}