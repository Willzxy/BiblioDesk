<?php

namespace App\Observers;

interface EmprestimoObserver{
    public function atualizar(string $evento, array $dados): void;
}