<?php

namespace App\Observers;

require_once __DIR__ . '/EmprestimoObserver.php';

class AuditoriaLogObserver implements EmprestimoObserver{
    private string $caminhoLog;

    public function __construct(?string $caminhoLog = null){
        $this->caminhoLog = $caminhoLog ?? (__DIR__ . '/../database/auditoria.log');
    }

    public function atualizar(string $evento, array $dados): void{
        try {
            $dataHora = date('Y-m-d H:i:s');
            $detalhes = json_encode($dados, JSON_UNESCAPED_UNICODE);
            $linha = "[{$dataHora}] {$evento} | {$detalhes}" . PHP_EOL;
            @file_put_contents($this->caminhoLog, $linha, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $th) {
        }
    }
}