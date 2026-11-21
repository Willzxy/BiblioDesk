<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once '../App/Models/tb_emprestimos.php';

use App\Models\tb_emprestimos;
use MF\Action;

class HistoricoController extends Action
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
        $emprestimosModel = new tb_emprestimos();
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