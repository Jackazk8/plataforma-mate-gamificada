<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo   = $_POST['correo'];
    $password = $_POST['password'];

    // Consulta para verificar credenciales
    $stmt = $conexion->prepare("SELECT id, nombre, correo, password, rol, puntos, nivel FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Validación simple de contraseña
        if ($password === $usuario['password']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['rol']        = $usuario['rol'];
            $_SESSION['puntos']     = $usuario['puntos'];
            $_SESSION['nivel']      = $usuario['nivel'];

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>