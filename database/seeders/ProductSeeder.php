<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contributor = User::query()->firstWhere('role', User::ROLE_CONTRIBUTOR)
            ?? User::query()->firstOrCreate(
                ['email' => 'contributor@example.com'],
                ['name' => 'Contributor User', 'password' => bcrypt('test12341'), 'role' => User::ROLE_CONTRIBUTOR, 'email_verified_at' => now(), 'active' => true],
            );

        $categories = collect([
            ['name' => 'Photography', 'slug' => 'photography', 'description' => 'Editorial and lifestyle photography.'],
            ['name' => 'Illustration', 'slug' => 'illustration', 'description' => 'Illustrations for thoughtful creative work.'],
            ['name' => 'Templates', 'slug' => 'templates', 'description' => 'Flexible templates for your next project.'],
            ['name' => 'Textures', 'slug' => 'textures', 'description' => 'Textures and visual details for richer compositions.'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::query()->firstOrCreate(['slug' => $category['slug']], $category),
        ]);

        $imagePath = 'products/2026/08/seed_6a7c374f971808.89376640.png';

        $products = [
            ['title' => 'Quiet Morning', 'category' => 'photography'],
            ['title' => 'Open Forms', 'category' => 'illustration'],
            ['title' => 'Clean Canvas', 'category' => 'templates'],
            ['title' => 'Paper Grain', 'category' => 'textures'],
            ['title' => 'Soft Focus', 'category' => 'photography'],
        ];

        foreach ($products as $asset) {
            Product::query()->updateOrCreate(
                ['user_id' => $contributor->id, 'title' => $asset['title']],
                [
                    'category_id' => $categories[$asset['category']]->id,
                    'description' => 'A curated creative asset for your next project.',
                    'tags' => ['creative', 'stock'],
                    'asset_type' => 'Image',
                    'status' => Product::STATUS_APPROVED,
                    'file_path' => $imagePath,
                    'file_name' => basename($imagePath),
                    'mime_type' => 'image/png',
                ],
            );
        }
    }
}
