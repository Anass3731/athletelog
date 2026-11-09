<?php
declare(strict_types=1);

function renderSportsHtml(array $individuales): string
{
    //TO DO Heardoc
    $html = '<table>';
    $html .= '<tr><th>Nombre</th><th>Unidad</th></tr>';

    foreach ($individuales as $sport) {
        $html .= '<tr>'
            . '<td>' . e($sport['name']) . '</td>'
            . '<td>' . e($sport['unit']) . '</td>'
            . '</tr>';
    }

    return $html . '</table>';
}


function index(): string
{
    return <<<HTML
        <h1>Bienvenido a AthleteLog</h1>

        <p>
            Hola
            AthleteLog es un diario de entrenamiento para deportistas de contacto.
            Aquí podrás registrarte, anotar tus entrenamientos y consultar tu historial.
        </p>
    HTML;
}