<?php
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathApp - Panel del Alumno</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Encabezado con Perfil y Stats Gamificadas -->
        <header class="user-bar">
            <div class="user-info">
                <h2>👋 ¡Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                <span class="badge-role">Nivel <?php echo $_SESSION['nivel']; ?></span>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <span>⭐ Puntos</span>
                    <strong><?php echo $_SESSION['puntos']; ?> PTS</strong>
                </div>
                <div class="stat-box">
                    <span>🏆 Rango</span>
                    <strong>Aprendiz</strong>
                </div>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Selección de Módulos -->
        <main class="modules-grid">
            <div class="card-module">
                <div class="icon">➕</div>
                <h3>Suma y Resta Básica</h3>
                <p>Domina las operaciones fundamentales y acumula tus primeros puntos.</p>
                <a href="juego.php?modulo=1" class="btn-play">¡Jugar Ahora!</a>
            </div>

            <div class="card-module">
                <div class="icon">✖️</div>
                <h3>Multiplicación y División</h3>
                <p>Desafíos rápidos de tablas de multiplicar y repartos.</p>
                <a href="juego.php?modulo=2" class="btn-play">¡Jugar Ahora!</a>
            </div>

            <div class="card-module">
                <div class="icon">🍕</div>
                <h3>Fracciones Interactiva</h3>
                <p>Aprende a dividir el entero y comparar proporciones.</p>
                <a href="juego.php?modulo=3" class="btn-play">¡Jugar Ahora!</a>
            </div>
        </main>
    </div>
</body>
</html>
