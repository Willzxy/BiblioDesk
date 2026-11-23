<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once __DIR__ . '/../Factories/EmprestimoModelFactory.php';

use App\Factories\EmprestimoModelFactory;
use MF\Action;

class HistoricoController extends Action{
    private EmprestimoModelFactory $emprestimoFactory;
    public function __construct(){
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->emprestimoFactory = new EmprestimoModelFactory();
    }

    public function index(){
        $emprestimosModel = $this->emprestimoFactory->criarModel();
        $filtro = $_GET['filtro'] ?? 'TODOS';
        $busca = trim($_GET['busca'] ?? '');

        $this->view->tituloPagina = 'Histórico de Empréstimos';
        $this->view->paginaAtiva = 'historico';
        $this->view->filtro = $filtro;
        $this->view->busca = $busca;
        $this->view->historico = $emprestimosModel->listarHistorico($filtro, $busca);

        $this->render('historico.index');
    }
}