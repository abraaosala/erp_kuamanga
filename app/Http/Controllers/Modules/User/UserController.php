<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\User;

use App\Services\Contracts\UserServiceInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;
use eftec\bladeone\BladeOne;

class UserController
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected RoleRepositoryInterface $roleRepository,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $users = $this->userService->paginateUsers(15);

        $html = $this->blade->run('users.index', [
            'users'   => $users,
            'success' => $_SESSION['flash_success'] ?? null,
            'error'   => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request)
    {
        $roles = $this->roleRepository->all();

        $html = $this->blade->run('users.create', [
            'roles' => $roles,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:255',
            'password' => 'required|min:6',
        ], [
            'name.required'     => 'O nome é obrigatório.',
            'email.required'    => 'O email é obrigatório.',
            'email.email'       => 'Email inválido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min'      => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/users/create');
        }

        try {
            $user = $this->userService->createUser([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'active'   => isset($data['active']) ? (bool) $data['active'] : true,
            ]);

            // Assign roles if provided
            if (!empty($data['roles']) && is_array($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            $_SESSION['flash_success'] = 'Usuário criado com sucesso!';
            return redirect('/users');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/users/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $user  = $this->userService->getUserById($id);
        $roles = $this->roleRepository->all();

        if (!$user) {
            $_SESSION['flash_error'] = 'Usuário não encontrado.';
            return redirect('/users');
        }

        $html = $this->blade->run('users.edit', [
            'user'  => $user,
            'roles' => $roles,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:255',
        ], [
            'name.required'  => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email'    => 'Email inválido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/users/' . $id . '/edit');
        }

        try {
            $updateData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'active' => isset($data['active']) ? (bool) $data['active'] : true,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $this->userService->updateUser($id, $updateData);

            // Sync roles
            $user = $this->userService->getUserById($id);
            if ($user) {
                $roles = !empty($data['roles']) && is_array($data['roles']) ? $data['roles'] : [];
                $user->roles()->sync($roles);
            }

            $_SESSION['flash_success'] = 'Usuário atualizado com sucesso!';
            return redirect('/users');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/users/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->userService->deleteUser($id);
            $_SESSION['flash_success'] = 'Usuário excluído com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/users');
    }
}
