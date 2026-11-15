<?php

declare(strict_types=1);

// Deportes: clave => ['name', 'category', 'unit'].
$sports = [
    'boxing'           => ['name' => 'Boxeo',            'category' => 'individual', 'unit' => 'rondas'],
    'muaythai'         => ['name' => 'Muay Thai',        'category' => 'individual', 'unit' => 'rondas'],
    'kickboxing'       => ['name' => 'Kickboxing',       'category' => 'individual', 'unit' => 'rondas'],
    'judo'             => ['name' => 'Judo',             'category' => 'individual', 'unit' => 'minutos'],
    'mma'              => ['name' => 'MMA',              'category' => 'individual', 'unit' => 'rondas'],
    'karate'           => ['name' => 'Karate',           'category' => 'individual', 'unit' => 'minutos'],
    'football'         => ['name' => 'Fútbol',           'category' => 'equipo',     'unit' => 'minutos'],
];

// Niveles competitivos: clave => texto que se ve.
$levels = [
    'beginner'      => 'Iniciación',
    'regional'      => 'Regional',
    'national'      => 'Nacional',
    'international' => 'Internacional',
];


// Usuarios de prueba
$users = [
    'anass' => [ //Anass1234
        'name'          => 'Anass',
        'password_hash' => '$2y$10$iR0GcsqW3IXOGSbTR3IffOrRcO97PwqHUfeNNTbgsp9d0CkVXnZGO',
        'sport'         => 'karate',
    ],
    'juan' => [ //Juan1234
        'name'          => 'Juan',
        'password_hash' => '$2y$10$hoejuQbmm4kDXgdtwnfFyOvcrie0c1sP0qvrZAOTn7E62thPY8uYC',
        'sport'         => 'judo',
    ],
    'maria' => [ //Maria1234
        'name'          => 'María',
        'password_hash' => '$2y$10$JnkV7GfNWwWyNYrQjaRdzeuBu5kiFXqzPYYWQhWdGwYz8kytmvY6m',
        'sport'         => 'mma',
    ],
];

// Opciones permitidas para las preferencias (Paso 5): clave => texto que se ve.
$themes = [
    'light' => 'Claro',
    'dark'  => 'Oscuro',
];

$weightUnits = [
    'kg' => 'Kilogramos (kg)',
    'lb' => 'Libras (lb)',
];

// Tipos de sesión de entrenamiento (Paso 6).
$trainingTypes = [
    'technique'   => 'Técnica',
    'strength'    => 'Fuerza',
    'endurance'   => 'Resistencia',
    'competition' => 'Competición',
    'recovery'    => 'Recuperación',
];