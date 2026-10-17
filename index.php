<?php
session_start();

// Si ya hay sesión iniciada, va directo al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: php/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'php/conexion.php';

    $correo   = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    $valido = false;

    if ($usuario) {
        $guardada = $usuario['password'];

        if (password_verify($password, $guardada)) {
            $valido = true;
        } elseif (empty(password_get_info($guardada)['algo']) && hash_equals($guardada, $password)) {
            // Contraseña antigua en texto plano: se acepta esta vez y se guarda como hash
            $valido = true;
            $nuevo_hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $upd->bind_param("si", $nuevo_hash, $usuario['id']);
            $upd->execute();
        }
    }

    if ($valido) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['nombre']     = $usuario['nombre'];
        $_SESSION['rol']        = $usuario['rol'];

        header("Location: php/dashboard.php");
        exit();
    }

    // Mensaje único, para no revelar si el correo existe o no
    $error = "Correo o contraseña incorrectos.";
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
        <p>Entra para continuar tu aventura</p>

        <?php if ($error !== ''): ?>
            <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="input-group">
                <label for="email">Correo:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">ENTRAR AL JUEGO</button>
        </form>
    </div>

</body>
</html>
