<?php

namespace App\Middleware;

require_once '../MinhaFramework/Controllers/action.php';

use MF\Action;

class TesteMiddleware extends Action {
    public function index(){
        // Libera a rota de qualquer maneira
    }
}