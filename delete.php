<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

// Borrar solo se permite por POST: si alguien entra por GET (enlace o <img>), no se hace nada.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<h1>405 - Método no permitido</h1>';
    echo '<p>Para borrar un entrenamiento usa el botón "Borrar" del historial.</p>';
    exit();
}

requireCsrf();

$id = $_POST['id'] ?? '';

// El id tiene que ser un número entero positivo.
if (is_string($id) && ctype_digit($id)) {
    deleteTraining(TRAININGS_FILE, (int) $id, $_SESSION['user']['username']);
}

header('Location: history.php');
exit();


// declare(strict_types=1);

// require_once __DIR__ . '/includes/bootstrap.php';

// requireLogin();

// // VERSIÓN MAL HECHA: borra por GET sin ninguna comprobación.
// $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);

// if ($id !== false) {
//     deleteTraining(TRAININGS_FILE, $id, $_SESSION['user']['username']);
// }

// header('Location: history.php');
// exit();
