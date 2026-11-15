<?php
declare(strict_types=1);

// Escapa el texto antes de imprimirlo
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Filtra los deportes por categoría
function getSportsByCategory(array $sports, string $category): array
{
    $result = [];

    foreach ($sports as $key => $sport) {
        if ($sport['category'] === $category) {
            $result[$key] = $sport;
        }
    }

    return $result;
}

// ===== Paso 4: sesiones =====

// Si no hay usuario en la sesión, manda a login.php y para el script.
function requireLogin(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

// ===== Paso 5: cookies =====

// Todas las cookies de la app se crean con las mismas opciones de seguridad.
// httponly: JavaScript no puede leerla. samesite: no se envía desde otras webs.
function setAppCookie(string $name, string $value, int $expires): void
{
    setcookie($name, $value, [
        'expires'  => $expires,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// Lee las preferencias de las cookies. Si no existen o no son válidas,
// usa el valor por defecto (nunca nos fiamos de lo que manda el navegador).
function getPreferences(array $themes, array $weightUnits): array
{
    $theme = $_COOKIE['theme'] ?? 'light';
    if (!isset($themes[$theme])) {
        $theme = 'light';
    }

    $unit = $_COOKIE['weight_unit'] ?? 'kg';
    if (!isset($weightUnits[$unit])) {
        $unit = 'kg';
    }

    $tips = isset($_COOKIE['tips']) && $_COOKIE['tips'] === '1';

    return [
        'theme'       => $theme,
        'weight_unit' => $unit,
        'tips'        => $tips,
    ];
}

// ===== Paso 6: entrenamientos =====

// Devuelve un array con los errores (vacío si todo está bien).
function validateTraining(array $input, array $trainingTypes): array
{
    $errors = [];

    // Fecha válida (strtotime no la entiende o la cambia) y no futura.
    $timestamp = strtotime($input['date']);
    if ($timestamp === false || date('Y-m-d', $timestamp) !== $input['date']) {
        $errors['date'] = 'La fecha no es válida.';
    } elseif ($input['date'] > date('Y-m-d')) {
        $errors['date'] = 'La fecha no puede ser futura.';
    }

    if (!isset($trainingTypes[$input['type']])) {
        $errors['type'] = 'Elige un tipo de sesión.';
    }

    $duration = filter_var($input['duration'], FILTER_VALIDATE_INT);
    if ($duration === false || $duration < 5 || $duration > 360) {
        $errors['duration'] = 'La duración debe estar entre 5 y 360 minutos.';
    }

    $rpe = filter_var($input['rpe'], FILTER_VALIDATE_INT);
    if ($rpe === false || $rpe < 1 || $rpe > 10) {
        $errors['rpe'] = 'El esfuerzo (RPE) debe ser un número entre 1 y 10.';
    }

    if (strlen($input['notes']) > 200) {
        $errors['notes'] = 'Las sensaciones pueden tener 200 caracteres como máximo.';
    }

    // Si marca lesión, tiene que describirla.
    if ($input['has_injury'] && $input['injury'] === '') {
        $errors['injury'] = 'Describe la lesión o molestia.';
    }

    return $errors;
}

// Carga de una sesión = duración × esfuerzo percibido.
function calculateLoad(int $duration, int $rpe): int
{
    return $duration * $rpe;
}

// Estadísticas de todos los entrenamientos: sesiones, minutos, RPE medio y carga.
function getTrainingStats(array $trainings): array
{
    $sessions = count($trainings);
    $minutes = 0;
    $rpeSum = 0;
    $load = 0;

    foreach ($trainings as $training) {
        $minutes = $minutes + $training['duration'];
        $rpeSum = $rpeSum + $training['rpe'];
        $load = $load + calculateLoad($training['duration'], $training['rpe']);
    }

    $averageRpe = 0;
    if ($sessions > 0) {
        $averageRpe = round($rpeSum / $sessions, 1);
    }

    return [
        'sessions'    => $sessions,
        'minutes'     => $minutes,
        'average_rpe' => $averageRpe,
        'load'        => $load,
    ];
}