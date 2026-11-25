<?php 
declare(strict_types=1);

$textCsv = '';
$users= [
    ['anass', 'Anass', 'Anass1234', 'karate'],
    ['juan', 'Juan', 'Juan1234', 'judo'],
    ['maria', 'María', 'Maria1234', 'mma'],


];

header('Content_Type: text/plain; charset=');

$textCsv = "username, name,password_hash,sport\n";

foreach ($users as $user) {
    $user[2] = password_hash($user[2], PASSWORD_DEFAULT);

    $textCsv .= implode(',',[
                $user[0],$user[1], '"' . $user[2] . '"', $user[3]]) . "\n";
}

// echo $textCsv; 


$fp = fopen('data/users.csv', 'w');
flock($fp, LOCK_EX);
// fputcsv($fp, $textCsv);
fwrite($fp,$textCsv);
flock($fp, LOCK_UN);
fclose($fp);