<?php

namespace App;

final class Connection{
    private static ?Connection $instancia = null;
    private ?\mysqli $conexao = null;

    private string $host = 'localhost:3306';
    private string $bd_name = 'bd_bibliodesk';
    private string $usuario = 'root';
    private string $senha = 'root';

    private function __construct(){
        try {
            $this->conexao = new \mysqli($this->host, $this->usuario, $this->senha, $this->bd_name);
        } catch (\Throwable $th) {
            $this->conexao = null;
        }
    }

    private function __clone(){}

    public function __wakeup(){
        throw new \Exception('Não é permitido desserializar um Singleton.');
    }

    public static function getInstance(): self{
        if (self::$instancia === null) {
            self::$instancia = new self();
        }

        return self::$instancia;
    }

    public function getDB(): ?\mysqli{
        return $this->conexao;
    }
}

// Aplicado método Singleton
/*
Prompt: implemente o padrão Connection, cuidando para não alterar nada como o projeto está funcionando. apenas aplique os padrões pelo requisitos do projeto 
Geminin: Converti Connection em um Singleton (Connection::getInstance()) mantendo exatamente as suas credenciais atuais (bd_bibliodesk, root/root), reutilizando uma única conexão mysqli durante toda a requisição em vez de abrir uma conexão por model.

como era antes:
<?php

namespace App;

abstract class Connection {
    private $host = 'localhost:3306';
    private $bd_name = 'bd_bibliodesk';
    private $usuario = 'root';
    private $senha = 'root';
    
    function getDB(){
        try {
            $Mysql = new \mysqli($this->host, $this->usuario, $this->senha, $this->bd_name);

            return $Mysql;
        } catch (\Throwable $th) {
            echo $th;
            echo 'tem parada errada ai';
        }
    }
}
*/