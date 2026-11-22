<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

$pageTitle = 'Historial';

$username = $_SESSION['user']['username'];

// Si el CSV no existe se crea; si no se puede abrir, avisamos con un mensaje amable.
$fileError = '';
$trainings = [];

if (prepareTrainingsFile(TRAININGS_FILE)) {
    // Solo leemos los entrenamientos del usuario que tiene la sesión iniciada.
    $trainings = readTrainings(TRAININGS_FILE, $username);
} else {
    $fileError = 'No podemos acceder a tu historial ahora mismo. Inténtalo de nuevo más tarde.';
}

// Botón "Descargar mi historial": enviamos un CSV solo con los datos del usuario.
if ($fileError === '' && isset($_GET['download'])) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="historial_' . $username . '.csv"');

    // php://output escribe directamente en la respuesta que recibe el navegador.
    $output = fopen('php://output', 'w');

    fputcsv($output, TRAININGS_HEADER);

    foreach ($trainings as $training) {
        // Escribimos las columnas en el orden de la cabecera (sin el id).
        $row = [];

        foreach (TRAININGS_HEADER as $column) {
            $row[] = $training[$column];
        }

        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// Filtro por tipo de sesión (GET): si no es un tipo permitido, mostramos todos.
$selectedType = $_GET['type'] ?? '';

if (!is_string($selectedType) || !isset($trainingTypes[$selectedType])) {
    $selectedType = '';
}

// Ordenamos por fecha, la más reciente primero.
usort($trainings, 'compareByDateDesc');

$shown = $trainings;

if ($selectedType !== '') {
    $shown = filterTrainingsByType($trainings, $selectedType);
}

// Las mismas funciones del Paso 6, ahora sobre lo que se está mostrando.
$stats = getTrainingStats($shown);
$summary = getSummaryByType($shown);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Historial</h1>

<?php if ($fileError !== ''): ?>

    <div class="error-box">
        <p><?= e($fileError) ?></p>
    </div>

<?php else: ?>

    <form action="history.php" method="GET">

        <p>Filtrar por tipo de sesión:</p>

        <?php
        $allChecked = '';
        if ($selectedType === '') {
            $allChecked = ' checked';
        }
        ?>
        <p>
            <input type="radio" name="type" value=""<?= $allChecked ?>>
            Todos
        </p>

        <?php foreach ($trainingTypes as $key => $label): ?>
            <?php
            $checked = '';
            if ($key === $selectedType) {
                $checked = ' checked';
            }
            ?>
            <p>
                <input type="radio" name="type" value="<?= e($key) ?>"<?= $checked ?>>
                <?= e($label) ?>
            </p>
        <?php endforeach; ?>

        <button type="submit">Filtrar</button>
        <button type="submit" name="download" value="1">Descargar mi historial</button>

    </form>

    <?php if ($stats['sessions'] === 0): ?>

        <p>No hay entrenamientos para mostrar.</p>

    <?php else: ?>

        <table>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Duración (min)</th>
                <th>RPE</th>
                <th>Carga</th>
                <th>Sensaciones</th>
                <th>Lesión</th>
                <th>Acciones</th>
            </tr>

            <?php foreach ($shown as $training): ?>
                <?php
                $injuryShown = 'No';
                if ($training['injury'] !== '') {
                    $injuryShown = $training['injury'];
                }
                ?>
                <tr>
                    <td><?= e($training['date']) ?></td>
                    <td><?= e($trainingTypes[$training['type']] ?? $training['type']) ?></td>
                    <td><?= e($training['duration']) ?></td>
                    <td><?= e($training['rpe']) ?></td>
                    <td><?= e(calculateLoad($training['duration'], $training['rpe'])) ?></td>
                    <td><?= e($training['notes']) ?></td>
                    <td><?= e($injuryShown) ?></td>
                    <td>
                        <form action="delete.php" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= e($training['id']) ?>">
                            <button type="submit">Borrar</button>
                        </form>
                        <!-- <a href="delete.php?id=<?= e($training['id']) ?>">Borrar</a> -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Estadísticas</h2>

        <ul>
            <li>Total de sesiones: <?= e($stats['sessions']) ?></li>
            <li>Minutos totales: <?= e($stats['minutes']) ?></li>
            <li>RPE medio: <?= e($stats['average_rpe']) ?></li>
            <li>Carga total: <?= e($stats['load']) ?></li>
        </ul>

        <h2>Resumen por tipo de sesión</h2>

        <table>
            <tr>
                <th>Tipo</th>
                <th>Sesiones</th>
                <th>Minutos</th>
            </tr>

            <?php foreach ($summary as $type => $data): ?>
                <tr>
                    <td><?= e($trainingTypes[$type] ?? $type) ?></td>
                    <td><?= e($data['sessions']) ?></td>
                    <td><?= e($data['minutes']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
