<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Login';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Comprobamos usuario y contraseña con el hash de $users (data.php).
    if (isset($users[$username]) && password_verify($password, $users[$username]['password_hash'])) {
        // Cambiamos el id de sesión al autenticarse (evita fijación de sesión).
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'username' => $username,
            'name'     => $users[$username]['name'],
            'sport'    => $users[$username]['sport'],
        ];

        header('Location: index.php');
        exit();
    }

    // Mensaje genérico: no decimos cuál de los dos datos falló.
    $error = 'Usuario o contraseña incorrectos.';
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Iniciar sesión</h1>

<?php if ($error !== ''): ?>
    <div class="error-box">
        <p><?= e($error) ?></p>
    </div>
<?php endif; ?>

<form action="login.php" method="POST">

    <p>
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>">
    </p>

    <p>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password">
    </p>

    <button type="submit">Entrar</button>

</form>

<?php
require_once __DIR__ . '/includes/footer.php';