<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(string $name = 'Audio', bool $active = true): Category
    {
        return Category::create([
            'name' => $name,
            'description' => "Categoría {$name}",
            'orden' => 1,
            'is_active' => $active,
        ]);
    }

    private function makeProduct(Category $category, array $attributes = []): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => 'Producto de prueba',
            'description' => 'Descripción de prueba',
            'price' => 100000,
            'disponible' => true,
            'is_active' => true,
            ...$attributes,
        ]);
    }

    public function test_categories_endpoint_returns_active_categories_with_product_counts(): void
    {
        $audio = $this->makeCategory('Audio');
        $this->makeProduct($audio);
        $this->makeProduct($audio, ['name' => 'Segundo producto']);
        $this->makeProduct($audio, ['name' => 'Producto inactivo', 'is_active' => false]);

        $this->makeCategory('Categoría oculta', active: false);

        $this->getJson('/api/store/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Audio')
            ->assertJsonPath('data.0.products_count', 2);
    }

    public function test_products_endpoint_returns_only_active_products_from_active_categories(): void
    {
        $audio = $this->makeCategory('Audio');
        $visible = $this->makeProduct($audio);
        $this->makeProduct($audio, ['name' => 'Producto inactivo', 'is_active' => false]);

        $hidden = $this->makeCategory('Categoría oculta', active: false);
        $this->makeProduct($hidden, ['name' => 'Producto de categoría oculta']);

        $response = $this->getJson('/api/store/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id)
            ->assertJsonPath('data.0.category.name', 'Audio')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'description',
                    'price',
                    'currency',
                    'image',
                    'available_for_sale',
                    'category' => ['id', 'name'],
                    'created_at',
                ]],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $this->assertSame(100000.0, (float) $response->json('data.0.price'));
    }

    public function test_products_endpoint_filters_by_search_price_and_category(): void
    {
        $audio = $this->makeCategory('Audio');
        $hogar = $this->makeCategory('Hogar');

        $this->makeProduct($audio, ['name' => 'Audífonos Bluetooth', 'price' => 150000]);
        $this->makeProduct($audio, ['name' => 'Parlante portátil', 'price' => 350000]);
        $this->makeProduct($hogar, ['name' => 'Licuadora', 'price' => 200000]);

        $this->getJson('/api/store/products?search=bluetooth')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Audífonos Bluetooth');

        $this->getJson('/api/store/products?min_price=180000&max_price=400000')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson("/api/store/products?category_id={$hogar->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Licuadora');

        $this->getJson('/api/store/products?sort=price_desc')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Parlante portátil');
    }

    public function test_product_detail_endpoint_returns_404_for_inactive_products(): void
    {
        $category = $this->makeCategory();
        $active = $this->makeProduct($category);
        $inactive = $this->makeProduct($category, ['name' => 'Oculto', 'is_active' => false]);

        $this->getJson("/api/store/products/{$active->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $active->id);

        $this->getJson("/api/store/products/{$inactive->id}")->assertNotFound();
    }

    public function test_order_endpoint_creates_order_with_server_side_totals(): void
    {
        $category = $this->makeCategory();
        $headphones = $this->makeProduct($category, ['name' => 'Audífonos', 'price' => 100000]);
        $speaker = $this->makeProduct($category, ['name' => 'Parlante', 'price' => 50000]);

        $response = $this->postJson('/api/store/orders', [
            'customer_name' => 'Laura Pérez',
            'customer_email' => 'laura@example.com',
            'customer_phone' => '3001112222',
            'customer_address' => 'Calle 10 # 25-30',
            'payment_method' => 'efectivo',
            'items' => [
                ['product_id' => $headphones->id, 'quantity' => 2],
                ['product_id' => $speaker->id, 'quantity' => 1],
            ],
            // Se envía un total manipulado: el servidor debe ignorarlo.
            'total' => 1,
            'subtotal' => 1,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subtotal', 250000)
            ->assertJsonPath('data.shipping_cost', 15000)
            ->assertJsonPath('data.total', 265000)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.0.line_total', 200000);

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'laura@example.com',
            'subtotal' => 250000,
            'shipping_cost' => 15000,
            'total' => 265000,
        ]);

        $order = Order::query()->firstOrFail();

        $this->assertSame(2, $order->details()->count());
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $headphones->id,
            'quantity' => 2,
            'price' => 100000,
        ]);
        $this->assertSame(2, OrderDetail::query()->count());
    }

    public function test_order_endpoint_applies_free_shipping_from_threshold(): void
    {
        $category = $this->makeCategory();
        $product = $this->makeProduct($category, ['price' => 600000]);

        $this->postJson('/api/store/orders', [
            'customer_name' => 'Laura Pérez',
            'customer_email' => 'laura@example.com',
            'customer_phone' => '3001112222',
            'customer_address' => 'Calle 10 # 25-30',
            'payment_method' => 'transferencia',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])
            ->assertCreated()
            ->assertJsonPath('data.subtotal', 600000)
            ->assertJsonPath('data.shipping_cost', 0)
            ->assertJsonPath('data.total', 600000);
    }

    public function test_order_endpoint_rejects_products_without_stock(): void
    {
        $category = $this->makeCategory();
        $product = $this->makeProduct($category, ['disponible' => false]);

        $this->postJson('/api/store/orders', [
            'customer_name' => 'Laura Pérez',
            'customer_email' => 'laura@example.com',
            'customer_phone' => '3001112222',
            'customer_address' => 'Calle 10 # 25-30',
            'payment_method' => 'efectivo',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('items.0.product_id');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_endpoint_validates_the_payload(): void
    {
        $this->postJson('/api/store/orders', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_name',
                'customer_email',
                'customer_phone',
                'customer_address',
                'payment_method',
                'items',
            ]);

        $category = $this->makeCategory();
        $product = $this->makeProduct($category);

        $this->postJson('/api/store/orders', [
            'customer_name' => 'Laura Pérez',
            'customer_email' => 'laura@example.com',
            'customer_phone' => '3001112222',
            'customer_address' => 'Calle 10 # 25-30',
            'payment_method' => 'bitcoin',
            'items' => [['product_id' => $product->id, 'quantity' => 0]],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method', 'items.0.quantity']);
    }
}
