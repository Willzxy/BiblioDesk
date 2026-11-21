<?php

namespace App\Controllers;

require_once '../MinhaFramework/Controllers/action.php';

use MF\Action;

class AuthController extends Action
{
    private const SENHA_BIBLIOTECARIO = 'admin123';

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        if (!empty($_SESSION['bibliotecario_autenticado'])) {
            $this->redirect('/dashboard');
            exit;
        }

        $this->view->erro = $_SESSION['erro_login'] ?? null;
        unset($_SESSION['erro_login']);

        $this->render('auth.login');
    }

    public function autenticar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            exit;
        }

        $senha = $_POST['senha'] ?? '';

        if ($senha === self::SENHA_BIBLIOTECARIO) {
            $_SESSION['bibliotecario_autenticado'] = true;
            $this->redirect('/dashboard');
            exit;
        }

        $_SESSION['erro_login'] = 'Senha incorreta. Acesso permitido apenas ao bibliotecário.';
        $this->redirect('/');
        exit;
    }

    public function sair()
    {
        session_destroy();
        $this->redirect('/');
        exit;
    }
}