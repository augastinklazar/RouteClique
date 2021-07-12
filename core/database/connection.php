<?php

$hostdetails = 'mysql:host=127.0.0.1; dbname=RouteClique; charset=utf8mb4';
$useradmin = 'root';
$pass = '';

try{
    $pdo = new PDO($hostdetails, $useradmin, $pass);
}
catch(PDOException $e) {
    echo 'Connection error!!!'.$e->getMessage();
}





?>