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
    'anass' => [
        'name'          => 'Anass',
        'password_hash' => '$2y$10$GFpcrOfGg8aupXqwoOj9r.JNdjsJhoc59151fcGTKV.s1RLERwmHy',
        'sport'         => 'karate',
    ],
    'juan' => [
        'name'          => 'Juan',
        'password_hash' => '$2y$10$KldF5GYBIWgm4UzI4/VIiu9p/747sRXC23VW04KaifHjUw6E.bnEa',
        'sport'         => 'judo',
    ],
    'maria' => [
        'name'          => 'María',
        'password_hash' => '$2y$10$s0ORZrX0wLo685sWKkciaOnKCTQWTco09EJC6BOhhnQvFCX4KH0qq',
        'sport'         => 'mma',
    ],
];