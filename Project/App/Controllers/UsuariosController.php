<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once __DIR__ . '/../Factories/UsuarioModelFactory.php';

use App\Factories\UsuarioModelFactory;
use MF\Action;

class UsuariosController extends Action
{
    private UsuarioModelFactory $usuarioFactory;

    public function __construct(){
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->usuarioFactory = new UsuarioModelFactory();
    }

    public function index(){
        $usuariosModel = $this->usuarioFactory->criarModel();
        $busca = trim($_GET['busca'] ?? '');

        $this->view->tituloPagina = 'Usuários / Leitores';
        $this->view->paginaAtiva = 'usuarios';
        $this->view->busca = $busca;
        $this->view->usuarios = $usuariosModel->listarTodos($busca);
        $this->view->sucesso = $_SESSION['flash_sucesso'] ?? null;
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_sucesso'], $_SESSION['flash_erro']);

        $this->render('usuarios.index');
    }

    public function buscarJson(){
        header('Content-Type: application/json; charset=utf-8');

        $termo = trim($_GET['q'] ?? '');
        if (mb_strlen($termo) < 2) {
            echo json_encode([]);
            exit;
        }

        $usuariosModel = $this->usuarioFactory->criarModel();
        $usuarios = $usuariosModel->buscarRapido($termo, 8);

        echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function novo(){
        $this->view->tituloPagina = 'Cadastrar Usuário';
        $this->view->paginaAtiva = 'usuarios';
        $this->view->origem = trim($_GET['origem'] ?? '');
        $this->view->erro = $_SESSION['flash_erro'] ?? null;

        unset($_SESSION['flash_erro']);

        $this->render('usuarios.novo');
    }

    public function cadastrar(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios/novo');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $origem = trim($_POST['origem'] ?? '');
        $rotaRetornoErro = $origem === 'emprestimo' ? '/usuarios/novo?origem=emprestimo' : '/usuarios/novo';

        if ($nome === '' || $documento === '' || $telefone === '') {
            $_SESSION['flash_erro'] = 'Preencha nome, documento e telefone do usuário.';
            $this->redirect($rotaRetornoErro);
            exit;
        }

        $usuariosModel = $this->usuarioFactory->criarModel();
        $salvou = $usuariosModel->cadastrar($_POST);

        if ($salvou) {
            $_SESSION['flash_sucesso'] = 'Usuário cadastrado com sucesso.';
            if ($origem === 'emprestimo') {
                $this->redirect('/emprestimos/novo');
                exit;
            }
            $this->redirect('/usuarios');
            exit;
        }

        $_SESSION['flash_erro'] = 'Não foi possível cadastrar o usuário. Verifique se o documento já existe.';
        $this->redirect($rotaRetornoErro);
        exit;
    }

    public function editar(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($id <= 0 || $nome === '' || $documento === '' || $telefone === '') {
            $_SESSION['flash_erro'] = 'Dados inválidos para atualizar o usuário.';
            $this->redirect('/usuarios');
            exit;
        }

        $usuariosModel = $this->usuarioFactory->criarModel();
        $atualizou = $usuariosModel->atualizar($id, $_POST);

        if ($atualizou) {
            $_SESSION['flash_sucesso'] = 'Dados do usuário atualizados com sucesso.';
        } else {
            $_SESSION['flash_erro'] = 'Erro ao atualizar o usuário. Verifique se o documento já está cadastrado.';
        }

        $this->redirect('/usuarios');
        exit;
    }

    public function excluir(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_erro'] = 'Usuário inválido para exclusão.';
            $this->redirect('/usuarios');
            exit;
        }

        $usuariosModel = $this->usuarioFactory->criarModel();
        $excluiu = $usuariosModel->excluir($id);

        if ($excluiu) {
            $_SESSION['flash_sucesso'] = 'Usuário excluído com sucesso.';
        } else {
            $_SESSION['flash_erro'] = 'Não é possível excluir este usuário porque possui histórico de empréstimos.';
        }

        $this->redirect('/usuarios');
        exit;
    }
}