<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "math_app";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}
?>