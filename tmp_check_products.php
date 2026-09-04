<?php

$path = __DIR__.'/database/database.sqlite';
var_export(file_exists($path));
echo PHP_EOL;
$pdo = new PDO('sqlite:'.$path);
$stmt = $pdo->query('select id, user_id, file_path, status, created_at from products order by id desc limit 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_export($rows);
echo PHP_EOL;
