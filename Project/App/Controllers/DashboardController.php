<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once __DIR__ . '/../Factories/LivroModelFactory.php';
require_once __DIR__ . '/../Factories/UsuarioModelFactory.php';
require_once __DIR__ . '/../Factories/EmprestimoModelFactory.php';

use App\Factories\EmprestimoModelFactory;
use App\Factories\LivroModelFactory;
use App\Factories\UsuarioModelFactory;
use MF\Action;

class DashboardController extends Action{
    public function __construct(){
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(){
        $livrosModel = (new LivroModelFactory())->criarModel();
        $usuariosModel = (new UsuarioModelFactory())->criarModel();
        $emprestimosModel = (new EmprestimoModelFactory())->criarModel();

        $this->view->tituloPagina = 'Painel do Bibliotecário';
        $this->view->paginaAtiva = 'dashboard';
        $this->view->resumo = [
            'total_livros' => $livrosModel->contarTotalExemplares(),
            'total_usuarios' => $usuariosModel->contarTotal(),
            'emprestimos_ativos' => $emprestimosModel->contarAtivos(),
            'emprestimos_atrasados' => $emprestimosModel->contarAtrasados()
        ];
        $this->view->pendencias = $emprestimosModel->listarPendencias();
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('dashboard.index');
    }
}