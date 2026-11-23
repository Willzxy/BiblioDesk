<?php

namespace App\Observers;

require_once __DIR__ . '/EmprestimoObserver.php';

class EmprestimoSubject{
    private array $observadores = [];

    public function anexar(EmprestimoObserver $observador): void{
        $this->observadores[] = $observador;
    }

    public function desanexar(EmprestimoObserver $observador): void{
        $this->observadores = array_values(array_filter(
            $this->observadores,
            fn (EmprestimoObserver $item) => $item !== $observador
        ));
    }

    public function notificar(string $evento, array $dados): void{
        foreach ($this->observadores as $observador) {
            $observador->atualizar($evento, $dados);
        }
    }
}