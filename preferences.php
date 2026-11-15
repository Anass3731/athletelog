<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

$pageTitle = 'Preferencias';

$errors = [];

// Valores del formulario: al entrar, los que hay guardados en las cookies.
$preferences = getPreferences($themes, $weightUnits);
$theme = $preferences['theme'];
$weightUnit = $preferences['weight_unit'];
$tips = $preferences['tips'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Botón "Restablecer": borramos las cookies poniendo su caducidad en el pasado.
    if (isset($_POST['reset'])) {
        setAppCookie('theme', '', time() - 3600);
        setAppCookie('weight_unit', '', time() - 3600);
        setAppCookie('tips', '', time() - 3600);

        header('Location: preferences.php');
        exit();
    }

    $theme = $_POST['theme'] ?? '';
    $weightUnit = $_POST['weight_unit'] ?? '';
    $tips = isset($_POST['tips']);

    // Solo se aceptan las opciones que existen en data.php.
    if (!isset($themes[$theme])) {
        $errors['theme'] = 'Elige un tema válido.';
    }

    if (!isset($weightUnits[$weightUnit])) {
        $errors['weight_unit'] = 'Elige una unidad de peso válida.';
    }

    if (count($errors) === 0) {
        $tipsValue = '0';
        if ($tips) {
            $tipsValue = '1';
        }

        setAppCookie('theme', $theme, time() + COOKIE_LIFETIME);
        setAppCookie('weight_unit', $weightUnit, time() + COOKIE_LIFETIME);
        setAppCookie('tips', $tipsValue, time() + COOKIE_LIFETIME);

        // Recargamos la página para que el tema nuevo se aplique ya.
        header('Location: preferences.php');
        exit();
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Preferencias</h1>

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

<form action="preferences.php" method="POST">

    <p>Tema:</p>

    <?php foreach ($themes as $key => $label): ?>
        <?php
        $checked = '';
        if ($key === $theme) {
            $checked = ' checked';
        }
        ?>
        <p>
            <input type="radio" name="theme" value="<?= e($key) ?>"<?= $checked ?>>
            <?= e($label) ?>
        </p>
    <?php endforeach; ?>

    <p>
        <label for="weight_unit">Unidad de peso:</label>
        <select name="weight_unit" id="weight_unit">
            <?php foreach ($weightUnits as $key => $label): ?>
                <?php
                $selected = '';
                if ($key === $weightUnit) {
                    $selected = ' selected';
                }
                ?>
                <option value="<?= e($key) ?>"<?= $selected ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <?php
    $tipsChecked = '';
    if ($tips) {
        $tipsChecked = ' checked';
    }
    ?>
    <p>
        <input type="checkbox" name="tips" value="1"<?= $tipsChecked ?>>
        Mostrar consejos
    </p>

    <button type="submit">Guardar preferencias</button>
    <button type="submit" name="reset" value="1">Restablecer preferencias</button>

</form>

<?php
require_once __DIR__ . '/includes/footer.php';
