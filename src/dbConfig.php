<?php

$server = "localhost";
$nomdDB = "reservadb";
$usuariDB = "reservadb";
$pas = "microdelta";


try {
    $conn = new PDO("mysql:host=$server;dbname=reservadb", $usuariDB, $pas);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}