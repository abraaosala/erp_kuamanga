<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\User;

use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use eftec\bladeone\BladeOne;

class AuthController
{
    public function __construct(
        protected AuthServiceInterface $authService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function showLogin(Request $request)
    {
        if ($this->authService->check()) {
            return redirect('/dashboard');
        }

        $html = $this->blade->run('auth.login', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'O email é obrigatório.',
            'email.email'       => 'Digite um email válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min'      => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/login');
        }

        if ($this->authService->attempt($data['email'], $data['password'])) {
            return redirect('/dashboard');
        }

        $_SESSION['flash_error'] = 'Email ou senha inválidos.';
        return redirect('/login');
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $_SESSION['flash_success'] = 'Desconectado com sucesso!';
        return redirect('/login');
    }
}
