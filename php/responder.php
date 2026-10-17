<?php
// Valida la respuesta del alumno en el servidor, guarda el intento y suma los puntos.
session_start();
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

function responder_error(int $codigo, string $mensaje): void {
    http_response_code($codigo);
    echo json_encode(['error' => $mensaje]);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    responder_error(403, 'Sesión no válida.');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_error(405, 'Método no permitido.');
}

$pregunta = $_SESSION['pregunta_actual'] ?? null;
if (!$pregunta) {
    responder_error(400, 'No hay una pregunta activa. Recarga la página.');
}
if ($pregunta['resuelta']) {
    responder_error(409, 'Esta pregunta ya fue respondida.');
}

$opcion = strtoupper($_POST['opcion'] ?? '');
if (!in_array($opcion, ['A', 'B', 'C', 'D'], true)) {
    responder_error(400, 'Opción no válida.');
}

$uid         = (int)$_SESSION['usuario_id'];
$es_correcta = ($opcion === $pregunta['correcta']);
$xp_ganado   = $es_correcta ? (int)$pregunta['xp'] : 0;
$es_int      = $es_correcta ? 1 : 0;

// Se guarda cada intento, bien o mal
$stmt = $conexion->prepare(
    "INSERT INTO historial_intentos (usuario_id, reino, enunciado, opcion_elegida, opcion_correcta, es_correcta, xp_ganado)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("iisssii", $uid, $pregunta['reino'], $pregunta['enunciado'], $opcion, $pregunta['correcta'], $es_int, $xp_ganado);
$stmt->execute();

// Datos actuales del alumno
$stmt = $conexion->prepare("SELECT puntos, nivel FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

$xp_total     = (int)$usuario['puntos'];
$nivel        = (int)$usuario['nivel'];
$subio_nivel  = false;

if ($es_correcta) {
    $xp_total   += $xp_ganado;
    $nivel_nuevo = 1 + intdiv($xp_total, 100);
    $subio_nivel = $nivel_nuevo > $nivel;
    $nivel       = $nivel_nuevo;

    $stmt = $conexion->prepare("UPDATE usuarios SET puntos = ?, nivel = ? WHERE id = ?");
    $stmt->bind_param("iii", $xp_total, $nivel, $uid);
    $stmt->execute();

    // La pregunta ya no puede volver a dar puntos
    $_SESSION['pregunta_actual']['resuelta'] = true;

    $mensaje = "¡Excelente razonamiento! Has acertado. Ganaste +$xp_ganado XP.";
    if ($subio_nivel) {
        $mensaje .= " ¡Subiste al nivel $nivel!";
    }
    echo json_encode([
        'correcta'    => true,
        'mensaje'     => $mensaje,
        'explicacion' => $pregunta['explicacion'],
        'xp_total'    => $xp_total,
        'nivel'       => $nivel,
    ]);
} else {
    // Se puede reintentar; no se revela cuál era la respuesta correcta
    echo json_encode([
        'correcta' => false,
        'mensaje'  => '¡Cerca, pero no! Revisa bien tu cálculo. Si tienes dudas, presiona APRENDE A ESTUDIAR.',
    ]);
}
