<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

requireLogin();

$pageTitle = 'Historial';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<h1>Historial</h1>

<p>Aquí aparecerá el historial de entrenamientos guardados.</p>

<?php
require_once __DIR__ . '/includes/footer.php';