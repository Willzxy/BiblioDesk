<?php

namespace App\Factories;

require_once __DIR__ . '/ModelFactory.php';
require_once __DIR__ . '/../Models/tb_usuarios.php';

use App\Models\tb_usuarios;

class UsuarioModelFactory extends ModelFactory{
    public function criarModel(): tb_usuarios{
        return new tb_usuarios();
    }
}