<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

$pageTitle = 'Nuevo entrenamiento';

$errors = [];

// Valores del formulario (sticky).
$date = '';
$type = '';
$duration = '';
$rpe = '5';
$notes = '';
$hasInjury = false;
$injury = '';

// Los entrenamientos se guardan de forma temporal en la sesión (array de arrays).
if (!isset($_SESSION['trainings'])) {
    $_SESSION['trainings'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $type = $_POST['type'] ?? '';
    $duration = trim($_POST['duration'] ?? '');
    $rpe = trim($_POST['rpe'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $hasInjury = isset($_POST['has_injury']);
    $injury = trim($_POST['injury'] ?? '');

    $input = [
        'date'       => $date,
        'type'       => $type,
        'duration'   => $duration,
        'rpe'        => $rpe,
        'notes'      => $notes,
        'has_injury' => $hasInjury,
        'injury'     => $injury,
    ];

    $errors = validateTraining($input, $trainingTypes);

    // Si no hay errores, construimos el entrenamiento como array asociativo.
    if (count($errors) === 0) {
        $injuryText = '';
        if ($hasInjury) {
            $injuryText = $injury;
        }

        $_SESSION['trainings'][] = [
            'date'     => $date,
            'type'     => $type,
            'duration' => (int) $duration,
            'rpe'      => (int) $rpe,
            'notes'    => $notes,
            'injury'   => $injuryText,
        ];

        // Recargamos para vaciar el formulario y que F5 no lo reenvíe.
        header('Location: training.php');
        exit();
    }
}

$trainings = $_SESSION['trainings'];
$stats = getTrainingStats($trainings);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Nuevo entrenamiento</h1>

<?php if (count($errors) > 0): ?>
    <div class="error-box">
        <p>Revisa estos errores:</p>
        <ul>
            <?php foreach ($errors as $message): ?>
                <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="training.php" method="POST" novalidate>

    <p>
        <label for="date">Fecha:</label>
        <input type="date" id="date" name="date" value="<?= e($date) ?>">
    </p>

    <p>Tipo de sesión:</p>

    <?php foreach ($trainingTypes as $key => $label): ?>
        <?php
        $checked = '';
        if ($key === $type) {
            $checked = ' checked';
        }
        ?>
        <p>
            <input type="radio" name="type" value="<?= e($key) ?>"<?= $checked ?>>
            <?= e($label) ?>
        </p>
    <?php endforeach; ?>

    <p>
        <label for="duration">Duración (minutos):</label>
        <input type="number" id="duration" name="duration" value="<?= e($duration) ?>">
    </p>

    <p>
        <label for="rpe">Esfuerzo percibido (RPE, de 1 = muy fácil a 10 = máximo):</label>
        <input type="range" id="rpe" name="rpe" min="1" max="10" value="<?= e($rpe) ?>">
    </p>

    <p>
        <label for="notes">Sensaciones (máximo 200 caracteres):</label>
        <textarea id="notes" name="notes" rows="3" cols="40"><?= e($notes) ?></textarea>
    </p>

    <?php
    $injuryChecked = '';
    if ($hasInjury) {
        $injuryChecked = ' checked';
    }
    ?>
    <p>
        <input type="checkbox" name="has_injury" value="1"<?= $injuryChecked ?>>
        Tengo una lesión o molestia
    </p>

    <p>
        <label for="injury">Describe la lesión o molestia:</label>
        <input type="text" id="injury" name="injury" value="<?= e($injury) ?>">
    </p>

    <button type="submit">Guardar entrenamiento</button>

</form>

<h2>Entrenamientos registrados</h2>

<?php if ($stats['sessions'] === 0): ?>

    <p>Todavía no has registrado ningún entrenamiento.</p>

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
        </tr>

        <?php foreach ($trainings as $training): ?>
            <?php
            $injuryShown = 'No';
            if ($training['injury'] !== '') {
                $injuryShown = $training['injury'];
            }
            ?>
            <tr>
                <td><?= e($training['date']) ?></td>
                <td><?= e($trainingTypes[$training['type']]) ?></td>
                <td><?= e($training['duration']) ?></td>
                <td><?= e($training['rpe']) ?></td>
                <td><?= e(calculateLoad($training['duration'], $training['rpe'])) ?></td>
                <td><?= e($training['notes']) ?></td>
                <td><?= e($injuryShown) ?></td>
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

<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
