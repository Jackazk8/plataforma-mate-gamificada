<?php
session_start();

// Si el usuario ya tiene sesión iniciada, lo mandamos al dashboard dentro de la carpeta /php/
if (isset($_SESSION['usuario_id'])) {
    header("Location: php/dashboard.php");
    exit();
}

// Si envió el formulario de login (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'php/conexion.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Consulta de ejemplo para validar usuario
    $sql = "SELECT id_usuario, nombre_usuario, password_hash FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        if (password_verify($password, $usuario['password_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_usuario'];
            $_SESSION['puntos'] = 100;

            // Redirección hacia la subcarpeta /php/
            header("Location: php/dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathApp - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="login-bg">

    <div class="login-container">
        <h1>MATHAPP 8-BIT</h1>
        
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <label for="email">Correo:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">ENTRAR AL JUEGO</button>
        </form>
    </div>

</body>
</html>