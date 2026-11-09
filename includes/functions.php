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