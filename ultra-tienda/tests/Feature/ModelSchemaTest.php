<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModelSchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Columns that are never mass assigned by convention.
     *
     * @var list<string>
     */
    private const array RESERVED_COLUMNS = ['id', 'created_at', 'updated_at', 'email_verified_at', 'remember_token'];

    /**
     * @return iterable<string, array{class-string<Model>, string}>
     */
    public static function modelProvider(): iterable
    {
        yield 'User' => [User::class, 'users'];
        yield 'Role' => [Role::class, 'roles'];
        yield 'Category' => [Category::class, 'categories'];
        yield 'Product' => [Product::class, 'products'];
        yield 'Order' => [Order::class, 'orders'];
        yield 'OrderDetail' => [OrderDetail::class, 'order_details'];
        yield 'OrderPayment' => [OrderPayment::class, 'order_payments'];
    }

    #[DataProvider('modelProvider')]
    public function test_model_resolves_its_database_table(string $modelClass, string $table): void
    {
        $model = new $modelClass;

        $this->assertSame($table, $model->getTable());
        $this->assertTrue(Schema::hasTable($table), "Missing table [$table] for [$modelClass].");
    }

    #[DataProvider('modelProvider')]
    public function test_every_fillable_attribute_exists_in_the_table(string $modelClass, string $table): void
    {
        $model = new $modelClass;
        $columns = Schema::getColumnListing($table);

        foreach ($model->getFillable() as $attribute) {
            $this->assertContains(
                $attribute,
                $columns,
                "[$modelClass] declares fillable [$attribute] but the column does not exist on [$table].",
            );
        }
    }

    #[DataProvider('modelProvider')]
    public function test_every_required_column_is_mass_assignable(string $modelClass, string $table): void
    {
        $model = new $modelClass;
        $fillable = $model->getFillable();

        foreach (Schema::getColumns($table) as $column) {
            if ($column['auto_increment']
                || $column['nullable']
                || $column['default'] !== null
                || in_array($column['name'], self::RESERVED_COLUMNS, true)) {
                continue;
            }

            $this->assertContains(
                $column['name'],
                $fillable,
                "[$modelClass] cannot create a record: required column [$table.{$column['name']}] is not fillable.",
            );
        }
    }

    public function test_relationships_match_the_foreign_keys_defined_in_the_migrations(): void
    {
        $role = Role::create(['name' => 'admin']);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret-password',
            'role_id' => $role->id,
        ]);

        $category = Category::create(['name' => 'Libros', 'orden' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Libro Laravel',
            'price' => 10.5,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => 'Test User',
            'customer_phone' => '555-0000',
            'customer_address' => 'Calle Falsa 123',
            'subtotal' => 21,
            'shipping_cost' => 5,
            'total' => 26,
        ]);
        $order->details()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 10.5,
        ]);
        $order->payments()->create([
            'amount' => 26,
            'payment_method' => 'efectivo',
            'payment_status' => 'paid',
            'payment_date' => now(),
        ]);

        $this->assertTrue($user->role->is($role));
        $this->assertTrue($user->orders->first()->is($order));
        $this->assertTrue($role->users->first()->is($user));
        $this->assertTrue($product->category->is($category));
        $this->assertTrue($category->products->first()->is($product));
        $this->assertTrue($order->user->is($user));

        $this->assertSame($order->id, $order->details->first()->order_id);
        $this->assertTrue($order->details->first()->product->is($product));
        $this->assertTrue($order->payments->first()->order->is($order));
        $this->assertCount(1, $order->details);
        $this->assertCount(1, $order->payments);
    }
}
