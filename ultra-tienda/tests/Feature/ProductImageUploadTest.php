<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'superadmin']);
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => 'secreta-123',
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);

        $this->category = Category::create(['name' => 'Libros', 'orden' => 1]);
    }

    private function makeProductWithImage(): Product
    {
        Storage::disk('public')->put('products/actual.jpg', 'contenido-previo');

        return Product::create([
            'category_id' => $this->category->id,
            'name' => 'Libro con imagen',
            'price' => 25.5,
            'image' => 'products/actual.jpg',
        ]);
    }

    public function test_product_image_is_uploaded_to_the_public_disk_on_store(): void
    {
        Storage::fake('public');

        $this->post('/products', [
            'category_id' => $this->category->id,
            'name' => 'Producto con imagen',
            'price' => 10,
            'image' => UploadedFile::fake()->create('producto.jpg', 120, 'image/jpeg'),
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $product = Product::firstWhere('name', 'Producto con imagen');
        $this->assertNotNull($product);
        $this->assertStringStartsWith('products/', $product->image);
        Storage::disk('public')->assertExists($product->image);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee($product->image_url, false);
    }

    public function test_invalid_product_image_is_rejected_on_store(): void
    {
        Storage::fake('public');

        $this->post('/products', [
            'category_id' => $this->category->id,
            'name' => 'Producto inválido',
            'price' => 10,
            'image' => UploadedFile::fake()->create('documento.txt', 5, 'text/plain'),
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('products', ['name' => 'Producto inválido']);
    }

    public function test_product_image_is_replaced_and_the_old_file_is_deleted_on_update(): void
    {
        Storage::fake('public');

        $product = $this->makeProductWithImage();
        $oldImage = $product->image;

        $this->put("/products/{$product->id}", [
            'category_id' => $this->category->id,
            'name' => $product->name,
            'price' => 30,
            'image' => UploadedFile::fake()->create('nueva.jpg', 80, 'image/jpeg'),
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $product->refresh();

        $this->assertNotSame($oldImage, $product->image);
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_product_image_can_be_removed_with_the_remove_image_flag(): void
    {
        Storage::fake('public');

        $product = $this->makeProductWithImage();
        $oldImage = $product->image;

        $this->put("/products/{$product->id}", [
            'category_id' => $this->category->id,
            'name' => $product->name,
            'price' => 30,
            'remove_image' => 1,
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $this->assertNull($product->fresh()->image);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_updating_without_a_new_file_keeps_the_current_image(): void
    {
        Storage::fake('public');

        $product = $this->makeProductWithImage();

        $this->put("/products/{$product->id}", [
            'category_id' => $this->category->id,
            'name' => 'Nombre actualizado',
            'price' => 30,
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $this->assertSame('products/actual.jpg', $product->fresh()->image);
        Storage::disk('public')->assertExists('products/actual.jpg');
    }

    public function test_deleting_a_product_removes_its_stored_image(): void
    {
        Storage::fake('public');

        $product = $this->makeProductWithImage();

        $this->delete("/products/{$product->id}")
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/actual.jpg');
    }

    public function test_image_url_accessor_resolves_uploaded_files_and_external_urls(): void
    {
        Storage::fake('public');

        $local = $this->makeProductWithImage();
        $this->assertSame(Storage::disk('public')->url('products/actual.jpg'), $local->image_url);

        $external = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Producto externo',
            'price' => 15,
            'image' => 'https://cdn.example.com/producto.png',
        ]);
        $this->assertSame('https://cdn.example.com/producto.png', $external->image_url);

        $without = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Producto sin imagen',
            'price' => 15,
        ]);
        $this->assertNull($without->image_url);
    }
}
