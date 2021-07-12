<?php

include 'database/connection.php';
include 'classes/users.php';
include 'classes/post.php';

global $pdo;

$loadfromuser = new user($pdo);
// $loadfrompost = new post($pdo);
define("BASE_URL", "http://localhost/RouteClique");

?>