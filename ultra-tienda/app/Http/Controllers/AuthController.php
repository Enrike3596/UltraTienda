<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RecoveryCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Minutos de vigencia del código de recuperación.
     */
    private const int RECOVERY_TTL_MINUTES = 15;

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function create(): View
    {
        return view('auth.login', [
            'tab' => 'login',
            'step' => session()->has('recovery_email') ? 2 : 1,
        ]);
    }

    /**
     * Autentica al usuario e inicia la sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'Ingresa tu correo electrónico.',
                'email.email' => 'Ingresa un correo electrónico válido.',
                'password.required' => 'Ingresa tu contraseña.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        if (! Auth::attempt($validator->validated(), $request->boolean('remember'))) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'El correo o la contraseña son incorrectos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Muestra la pestaña de recuperación (paso 1: solicitud de código).
     */
    public function recoveryForm(): View
    {
        return view('auth.login', [
            'tab' => 'recovery',
            'step' => 1,
        ]);
    }

    /**
     * Genera y envía el código de 6 dígitos al correo registrado.
     *
     * La respuesta es idéntica exista o no el correo para no revelar
     * qué direcciones están registradas en el sistema.
     */
    public function sendRecoveryCode(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['email' => ['required', 'email']],
            [
                'email.required' => 'Ingresa tu correo electrónico.',
                'email.email' => 'Ingresa un correo electrónico válido.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('password.request')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $data = $validator->validated();
        $user = User::query()->where('email', $data['email'])->first();

        if ($user instanceof User) {
            $code = Str::padLeft((string) random_int(0, 999999), 6, '0');

            DB::table(config('auth.passwords.users.table'))->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($code), 'created_at' => now()]
            );

            try {
                $user->notify(new RecoveryCodeNotification($code));
            } catch (\Exception $e) {
                // Log error but continue - user will see message on next page
                \Log::error('Recovery code notification failed: ' . $e->getMessage());
            }

            $request->session()->put('recovery_email', $user->email);
        } else {
            $request->session()->put('recovery_email', $data['email']);
        }

        return redirect()
            ->route('password.reset')
            ->with('success', 'Si el correo está registrado, recibirás un código de 6 dígitos (vigente '.self::RECOVERY_TTL_MINUTES.' minutos).');
    }

    /**
     * Muestra el paso 2: formulario para restablecer la contraseña.
     */
    public function resetForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('recovery_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.login', [
            'tab' => 'recovery',
            'step' => 2,
        ]);
    }

    /**
     * Valida el código enviado y guarda la nueva contraseña.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'code' => ['required', 'digits:6'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'code.required' => 'Ingresa el código de 6 dígitos.',
                'code.digits' => 'El código debe tener 6 dígitos.',
                'password.required' => 'Ingresa la nueva contraseña.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('password.reset')
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $email = (string) $request->session()->get('recovery_email');
        $user = User::query()->where('email', $email)->first();
        $record = DB::table(config('auth.passwords.users.table'))->where('email', $email)->first();

        $isValidCode = $user instanceof User
            && $record !== null
            && $record->created_at !== null
            && Carbon::parse($record->created_at)->gt(now()->subMinutes(self::RECOVERY_TTL_MINUTES))
            && Hash::check((string) $request->input('code'), $record->token);

        if (! $isValidCode) {
            throw ValidationException::withMessages([
                'code' => 'El código es inválido o ha expirado. Solicita uno nuevo.',
            ])->redirectTo(route('password.reset'));
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        DB::table(config('auth.passwords.users.table'))->where('email', $email)->delete();
        $request->session()->forget('recovery_email');

        return redirect()
            ->route('login')
            ->with('success', 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.');
    }
}