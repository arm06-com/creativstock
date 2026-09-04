<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ensure PHP upload temp path is available for local development.
$tempUploadDir = realpath(__DIR__.'/../storage/tmp') ?: __DIR__.'/../storage/tmp';
if (! is_dir($tempUploadDir)) {
    mkdir($tempUploadDir, 0755, true);
}

@ini_set('upload_tmp_dir', $tempUploadDir);
@ini_set('upload_max_filesize', '10M');
@ini_set('post_max_size', '20M');
@ini_set('max_file_uploads', '20');
@ini_set('memory_limit', '256M');

putenv('TMP='.$tempUploadDir);
putenv('TMPDIR='.$tempUploadDir);
putenv('TEMP='.$tempUploadDir);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
