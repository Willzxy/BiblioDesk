<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';
require_once '../App/Models/tb_usuarios.php';

use App\Models\tb_usuarios;
use MF\Action;

class UsuariosController extends Action
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
        $usuariosModel = new tb_usuarios();
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

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($nome === '' || $documento === '' || $telefone === '') {
            $_SESSION['flash_erro'] = 'Preencha nome, documento e telefone do usuário.';
            $this->redirect('/usuarios');
            exit;
        }

        $usuariosModel = new tb_usuarios();
        $salvou = $usuariosModel->cadastrar($_POST);

        if ($salvou) {
            $_SESSION['flash_sucesso'] = 'Usuário cadastrado com sucesso.';
        } else {
            $_SESSION['flash_erro'] = 'Não foi possível cadastrar o usuário. Verifique se o documento já existe.';
        }

        $this->redirect('/usuarios');
        exit;
    }

    public function editar()
    {
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

        $usuariosModel = new tb_usuarios();
        $atualizou = $usuariosModel->atualizar($id, $_POST);

        if ($atualizou) {
            $_SESSION['flash_sucesso'] = 'Dados do usuário atualizados com sucesso.';
        } else {
            $_SESSION['flash_erro'] = 'Erro ao atualizar o usuário. Verifique se o documento já está cadastrado.';
        }

        $this->redirect('/usuarios');
        exit;
    }

    public function excluir()
    {
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

        $usuariosModel = new tb_usuarios();
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