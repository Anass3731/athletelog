<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Registro';

$days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// $errors guarda un mensaje por cada campo que falla.
$errors = [];
$success = false;

// Valores del formulario: vacíos al entrar, y lo escrito si se envía (sticky).
$name = '';
$username = '';
$email = '';
$age = '';
$sport = '';
$level = '';
$selectedDays = [];
$accept = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $sport = $_POST['sport'] ?? '';
    $level = $_POST['level'] ?? '';
    $selectedDays = $_POST['days'] ?? [];
    $accept = isset($_POST['accept']);

    // Las contraseñas nunca se vuelven a pintar en el formulario.
    $password = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    // Validamos en el servidor, aunque el HTML ya tenga su propia validación.
    if (strlen($name) < 3 || strlen($name) > 50) {
        $errors['name'] = 'El nombre debe tener entre 3 y 50 caracteres.';
    }

    if (preg_match('/^[A-Za-z0-9_]{4,15}$/', $username) !== 1) {
        $errors['username'] = 'El usuario solo puede tener letras, números y _ (entre 4 y 15 caracteres).';
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'El email no es válido.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $passwordRepeat) {
        $errors['password'] = 'Las dos contraseñas no son iguales.';
    }

    $ageNumber = filter_var($age, FILTER_VALIDATE_INT);
    if ($ageNumber === false || $ageNumber < 16 || $ageNumber > 60) {
        $errors['age'] = 'La edad debe estar entre 16 y 60 años.';
    }

    // El deporte y el nivel tienen que existir como clave en los arrays de data.php.
    if (!isset($sports[$sport])) {
        $errors['sport'] = 'Elige un deporte de la lista.';
    }

    if (!isset($levels[$level])) {
        $errors['level'] = 'Elige un nivel competitivo.';
    }

    if (count($selectedDays) === 0) {
        $errors['days'] = 'Marca al menos un día de entrenamiento.';
    }

    if (!$accept) {
        $errors['accept'] = 'Debes aceptar el tratamiento de tus datos.';
    }

    // Si no hay errores, todo está bien. Todavía no se guarda en ningún sitio.
    if (count($errors) === 0) {
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Registro de deportista</h1>

<?php if ($success): ?>

    <div class="success-box">
        <p>¡Registro correcto! Estos son tus datos:</p>
        <ul>
            <li>Nombre: <?= e($name) ?></li>
            <li>Usuario: <?= e($username) ?></li>
            <li>Email: <?= e($email) ?></li>
            <li>Edad: <?= e($age) ?></li>
            <li>Deporte: <?= e($sports[$sport]['name']) ?></li>
            <li>Nivel: <?= e($levels[$level]) ?></li>
            <li>Días de entrenamiento: <?= e(implode(', ', $selectedDays)) ?></li>
        </ul>
    </div>

<?php else: ?>

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

    <form action="register.php" method="POST" novalidate>

        <p>
            <label for="name">Nombre completo:</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>">
        </p>

        <p>
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" value="<?= e($username) ?>">
        </p>

        <p>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>">
        </p>

        <p>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password">
        </p>

        <p>
            <label for="password_repeat">Repetir contraseña:</label>
            <input type="password" id="password_repeat" name="password_repeat">
        </p>

        <p>
            <label for="age">Edad:</label>
            <input type="number" id="age" name="age" value="<?= e($age) ?>">
        </p>

        <p>
            <label for="sport">Deporte principal:</label>
            <select name="sport" id="sport">
                <option value="">Selecciona un deporte</option>

                <?php foreach ($sports as $key => $item): ?>
                    <?php
                    $selected = '';
                    if ($key === $sport) {
                        $selected = ' selected';
                    }
                    ?>
                    <option value="<?= e($key) ?>"<?= $selected ?>>
                        <?= e($item['name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </p>

        <p>Nivel competitivo:</p>

        <?php foreach ($levels as $key => $label): ?>
            <?php
            $checked = '';
            if ($key === $level) {
                $checked = ' checked';
            }
            ?>
            <p>
                <input type="radio" name="level" value="<?= e($key) ?>"<?= $checked ?>>
                <?= e($label) ?>
            </p>
        <?php endforeach; ?>

        <p>Días de entrenamiento:</p>

        <?php foreach ($days as $day): ?>
            <?php
            $checked = '';
            if (in_array($day, $selectedDays)) {
                $checked = ' checked';
            }
            ?>
            <p>
                <input type="checkbox" name="days[]" value="<?= e($day) ?>"<?= $checked ?>>
                <?= e($day) ?>
            </p>
        <?php endforeach; ?>

        <?php
        $acceptChecked = '';
        if ($accept) {
            $acceptChecked = ' checked';
        }
        ?>
        <p>
            <input type="checkbox" name="accept" value="yes"<?= $acceptChecked ?>>
            Acepto el tratamiento de mis datos.
        </p>

        <button type="submit">Registrarme</button>

    </form>

<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';