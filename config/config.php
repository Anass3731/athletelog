<?php

declare(strict_types=1);

const APP_NAME = 'AthleteLog';
const APP_OWNER = 'Anass Banan Aoussar';

// Menú sin sesión (visitante).
const APP_MENU_GUEST = [
    'index.php'    => 'Inicio',
    'register.php' => 'Registro',
    'login.php'    => 'Login',
];

// Menú con sesión iniciada.
const APP_MENU_USER = [
    'index.php'       => 'Inicio',
    'training.php'    => 'Nuevo entrenamiento',
    'history.php'     => 'Historial',
    'preferences.php' => 'Preferencias',
];

// Las cookies duran 30 días (en segundos).
const COOKIE_LIFETIME = 30 * 24 * 60 * 60;