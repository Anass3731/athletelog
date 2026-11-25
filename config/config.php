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

// ===== Paso 7: fichero CSV =====

// Ruta del fichero donde se guardan los entrenamientos (con __DIR__ para que no falle).
const TRAININGS_FILE = __DIR__ . '/../data/trainings.csv';

// Cabecera del CSV: el orden de las columnas.
const TRAININGS_HEADER = ['username', 'date', 'type', 'duration', 'rpe', 'notes', 'injury'];


const USERS_FILE = __DIR__ . '/../data/users.csv';