<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Inicio';

// Contador de visitas guardado en una cookie (dura 30 días).
if (isset($_COOKIE['visits'])) {
    $visits = filter_var($_COOKIE['visits'], FILTER_VALIDATE_INT);

    if ($visits === false) {
        $visits = 0;
    }

    $visits = $visits + 1;
} else {
    $visits = 1;
}

setAppCookie('visits', strval($visits), time() + COOKIE_LIFETIME);

$individuales = getSportsByCategory($sports, 'individual');
$equipo = getSportsByCategory($sports, 'equipo');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<?= index() ?>

<p>Visitas a esta página: <?= e($visits) ?></p>

<h2>Deportes individuales</h2>
<?= renderSportsHtml($individuales) ?>
<h2>Deportes de equipo</h2>
<?= renderSportsHtml($equipo) ?>
<?php
require_once __DIR__ . '/includes/footer.php';