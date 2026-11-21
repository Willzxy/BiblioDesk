<?php

namespace App\Middleware;

require_once '../MinhaFramework/Controllers/action.php';

use MF\Action;

class AuthMiddleware extends Action
{
    public function action()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['bibliotecario_autenticado'])) {
            $this->redirect('/');
            exit;
        }
    }
}