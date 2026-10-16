<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_api_roles_returns_401_json_not_302(): void
    {
        $this->getJson('/api/roles')->assertUnauthorized();
    }

    public function test_login_returns_token_and_roles_crud_works_without_csrf(): void
    {
        $user = User::create([
            'name' => 'Api Tester',
            'email' => 'api@test.com',
            'password' => 'Secreta123',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'api@test.com',
            'password' => 'Secreta123',
        ])->assertOk()->json();

        $this->assertArrayHasKey('token', $login);
        $token = $login['token'];

        $this->getJson('/api/roles', ['Authorization' => "Bearer $token"])->assertOk();

        $created = $this->postJson('/api/roles', ['name' => 'cajero_test'], ['Authorization' => "Bearer $token"])
            ->assertCreated()->json();

        $this->postJson('/api/roles', ['name' => 'cajero_test'], ['Authorization' => "Bearer $token"])
            ->assertStatus(422);

        $this->getJson("/api/roles/{$created['id']}", ['Authorization' => "Bearer $token"])->assertOk();

        $this->putJson("/api/roles/{$created['id']}", ['name' => 'cajero_test_2'], ['Authorization' => "Bearer $token"])->assertOk();

        $this->deleteJson("/api/roles/{$created['id']}", [], ['Authorization' => "Bearer $token"])->assertNoContent();

        $this->postJson('/api/logout', [], ['Authorization' => "Bearer $token"])->assertOk();
    }

    public function test_login_with_wrong_password_returns_422(): void
    {
        User::create(['name' => 'Api Tester', 'email' => 'api@test.com', 'password' => 'Secreta123']);

        $this->postJson('/api/login', ['email' => 'api@test.com', 'password' => 'mal'])->assertStatus(422);
    }
}
