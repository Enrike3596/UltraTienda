<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceControllersSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(): Category
    {
        return Category::create(['name' => 'Libros', 'orden' => 1]);
    }

    private function makeProduct(Category $category): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => 'Laravel en acción',
            'price' => 25.5,
        ]);
    }

    private function makeOrder(): Order
    {
        return Order::create([
            'customer_name' => 'Cliente Uno',
            'customer_phone' => '555-0000',
            'customer_address' => 'Calle 1',
            'total' => 30,
        ]);
    }

    public function test_product_controller_stores_updates_and_destroys(): void
    {
        $category = $this->makeCategory();

        $this->post('/products', [
            'category_id' => $category->id,
            'name' => 'Nuevo producto',
            'description' => 'Descripción',
            'price' => 10,
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $product = Product::firstWhere('name', 'Nuevo producto');
        $this->assertNotNull($product);
        $this->assertSame($category->id, $product->category_id);

        $this->put("/products/{$product->id}", [
            'category_id' => $category->id,
            'name' => 'Producto editado',
            'price' => 15,
        ])->assertRedirect(route('products.index'))->assertSessionHas('success');

        $this->assertSame('Producto editado', $product->fresh()->name);

        $this->delete("/products/{$product->id}")->assertRedirect(route('products.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_requires_category_and_rejects_unknown_fields(): void
    {
        $this->post('/products', ['name' => 'Sin categoría', 'price' => 10])
            ->assertSessionHasErrors(['category_id']);

        $this->post('/products', [
            'category_id' => 1,
            'name' => 'Inválido',
            'price' => -1,
        ])->assertSessionHasErrors(['category_id', 'price']);
    }

    public function test_missing_product_returns_404_on_update_and_destroy(): void
    {
        $this->put('/products/9999', [])->assertNotFound();
        $this->delete('/products/9999')->assertNotFound();
    }

    public function test_product_linked_to_an_order_detail_cannot_be_deleted(): void
    {
        $product = $this->makeProduct($this->makeCategory());
        $order = $this->makeOrder();
        $order->details()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 25.5,
        ]);

        $this->delete("/products/{$product->id}")
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_category_controller_full_flow_and_unique_name(): void
    {
        $this->post('/categories', ['name' => 'Tecnología'])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $category = Category::firstWhere('name', 'Tecnología');
        $this->assertNotNull($category);

        $this->post('/categories', ['name' => 'Tecnología'])
            ->assertSessionHasErrors(['name']);

        $this->put("/categories/{$category->id}", ['name' => 'Tecnología 2'])
            ->assertRedirect(route('categories.index'));

        $this->assertSame('Tecnología 2', $category->fresh()->name);

        $this->post('/categories', ['name' => 'Tecnología 2', 'orden' => 3])
            ->assertSessionHasErrors(['name']);

        $this->delete("/categories/{$category->id}")->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = $this->makeCategory();
        $this->makeProduct($category);

        $this->delete("/categories/{$category->id}")
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_role_controller_full_flow(): void
    {
        $this->post('/roles', ['name' => 'admin'])->assertSessionHas('success');
        $role = Role::firstWhere('name', 'admin');

        $this->post('/roles', ['name' => 'admin'])->assertSessionHasErrors(['name']);

        $this->put("/roles/{$role->id}", ['name' => 'editor'])->assertSessionHas('success');
        $this->assertSame('editor', $role->fresh()->name);

        $this->delete("/roles/{$role->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_user_controller_hashes_password_and_keeps_it_when_blank_on_update(): void
    {
        $role = Role::create(['name' => 'cliente']);

        $this->post('/users', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secreta-123',
            'role_id' => $role->id,
        ])->assertRedirect(route('users.index'))->assertSessionHas('success');

        $user = User::firstWhere('email', 'ana@example.com');
        $this->assertNotNull($user);
        $this->assertNotSame('secreta-123', $user->password);

        $this->post('/users', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'otra-clave',
        ])->assertSessionHasErrors(['email']);

        $passwordBefore = $user->password;

        $this->put("/users/{$user->id}", [
            'name' => 'Ana Editada',
            'email' => 'ana@example.com',
            'password' => '',
            'role_id' => $role->id,
        ])->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertSame('Ana Editada', $user->name);
        $this->assertSame($passwordBefore, $user->password);

        $this->put("/users/{$user->id}", [
            'name' => 'Ana Editada',
            'email' => 'ana@example.com',
            'password' => 'nueva-clave-1',
        ])->assertSessionHas('success');

        $this->assertNotSame($passwordBefore, $user->fresh()->password);

        $this->delete("/users/{$user->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_order_detail_and_payment_controllers_full_flow(): void
    {
        $product = $this->makeProduct($this->makeCategory());
        $order = $this->makeOrder();

        $this->post('/order-details', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 25.5,
        ])->assertRedirect(route('order-details.index'))->assertSessionHas('success');

        $detail = OrderDetail::first();
        $this->assertNotNull($detail);

        $this->post('/order-details', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 0,
            'price' => 25.5,
        ])->assertSessionHasErrors(['quantity']);

        $this->put("/order-details/{$detail->id}", [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 20,
        ])->assertSessionHas('success');
        $this->assertSame(5, $detail->fresh()->quantity);

        $this->post('/order-payments', [
            'order_id' => $order->id,
            'amount' => 50,
            'payment_method' => 'efectivo',
        ])->assertRedirect(route('order-payments.index'))->assertSessionHas('success');

        $payment = OrderPayment::first();
        $this->assertNotNull($payment);

        $this->post('/order-payments', [
            'order_id' => $order->id,
            'amount' => -5,
            'payment_method' => 'efectivo',
        ])->assertSessionHasErrors(['amount']);

        $this->put("/order-payments/{$payment->id}", [
            'order_id' => $order->id,
            'amount' => 60,
            'payment_method' => 'transferencia',
            'payment_date' => now()->toDateString(),
        ])->assertSessionHas('success');
        $this->assertSame(60.0, (float) $payment->fresh()->amount);

        $this->delete("/order-details/{$detail->id}")->assertSessionHas('success');
        $this->delete("/order-payments/{$payment->id}")->assertSessionHas('success');

        $this->delete('/order-details/9999')->assertNotFound();
        $this->delete('/order-payments/9999')->assertNotFound();
        $this->put('/orders/9999', [])->assertNotFound();
        $this->delete('/users/9999')->assertNotFound();
        $this->delete('/roles/9999')->assertNotFound();
        $this->delete('/categories/9999')->assertNotFound();
    }

    public function test_order_controller_full_flow(): void
    {
        $this->post('/orders', [
            'customer_name' => 'Pedro',
            'customer_phone' => '3001112222',
            'customer_address' => 'Carrera 7',
            'total' => 100,
        ])->assertRedirect(route('orders.index'))->assertSessionHas('success');

        $order = Order::firstWhere('customer_name', 'Pedro');
        $this->assertNotNull($order);
        $this->assertSame('0.00', (string) $order->subtotal);

        $this->post('/orders', ['customer_name' => 'Sin datos'])
            ->assertSessionHasErrors(['customer_phone', 'customer_address', 'total']);

        $this->put("/orders/{$order->id}", [
            'customer_name' => 'Pedro Pérez',
            'customer_phone' => '3001112222',
            'customer_address' => 'Carrera 8',
            'total' => 120,
            'status' => 'paid',
        ])->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('Pedro Pérez', $order->customer_name);
        $this->assertSame('paid', $order->status);

        $this->delete("/orders/{$order->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
