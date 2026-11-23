<?php

namespace App\Factories;

require_once __DIR__ . '/ModelFactory.php';
require_once __DIR__ . '/../Models/tb_emprestimos.php';

use App\Models\tb_emprestimos;

class EmprestimoModelFactory extends ModelFactory{
    public function criarModel(): tb_emprestimos{
        return new tb_emprestimos();
    }
}