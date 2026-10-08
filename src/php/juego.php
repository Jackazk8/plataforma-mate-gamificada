<?php
session_start();

// Al estar dentro de /php/, conexion.php se encuentra en la misma carpeta
require_once 'conexion.php';

// Si no hay sesión, se usa ../ para regresar al index.php de la raíz
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'] ?? 'Explorador';
$puntos_xp = $_SESSION['puntos'] ?? 100;

// 1. Obtener una pregunta ALEATORIA del Reino 1 desde la base de datos
$sql = "SELECT * FROM preguntas WHERE reino = 1 ORDER BY RAND() LIMIT 1";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $pregunta = $resultado->fetch_assoc();
} else {
    $pregunta = null;
}

// 2. Generar números aleatorios dinámicos para el ejercicio
$num1 = rand(10, 45);
$num2 = rand(5, 30);

// Decidir si es suma o resta de forma aleatoria
$operacion = (rand(0, 1) === 1) ? '+' : '-';

if ($operacion === '+') {
    $resultado_correcto = $num1 + $num2;
    $texto_pregunta = "Recolectaste $num1 manzanas en un árbol y $num2 manzanas en otro. ¿Cuántas manzanas tienes en total?";
    $explicacion_dinamica = "Identifica los datos: $num1 y $num2. Al juntar elementos aplicamos una Suma (+): $num1 + $num2 = $resultado_correcto.";
} else {
    // Evitar resultados negativos
    if ($num1 < $num2) {
        $temp = $num1;
        $num1 = $num2;
        $num2 = $temp;
    }
    $resultado_correcto = $num1 - $num2;
    $texto_pregunta = "Tenías $num1 manzanas, pero le diste $num2 a Newton para sus experimentos. ¿Cuántas manzanas te quedan?";
    $explicacion_dinamica = "Identifica los datos: tenías $num1 y regalaste $num2. Al quitar o repartir elementos aplicamos una Resta (-): $num1 - $num2 = $resultado_correcto.";
}

// 3. Generar opciones falsas aleatorias alrededor de la respuesta correcta
$opcion_correcta_letra = ['A', 'B', 'C', 'D'][rand(0, 3)];
$opciones = [
    'A' => $resultado_correcto + rand(2, 5),
    'B' => $resultado_correcto - rand(1, 4),
    'C' => $resultado_correcto + rand(6, 10),
    'D' => $resultado_correcto - rand(5, 8)
];
// Asignar la respuesta correcta a la letra seleccionada al azar
$opciones[$opcion_correcta_letra] = $resultado_correcto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathApp - Aventura Aritmética</title>
    <!-- Rutas relativas con ../ para acceder a la carpeta css de la raíz -->
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/juego.css">
</head>
<body class="game-bg">

    <div class="game-container">
        <!-- Mentor Isaac Newton -->
        <div class="character-newton">
            <div class="sprite-placeholder">🍎</div>
            <div class="mentor-badge">MENTOR NEWTON</div>
        </div>

        <!-- Pergamino de Diálogo y Pregunta -->
        <div class="dialog-scroll">
            <div class="dialog-header">
                <h2>El Valle de la Suma y Resta</h2>
                <div class="score-display">XP: <span id="score"><?php echo $puntos_xp; ?></span></div>
            </div>

            <div class="dialog-text">
                <p id="newton-message">
                    "¡Atención, <?php echo htmlspecialchars($nombre_usuario); ?>!<br><br>
                    <?php echo $texto_pregunta; ?>"
                </p>
            </div>

            <!-- Opciones Dinámicas -->
            <div class="options-grid" id="options-container">
                <button class="btn-option" data-option="A">A) <?php echo $opciones['A']; ?></button>
                <button class="btn-option" data-option="B">B) <?php echo $opciones['B']; ?></button>
                <button class="btn-option" data-option="C">C) <?php echo $opciones['C']; ?></button>
                <button class="btn-option" data-option="D">D) <?php echo $opciones['D']; ?></button>
            </div>
        </div>

        <!-- Panel de Acciones -->
        <div class="action-panel">
            <div class="status-badge">REINO 1</div>
            <button class="btn-action help" id="btn-help">📘 APRENDE A ESTUDIAR</button>
            <button class="btn-action flee" onclick="window.location.href='dashboard.php'">🏃 HUIR</button>
            <button class="btn-action continue hidden" id="btn-next" onclick="window.location.reload();">▶️ SIGUIENTE</button>
        </div>
    </div>

    <footer class="game-footer">
        MUNDO 1: EL VALLE DE LA SUMA Y RESTA — EJERCICIO DINÁMICO
    </footer>

    <!--  Módulo "Aprende a Estudiar" -->
    <div id="modal-help" class="modal hidden">
        <div class="modal-content">
            <h2>📜 CONSEJO DE ISAAC NEWTON</h2>
            <div class="lesson-body">
                <p><?php echo $explicacion_dinamica; ?></p>
            </div>
            <button class="btn-primary" id="btn-close-help">¡ENTENDIDO, VOLVER!</button>
        </div>
    </div>

    <script>
        const RESPUESTA_CORRECTA = "<?php echo $opcion_correcta_letra; ?>";
        const PUNTOS_RECOMPENSA = 50;
    </script>
    <!-- Ruta para acceder a la carpeta js de la raíz -->
    <script src="../js/juego.js"></script>
</body>
</html>