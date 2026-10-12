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

class ResourceViewsSmokeTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Product $product;
    private Order $order;
    private OrderDetail $orderDetail;
    private OrderPayment $orderPayment;
    private Role $role;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'admin']);
        $this->user = User::create([
            'name' => 'Luis CuÃ©llar',
            'email' => 'luis@example.com',
            'password' => 'secreta-123',
            'role_id' => $this->role->id,
        ]);
        $this->category = Category::create(['name' => 'Libros', 'orden' => 1]);
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Laravel en acciÃ³n',
            'price' => 25.5,
        ]);
        $this->order = Order::create([
            'customer_name' => 'Cliente Uno',
            'customer_phone' => '555-0000',
            'customer_address' => 'Calle 1',
            'total' => 30,
        ]);
        $this->orderDetail = $this->order->details()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 25.5,
        ]);
        $this->orderPayment = $this->order->payments()->create([
            'amount' => 50,
            'payment_method' => 'efectivo',
        ]);
    }

    public function test_all_resource_views_render_with_the_admin_layout(): void
    {
        $routes = [
            'products.index' => [],
            'products.create' => [],
            'products.edit' => ['product' => $this->product],
            'products.show' => ['product' => $this->product],

            'categories.index' => [],
            'categories.create' => [],
            'categories.edit' => ['category' => $this->category],
            'categories.show' => ['category' => $this->category],

            'orders.index' => [],
            'orders.create' => [],
            'orders.edit' => ['order' => $this->order],
            'orders.show' => ['order' => $this->order],

            'order-details.index' => [],
            'order-details.create' => [],
            'order-details.edit' => ['order_detail' => $this->orderDetail],
            'order-details.show' => ['order_detail' => $this->orderDetail],

            'order-payments.index' => [],
            'order-payments.create' => [],
            'order-payments.edit' => ['order_payment' => $this->orderPayment],
            'order-payments.show' => ['order_payment' => $this->orderPayment],

            'users.index' => [],
            'users.create' => [],
            'users.edit' => ['user' => $this->user],
            'users.show' => ['user' => $this->user],

            'roles.index' => [],
            'roles.create' => [],
            'roles.edit' => ['role' => $this->role],
            'roles.show' => ['role' => $this->role],
        ];

        foreach ($routes as $route => $parameters) {
            $response = $this->get(route($route, $parameters));

            $response->assertOk();

            $response->assertSee('ut-sidebar');
            $response->assertSee('ut-topbar');
        }
    }

    public function test_index_views_render_tables_with_row_actions(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('products-table', false)
            ->assertSee('ut-icon-btn-danger', false)
            ->assertSee($this->product->name);

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('orders-table', false)
            ->assertSee($this->order->customer_name);

        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee('users-table', false)
            ->assertSee($this->user->email);
    }
}
