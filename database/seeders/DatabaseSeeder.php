<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Products;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected array $categories = [
        [
            'key' => 'men',
            'name' => 'ملابس رجالية',
            'description' => 'منتجات رجالية',
        ],
        [
            'key' => 'women',
            'name' => 'ملابس نسائية',
            'description' => 'منتجات نسائية',
        ],
        [
            'key' => 'kids',
            'name' => 'ملابس أطفال',
            'description' => 'منتجات للأطفال',
        ],
    ];

    protected array $productImages = [
        'storage/products/men_shirt1.jpg',
        'storage/products/men_shirt2.jpg',
        'storage/products/women_dress1.jpg',
        'storage/products/women_dress2.jpg',
        'storage/products/kids_shirt1.jpg',
        'storage/products/kids_shirt2.jpg',
    ];

    protected array $products = [
        [
            'name' => 'قميص رجالي كلاسيكي',
            'description' => 'قميص رجالي أنيق مصنوع من القطن الطبيعي',
            'price' => 46,
            'stock_quantity' => 25,
            'category' => 'men',
            'image' => 'storage/products/men_shirt1.jpg',
        ],
        [
            'name' => 'قميص رجالي رسمي',
            'description' => 'قميص رسمي مناسب للمناسبات والعمل',
            'price' => 60,
            'stock_quantity' => 30,
            'category' => 'men',
            'image' => 'storage/products/men_shirt2.jpg',
        ],
        [
            'name' => 'فستان نسائي أنيق',
            'description' => 'فستان نسائي عصري بتصميم جذاب',
            'price' => 80,
            'stock_quantity' => 20,
            'category' => 'women',
            'image' => 'storage/products/women_dress1.jpg',
        ],
        [
            'name' => 'فستان نسائي كاجوال',
            'description' => 'فستان مريح للاستخدام اليومي',
            'price' => 56,
            'stock_quantity' => 15,
            'category' => 'women',
            'image' => 'storage/products/women_dress2.jpg',
        ],
        [
            'name' => 'قميص أطفال ملون',
            'description' => 'قميص أطفال مريح بألوان زاهية',
            'price' => 30,
            'stock_quantity' => 40,
            'category' => 'kids',
            'image' => 'storage/products/kids_shirt1.jpg',
        ],
        [
            'name' => 'قميص أطفال رياضي',
            'description' => 'قميص رياضي للأطفال مناسب للنشاطات',
            'price' => 35,
            'stock_quantity' => 35,
            'category' => 'kids',
            'image' => 'storage/products/kids_shirt2.jpg',
        ],
    ];

    public function run()
    {
        if (Products::count() > 0) {
            $this->fillMissingProductImages();
            return;
        }

        $categories = collect($this->categories)->mapWithKeys(function (array $category) {
            $model = Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'parent_id' => null,
                ]
            );

            return [$category['key'] => $model];
        });

        foreach ($this->products as $product) {
            Products::firstOrCreate(
                ['name' => $product['name']],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock_quantity' => $product['stock_quantity'],
                    'image' => $product['image'],
                    'category_id' => $categories[$product['category']]->id,
                ]
            );
        }

        $this->call(SecurityBlocksSeeder::class);
    }

    protected function fillMissingProductImages(): void
    {
        $imageCount = count($this->productImages);

        Products::query()
            ->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })
            ->get()
            ->values()
            ->each(function (Products $product, int $index) use ($imageCount) {
                $product->update([
                    'image' => $this->productImages[$index % $imageCount],
                ]);
            });
    }
}
