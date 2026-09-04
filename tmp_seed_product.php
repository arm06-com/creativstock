<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

$user = User::first();
if (! $user) {
    echo "No users found\n";
    exit(1);
}

$dir = storage_path('app/public/products/'.date('Y/m'));
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// 1x1 PNG
$png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAn8B9c3O9gAAAABJRU5ErkJggg==');
$filename = uniqid('seed_', true).'.png';
$path = 'products/'.date('Y/m').'/'.$filename;
file_put_contents(storage_path('app/public/'.$path), $png);

$product = Product::create([
    'user_id' => $user->id,
    'file_path' => $path,
    'file_name' => $filename,
    'mime_type' => 'image/png',
    'status' => Product::STATUS_UPLOADED,
]);

echo 'Created product id='.$product->id.' for user id='.$user->id.' file_path='.$product->file_path."\n";
