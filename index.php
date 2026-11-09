<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Inicio';

$individuales = getSportsByCategory($sports, 'individual');
$equipo = getSportsByCategory($sports, 'equipo');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<?= index() ?>

<h3>indi</h3>
<?=  renderSportsHtml($individuales) ?>
<h3>eq</h3>
<?= renderSportsHtml($equipo) ?>
<?php
require_once __DIR__ . '/includes/footer.php';