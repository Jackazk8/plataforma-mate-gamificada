<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Por ahora solo existe el módulo 1 (suma y resta)
$modulo = (int)($_GET['modulo'] ?? 1);
if ($modulo !== 1) {
    header("Location: dashboard.php");
    exit();
}

// Datos actuales del alumno
$stmt = $conexion->prepare("SELECT nombre, puntos FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

/**
 * Genera una suma o resta con números aleatorios.
 * Las opciones son distintas entre sí y nunca negativas.
 */
function generar_pregunta_dinamica(): array {
    $n1 = rand(10, 45);
    $n2 = rand(5, 30);

    if (rand(0, 1) === 1) {
        $correcta    = $n1 + $n2;
        $enunciado   = "Recolectaste $n1 manzanas en un árbol y $n2 manzanas en otro. ¿Cuántas manzanas tienes en total?";
        $pista       = "Identifica los datos: $n1 y $n2. Cuando juntas elementos, aplicas una Suma (+).";
        $explicacion = "Al juntar elementos aplicamos una Suma (+): $n1 + $n2 = $correcta.";
    } else {
        if ($n1 <= $n2) {
            [$n1, $n2] = [$n2, $n1];       // el mayor va primero
            if ($n1 === $n2) {
                $n1++;                      // evita un resultado de 0
            }
        }
        $correcta    = $n1 - $n2;
        $enunciado   = "Tenías $n1 manzanas, pero le diste $n2 a Newton para sus experimentos. ¿Cuántas manzanas te quedan?";
        $pista       = "Identifica los datos: tenías $n1 y regalaste $n2. Cuando quitas o repartes elementos, aplicas una Resta (-).";
        $explicacion = "Al quitar elementos aplicamos una Resta (-): $n1 - $n2 = $correcta.";
    }

    $distractores = [];
    while (count($distractores) < 3) {
        $d = $correcta + rand(-8, 10);
        if ($d >= 0 && $d !== $correcta && !in_array($d, $distractores, true)) {
            $distractores[] = $d;
        }
    }

    $valores = array_merge([$correcta], $distractores);
    shuffle($valores);
    $opciones = array_combine(['A', 'B', 'C', 'D'], $valores);

    return [
        'enunciado'   => $enunciado,
        'opciones'    => $opciones,
        'correcta'    => array_search($correcta, $opciones, true),
        'pista'       => $pista,
        'explicacion' => $explicacion,
        'xp'          => 50,
    ];
}

// 1. A veces se usa una pregunta guardada en la base de datos
$pregunta = null;
if (rand(0, 1) === 1) {
    $stmt = $conexion->prepare("SELECT * FROM preguntas WHERE reino = ? ORDER BY RAND() LIMIT 1");
    $stmt->bind_param("i", $modulo);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if ($fila) {
        $pregunta = [
            'enunciado'   => $fila['pregunta'],
            'opciones'    => ['A' => $fila['opcion_a'], 'B' => $fila['opcion_b'], 'C' => $fila['opcion_c'], 'D' => $fila['opcion_d']],
            'correcta'    => strtoupper($fila['respuesta_correcta']),
            'pista'       => $fila['pista'] ?: 'Lee con calma, identifica los datos y piensa qué operación necesitas.',
            'explicacion' => $fila['explicacion'],
            'xp'          => (int)($fila['puntos_xp'] ?: 50),
        ];
    }
}

// 2. Si no hubo pregunta de la base de datos, se genera una dinámica
if ($pregunta === null) {
    $pregunta = generar_pregunta_dinamica();
}

// La respuesta correcta se guarda SOLO en el servidor (en la sesión).
// El navegador nunca la recibe: responder.php es quien la valida.
$_SESSION['pregunta_actual'] = [
    'reino'       => $modulo,
    'enunciado'   => mb_substr($pregunta['enunciado'], 0, 255),
    'correcta'    => $pregunta['correcta'],
    'explicacion' => $pregunta['explicacion'],
    'xp'          => $pregunta['xp'],
    'resuelta'    => false,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathApp - Aventura Aritmética</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/juego.css">
</head>
<body class="game-bg">

    <div class="game-container">
        <div class="character-newton">
            <div class="sprite-placeholder">🍎</div>
            <div class="mentor-badge">MENTOR NEWTON</div>
        </div>

        <div class="dialog-scroll">
            <div class="dialog-header">
                <h2>El Valle de la Suma y Resta</h2>
                <div class="score-display">XP: <span id="score"><?php echo (int)$usuario['puntos']; ?></span></div>
            </div>

            <div class="dialog-text">
                <p id="newton-message">
                    "¡Atención, <?php echo htmlspecialchars($usuario['nombre']); ?>!<br><br>
                    <?php echo htmlspecialchars($pregunta['enunciado']); ?>"
                </p>
                <p id="explicacion" class="explicacion hidden"></p>
            </div>

            <div class="options-grid" id="options-container">
                <?php foreach ($pregunta['opciones'] as $letra => $texto): ?>
                    <button class="btn-option" data-option="<?php echo $letra; ?>"><?php echo $letra; ?>) <?php echo htmlspecialchars($texto); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="action-panel">
            <div class="status-badge">REINO 1</div>
            <button class="btn-action help" id="btn-help">📘 APRENDE A ESTUDIAR</button>
            <button class="btn-action flee" onclick="window.location.href='dashboard.php'">🏃 HUIR</button>
            <button class="btn-action continue hidden" id="btn-next" onclick="window.location.reload();">▶️ SIGUIENTE</button>
        </div>
    </div>

    <footer class="game-footer">
        MUNDO 1: EL VALLE DE LA SUMA Y RESTA
    </footer>

    <!-- Módulo "Aprende a Estudiar": da una pista, pero no el resultado -->
    <div id="modal-help" class="modal hidden">
        <div class="modal-content">
            <h2>📜 CONSEJO DE ISAAC NEWTON</h2>
            <div class="lesson-body">
                <p><?php echo htmlspecialchars($pregunta['pista']); ?></p>
            </div>
            <button class="btn-primary" id="btn-close-help">¡ENTENDIDO, VOLVER!</button>
        </div>
    </div>

    <script src="../js/juego.js"></script>
</body>
</html>
