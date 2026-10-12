<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\RecoveryCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'secreta-123',
        ]);
    }

    public function test_login_page_renders_tabs_and_forms(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar Sesión')
            ->assertSee('Recuperar Cuenta')
            ->assertSee('form-login', false)
            ->assertSee('view-recovery', false);
    }

    public function test_guest_is_redirected_to_login_from_protected_routes(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_valid_credentials_authenticate_and_redirect_to_dashboard(): void
    {
        $this->post(route('login.attempt'), [
            'email' => 'ana@example.com',
            'password' => 'secreta-123',
            'remember' => 1,
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->post(route('login.attempt'), [
            'email' => 'ana@example.com',
            'password' => 'incorrecta',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_validates_required_fields(): void
    {
        $this->post(route('login.attempt'), [])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_authenticated_user_cannot_visit_login_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('login'))
            ->assertRedirect('/');
    }

    public function test_logout_destroys_the_session(): void
    {
        $this->actingAs($this->user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_recovery_page_renders_request_step(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Enviar código de recuperación');
    }

    public function test_recovery_code_is_sent_for_registered_email(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'ana@example.com'])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHas('recovery_email', 'ana@example.com');

        Notification::assertSentTo($this->user, RecoveryCodeNotification::class);

        $this->assertGuest();
    }

    public function test_recovery_flow_looks_the_same_for_unknown_email(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'nadie@example.com'])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHas('recovery_email', 'nadie@example.com');

        Notification::assertNothingSent();
    }

    public function test_recovery_step_two_requires_a_previous_code_request(): void
    {
        $this->get(route('password.reset'))->assertRedirect(route('password.request'));
    }

    public function test_recovery_step_two_renders_with_a_pending_request(): void
    {
        $this->post(route('password.email'), ['email' => 'ana@example.com']);

        $this->get(route('password.reset'))
            ->assertOk()
            ->assertSee('recovery-password', false);
    }

    public function test_password_can_be_reset_with_the_received_code(): void
    {
        $code = $this->requestRecoveryCode();

        $this->post(route('password.update'), [
            'code' => $code,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success')
            ->assertSessionMissing('recovery_email');

        $this->assertGuest();
        $this->assertTrue(Hash::check('nueva-clave-123', $this->user->fresh()->password));

        $this->post(route('login.attempt'), [
            'email' => 'ana@example.com',
            'password' => 'nueva-clave-123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_invalid_code_is_rejected(): void
    {
        $code = $this->requestRecoveryCode();
        $wrongCode = $code === '000000' ? '000001' : '000000';

        $this->post(route('password.update'), [
            'code' => $wrongCode,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertSessionHasErrors('code');

        $this->assertGuest();
        $this->assertTrue(Hash::check('secreta-123', $this->user->fresh()->password));
    }

    public function test_expired_code_is_rejected(): void
    {
        $code = $this->requestRecoveryCode();

        $this->travel(16)->minutes();

        $this->post(route('password.update'), [
            'code' => $code,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertSessionHasErrors('code');

        $this->assertTrue(Hash::check('secreta-123', $this->user->fresh()->password));
    }

    public function test_reset_validates_password_rules(): void
    {
        $code = $this->requestRecoveryCode();

        $this->post(route('password.update'), [
            'code' => $code,
            'password' => 'corta',
            'password_confirmation' => 'corta',
        ])->assertSessionHasErrors('password');

        $this->post(route('password.update'), [
            'code' => $code,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'distinta-123',
        ])->assertSessionHasErrors('password');
    }

    public function test_reset_validates_code_format(): void
    {
        $this->requestRecoveryCode();

        $this->post(route('password.update'), [
            'code' => '12ab56',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertSessionHasErrors('code');
    }

    /**
     * Solicita el código de recuperación y lo extrae de la notificación enviada.
     */
    private function requestRecoveryCode(): string
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'ana@example.com'])
            ->assertRedirect(route('password.reset'));

        $code = '';

        Notification::assertSentTo(
            $this->user,
            RecoveryCodeNotification::class,
            function (RecoveryCodeNotification $notification) use (&$code): bool {
                $code = $notification->code;

                return true;
            }
        );

        return $code;
    }
}
