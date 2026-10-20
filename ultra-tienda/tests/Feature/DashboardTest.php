<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin']);
        $this->user = User::create([
            'name' => 'Luis Cuéllar',
            'email' => 'luis@example.com',
            'password' => 'secreta-123',
            'role_id' => $role->id,
        ]);

        $this->actingAs($this->user);
    }

    public function test_dashboard_renders_with_the_admin_layout_and_real_metrics(): void
    {
        $category = Category::create(['name' => 'Libros', 'orden' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Laravel en acción',
            'price' => 25.5,
        ]);
        $order = Order::create([
            'customer_name' => 'Cliente Uno',
            'customer_phone' => '555-0000',
            'customer_address' => 'Calle 1',
            'total' => 30,
            'status' => 'paid',
        ]);
        $order->details()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 25.5,
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('ut-sidebar')
            ->assertSee('ut-topbar')
            ->assertSee('Ventas del mes', false)
            ->assertSee('Productos más vendidos', false)
            ->assertSee('Laravel en acción')
            ->assertSee($this->user->name);
    }

    public function test_dashboard_shows_empty_states_without_data(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sin pedidos recientes', false)
            ->assertSee('Aún no hay ventas', false);
    }

    public function test_guest_is_redirected_to_login_from_the_dashboard(): void
    {
        auth()->logout();

        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
