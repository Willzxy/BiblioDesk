<?php

namespace MF;

require_once __DIR__ . '/../../App/database/Connection.php';

use App\Connection;

class Models {
    protected $db;

    public function __construct() {
        $this->db = Connection::getInstance()->getDB();
    }
}