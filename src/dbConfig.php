<?php

$server = "localhost";
$nomdDB = "reservadb";
$usuariDB = "reservadb";
$pas = "microdelta";


try {
    $conn = new PDO("mysql:host=$server;dbname=reservadb", $usuariDB, $pas);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa";
} catch(PDOException $e) {
    echo "Conexión fallada: " . $e->getMessage();
}