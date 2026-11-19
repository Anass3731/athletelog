<?php
$file_route = __DIR__. '/trainings.csv';


// LOCK_EX : candado exclusivo. Solo uno escribe. Se usa al guardar.
// LOCK_SH : candado compartido. Varios pueden leer a la vez. Se usa al leer.
// LOCK_UN : quitar el candado.


// r Solo lectura Falla si no existe
// a Añade al final; crea si no existe Es el modo típico para guardar
// w Escribe desde cero Borra todo el contenido anterior
// c+ Lee y escribe; crea si no existe No borra el contenido


// FUNCION LEER LOS REGISTROS DEL FICHERO

function readTrainings(string $file, string $username): array {
    $result = []; //aqui guardare las filas del csv

    $h = fopen($file, 'r');

    if ($h === false) {
    # gestiono el error
    echo 'No se pudo abrir el ficher';
    exit;
    }

    // gestiono el operar con el fichero

    // Leer ficher
    flock($h, LOCK_SH); //Cojo la llave compartida, para que puedan leer otros sin escribir 
   
    $cabeceras = fgetcsv($h);

    while (($file = fgetcsv($h)) !== false) {
        $trainig = array_combine($cabeceras, $file);

        if ($file[0] == $username) {
            $result[] = $trainig;
        }

    } 

    flock($h, LOCK_UN); //devuelvo la llave 
    fclose($h);

    return $result;
}

//FUNCION UN REGISTRO EN EL CSV
function escribir_csv(string $file, array $registro): bool {

    // escribir un nuevo regisrto
    // "a" es append, añadir al final
    // "w" es borrar lñ que hay y escribir lo nuevo
    // "r" es leer
    $h = fopen($file, 'a');

    if ($h === false) {
        # gestiono el error
        echo 'No se pudo abrir el ficher';
        return false;
    }


    

    flock($h, LOCK_EX); //si cojo la lleva 
    fputcsv($h, $registro); //escribe en el fichero
    flock($h, LOCK_UN); //devuelvo la llave 
    fclose($h);
    
    return true;
}





$resultado = readTrainings($file_route, 'anass');

$nuevo_registro = ['Pepe','2026-05-06','endurance',60,8,'Entrenamiento de Kumite y resistencia aeróbica',''];

escribir_csv($file_route, $nuevo_registro);

if (escribir_csv($file_route, $nuevo_registro) === true) {
    echo '<br> Registro añadido'; 
} 

$resultado = readTrainings($file_route, 'anass');

var_dump($resultado);