<?php

declare(strict_types=1);

// Nombre del fichero de la página actual (ej. index.php).
$paginaActual = basename($_SERVER['PHP_SELF']);

$menu = '';

foreach (APP_MENU as $file => $label) {
    if ($file === $paginaActual) {
        $menu .= '<li><a href="' . e($file) . '" class="active">' . e($label) . '</a></li>';
    } else {
        $menu .= '<li><a href="' . e($file) . '">' . e($label) . '</a></li>';
    }
}

echo <<<HTML
<header>
    <nav>
        <ul>
            {$menu}
        </ul>
    </nav>
</header>
HTML;