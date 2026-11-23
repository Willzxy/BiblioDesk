<?php

namespace App\Factories;

require_once __DIR__ . '/ModelFactory.php';
require_once __DIR__ . '/../Models/tb_livros.php';

use App\Models\tb_livros;

class LivroModelFactory extends ModelFactory{
    public function criarModel(): tb_livros{
        return new tb_livros();
    }
}