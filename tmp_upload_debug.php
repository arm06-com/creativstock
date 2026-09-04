<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
echo 'upload_tmp_dir='.ini_get('upload_tmp_dir').PHP_EOL;
echo 'upload_max_filesize='.ini_get('upload_max_filesize').PHP_EOL;
echo 'post_max_size='.ini_get('post_max_size').PHP_EOL;
echo 'file_uploads='.ini_get('file_uploads').PHP_EOL;
echo 'memory_limit='.ini_get('memory_limit').PHP_EOL;
echo 'tmp='.getenv('TMP').PHP_EOL;
echo 'temp='.getenv('TEMP').PHP_EOL;
echo 'cwd='.getcwd().PHP_EOL;
$pdo = new PDO('sqlite:'.__DIR__.'/database/database.sqlite');
$rows = $pdo->query('select count(*) as total from products')->fetch(PDO::FETCH_ASSOC);
echo 'products='.($rows['total'] ?? 0).PHP_EOL;
$sample = $pdo->query('select id, file_path, file_name, status, created_at from products order by id desc limit 5')->fetchAll(PDO::FETCH_ASSOC);
var_export($sample);
echo PHP_EOL;
