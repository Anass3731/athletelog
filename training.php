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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();

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

    // Si no hay errores, guardamos el entrenamiento al final del CSV.
    if (count($errors) === 0) {
        $injuryText = '';
        if ($hasInjury) {
            $injuryText = $injury;
        }

        // El orden tiene que ser el de la cabecera del CSV.
        $training = [
            $_SESSION['user']['username'],
            $date,
            $type,
            (int) $duration,
            (int) $rpe,
            $notes,
            $injuryText,
        ];

        if (saveTraining(TRAININGS_FILE, $training)) {
            // Vamos al historial para ver el entrenamiento y que F5 no lo reenvíe.
            header('Location: history.php');
            exit();
        }

        $errors['file'] = 'No hemos podido guardar el entrenamiento. Inténtalo de nuevo más tarde.';
    }
}

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

    <?= csrfField() ?>

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

<?php
require_once __DIR__ . '/includes/footer.php';
