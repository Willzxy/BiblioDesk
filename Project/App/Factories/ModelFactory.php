<?php

namespace App\Factories;

require_once __DIR__ . '/../../MinhaFramework/Models/Models.php';

use MF\Models;

abstract class ModelFactory{
    abstract public function criarModel(): Models;
}