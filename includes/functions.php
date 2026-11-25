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

// ===== Paso 7: ficheros CSV =====

// Prepara el CSV: si no existe (o está vacío) lo crea con su cabecera.
// Devuelve false si no se puede crear, leer o escribir (la página enseña un mensaje amable).
function prepareTrainingsFile(string $file): bool
{
    $folder = dirname($file);

    if (!file_exists($file) || filesize($file) === 0) {
        // Si la carpeta no existe o no se puede escribir, no seguimos (así no sale ningún warning).
        if (!is_dir($folder) || !is_writable($folder)) {
            return false;
        }

        $h = fopen($file, 'a');

        if ($h === false) {
            return false;
        }

        flock($h, LOCK_EX);
        fputcsv($h, TRAININGS_HEADER);
        flock($h, LOCK_UN);
        fclose($h);
    }

    return is_readable($file) && is_writable($file);
}

// Lee el CSV y devuelve solo los entrenamientos de $username (array de arrays asociativos).
function readTrainings(string $file, string $username): array
{
    $result = [];

    if (!prepareTrainingsFile($file)) {
        return $result;
    }

    $h = fopen($file, 'r');

    if ($h === false) {
        return $result;
    }

    // LOCK_SH: candado compartido, varios pueden leer a la vez.
    flock($h, LOCK_SH);

    // La primera línea es la cabecera: la leemos y la guardamos para array_combine.
    $header = fgetcsv($h);
    $number = 0;

    if ($header !== false) {
        while (($row = fgetcsv($h)) !== false) {
            // Saltamos las filas vacías o rotas (con distinto número de columnas que la cabecera).
            if (count($row) !== count($header)) {
                continue;
            }

            // Número de fila del CSV (sin contar la cabecera): sirve como id para borrar.
            $number++;

            $training = array_combine($header, $row);

            if ($training['username'] === $username) {
                // En el CSV todo es texto: pasamos a número lo que luego se calcula.
                $training['duration'] = (int) $training['duration'];
                $training['rpe'] = (int) $training['rpe'];
                $training['id'] = $number;

                $result[] = $training;
            }
        }
    }

    flock($h, LOCK_UN);
    fclose($h);

    return $result;
}

// Añade un entrenamiento al final del CSV. Devuelve false si no se ha podido guardar.
function saveTraining(string $file, array $training): bool
{
    if (!prepareTrainingsFile($file)) {
        return false;
    }

    // "a" = append: añade al final sin borrar lo que ya hay.
    $h = fopen($file, 'a');

    if ($h === false) {
        return false;
    }

    // LOCK_EX: candado exclusivo, mientras escribo nadie más puede escribir.
    flock($h, LOCK_EX);
    fputcsv($h, $training);
    flock($h, LOCK_UN);
    fclose($h);

    return true;
}

// Compara dos entrenamientos por fecha para usort (la más reciente primero).
function compareByDateDesc(array $a, array $b): int
{
    return strcmp($b['date'], $a['date']);
}

// Devuelve solo los entrenamientos de un tipo de sesión.
function filterTrainingsByType(array $trainings, string $type): array
{
    $result = [];

    foreach ($trainings as $training) {
        if ($training['type'] === $type) {
            $result[] = $training;
        }
    }

    return $result;
}

// Opcional (nota extra): resumen por tipo de sesión con un array acumulador.
function getSummaryByType(array $trainings): array
{
    $summary = [];

    foreach ($trainings as $training) {
        $type = $training['type'];

        // Si es la primera vez que vemos este tipo, empezamos sus contadores a 0.
        if (!isset($summary[$type])) {
            $summary[$type] = ['sessions' => 0, 'minutes' => 0];
        }

        $summary[$type]['sessions'] = $summary[$type]['sessions'] + 1;
        $summary[$type]['minutes'] = $summary[$type]['minutes'] + $training['duration'];
    }

    return $summary;
}

// Borra el entrenamiento número $id (fila del CSV) si es del usuario $username.
// Devuelve true solo si ha borrado algo.
function deleteTraining(string $file, int $id, string $username): bool
{
    if (!prepareTrainingsFile($file)) {
        return false;
    }

    // "c+" = leer y escribir sin vaciar el fichero al abrirlo.
    $h = fopen($file, 'c+');

    if ($h === false) {
        return false;
    }

    flock($h, LOCK_EX);

    $header = fgetcsv($h);
    $rows = [];
    $number = 0;
    $deleted = false;

    if ($header !== false) {
        while (($row = fgetcsv($h)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            $number++;

            // La fila se borra solo si es la que toca Y es de este usuario.
            if ($number === $id && $row[0] === $username) {
                $deleted = true;
            } else {
                $rows[] = $row;
            }
        }
    }

    // Reescribimos el fichero entero sin la fila borrada.
    if ($deleted) {
        ftruncate($h, 0);
        rewind($h);
        fputcsv($h, $header);

        foreach ($rows as $row) {
            fputcsv($h, $row);
        }
    }

    flock($h, LOCK_UN);
    fclose($h);

    return $deleted;
}

// ===== Paso 8: CSRF =====

// Crea el token de la sesión (solo si todavía no existe) y lo devuelve.
function generateCsrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        // 32 bytes aleatorios seguros, pasados a texto hexadecimal (64 caracteres).
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Devuelve el campo oculto con el token, listo para pegarlo dentro de un <form>.
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(generateCsrfToken()) . '">';
}

// Comprueba que el token recibido es el de la sesión. hash_equals compara en tiempo constante.
function verifyCsrfToken(?string $token): bool
{
    if ($token === null || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

// Se llama al principio de cada POST: si el token no vale, 403 y no se procesa nada más.
function requireCsrf(): void
{
    $token = $_POST['csrf_token'] ?? null;

    if (!is_string($token) || !verifyCsrfToken($token)) {
        http_response_code(403);
        echo '<h1>403 - Petición rechazada</h1>';
        echo '<p>El formulario no incluye un token de seguridad válido. Vuelve atrás, recarga la página e inténtalo de nuevo.</p>';
        exit();
    }
}


function readUsers (string $file): array {

    if (!is_file($file) || !is_readable($file)) {
        return [];
    }

    $fichero = fopen ($file, 'r');
    if ($fichero === false) {
        return [];
    }

    $cabecera = fgetcsv($fichero, null, ',', '"', '');

    $usuarios[];

    while ($fila = fgetcsv($fichero, null, '')) {
        # code...
    }
    return $usuarios;
}