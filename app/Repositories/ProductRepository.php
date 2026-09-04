<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;

class ProductRepository
{
    /**
     * Store uploaded files and create Product records.
     *
     * @param  array<int, UploadedFile>  $files
     * @param  Authenticatable  $user
     * @return array<int, Product>
     */
    public function storeUploadedFiles(array $files, $user): array
    {
        $created = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('products/'.date('Y/m'), 'public');

            $created[] = Product::create([
                'user_id' => $user->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'status' => Product::STATUS_UPLOADED,
            ]);
        }

        return $created;
    }
}
