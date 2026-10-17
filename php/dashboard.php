<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Los datos se leen siempre de la base de datos, para que los puntos estén al día
$stmt = $conexion->prepare("SELECT nombre, puntos, nivel FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

function rango_por_nivel(int $nivel): string {
    if ($nivel >= 6) return 'Sabio';
    if ($nivel >= 3) return 'Explorador';
    return 'Aprendiz';
}

$xp_en_nivel = $usuario['puntos'] % 100;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathApp - Panel del Alumno</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="user-bar">
            <div class="user-info">
                <h2>👋 ¡Hola, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h2>
                <span class="badge-role">Nivel <?php echo (int)$usuario['nivel']; ?></span>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <span>⭐ Puntos</span>
                    <strong><?php echo (int)$usuario['puntos']; ?> XP</strong>
                </div>
                <div class="stat-box">
                    <span>⬆️ Siguiente nivel</span>
                    <strong><?php echo $xp_en_nivel; ?>/100</strong>
                </div>
                <div class="stat-box">
                    <span>🏆 Rango</span>
                    <strong><?php echo rango_por_nivel((int)$usuario['nivel']); ?></strong>
                </div>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <main class="modules-grid">
            <div class="card-module">
                <div class="icon">➕</div>
                <h3>Suma y Resta Básica</h3>
                <p>Domina las operaciones fundamentales y acumula tus primeros puntos.</p>
                <a href="juego.php?modulo=1" class="btn-play">¡Jugar Ahora!</a>
            </div>

            <div class="card-module locked">
                <div class="icon">✖️</div>
                <h3>Multiplicación y División</h3>
                <p>Desafíos rápidos de tablas de multiplicar y repartos.</p>
                <span class="btn-play disabled">Próximamente</span>
            </div>

            <div class="card-module locked">
                <div class="icon">🍕</div>
                <h3>Fracciones Interactivas</h3>
                <p>Aprende a dividir el entero y comparar proporciones.</p>
                <span class="btn-play disabled">Próximamente</span>
            </div>
        </main>
    </div>
</body>
</html>
