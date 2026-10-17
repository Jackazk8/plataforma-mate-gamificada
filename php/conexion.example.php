<?php
// Conexión a la base de datos.
// Archivo de ejemplo: cópialo como conexion.php y escribe tus datos reales.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "math_app";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos.");
}

$conexion->set_charset("utf8mb4");
