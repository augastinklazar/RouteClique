<?php

class post extends user {
    function __construct($pdo) {
        $this->pdo = $pdo;
        
    }
}

?>