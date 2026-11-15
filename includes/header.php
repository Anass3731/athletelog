<?php

declare(strict_types=1);

// $pageTitle la define cada página antes de incluir este fichero.

// La cookie del tema decide la clase del <body> (theme-light o theme-dark).
$headerPreferences = getPreferences($themes, $weightUnits);
$bodyClass = 'theme-' . $headerPreferences['theme'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">