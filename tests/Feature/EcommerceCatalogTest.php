<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EcommerceCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_product_belongs_to_one_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $this->assertTrue($product->category->is($category));
        $this->assertTrue($category->products->contains($product));
    }

    public function test_catalog_content_uses_the_requested_locale_with_a_fallback(): void
    {
        $product = Product::factory()->create([
            'name_en' => 'Sulfuric Acid',
            'name_ar' => 'حمض الكبريتيك',
            'description_en' => 'English description',
            'description_ar' => null,
        ]);

        $this->assertSame('حمض الكبريتيك', $product->localized('name', 'ar'));
        $this->assertSame('English description', $product->localized('description', 'ar'));
    }

    public function test_category_image_and_product_thumbnail_are_single_file_collections(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $category->addMedia(UploadedFile::fake()->image('first-category.jpg'))->toMediaCollection('image');
        $category->addMedia(UploadedFile::fake()->image('second-category.jpg'))->toMediaCollection('image');
        $product->addMedia(UploadedFile::fake()->image('first-product.jpg'))->toMediaCollection('thumbnail');
        $product->addMedia(UploadedFile::fake()->image('second-product.jpg'))->toMediaCollection('thumbnail');

        $this->assertCount(1, $category->getMedia('image'));
        $this->assertSame('second-category.jpg', $category->getFirstMedia('image')?->file_name);
        $this->assertCount(1, $product->getMedia('thumbnail'));
        $this->assertSame('second-product.jpg', $product->getFirstMedia('thumbnail')?->file_name);
    }

    public function test_product_gallery_accepts_multiple_images(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $product->addMedia(UploadedFile::fake()->image('front.jpg'))->toMediaCollection('gallery');
        $product->addMedia(UploadedFile::fake()->image('back.jpg'))->toMediaCollection('gallery');

        $this->assertCount(2, $product->getMedia('gallery'));
    }
}
