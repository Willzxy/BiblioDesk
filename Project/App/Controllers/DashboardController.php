<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once '../App/Models/tb_livros.php';
require_once '../App/Models/tb_usuarios.php';
require_once '../App/Models/tb_emprestimos.php';

use App\Models\tb_emprestimos;
use App\Models\tb_livros;
use App\Models\tb_usuarios;
use MF\Action;

class DashboardController extends Action
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
        $usuariosModel = new tb_usuarios();
        $emprestimosModel = new tb_emprestimos();

        $this->view->tituloPagina = 'Painel do Bibliotecário';
        $this->view->paginaAtiva = 'dashboard';
        $this->view->resumo = [
            'total_livros' => $livrosModel->contarTotalExemplares(),
            'total_usuarios' => $usuariosModel->contarTotal(),
            'emprestimos_ativos' => $emprestimosModel->contarAtivos(),
            'emprestimos_atrasados' => $emprestimosModel->contarAtrasados()
        ];
        $this->view->pendencias = $emprestimosModel->listarPendencias();

        $this->render('dashboard.index');
    }
}