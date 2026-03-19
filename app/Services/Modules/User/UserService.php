<?php

declare(strict_types=1);

namespace App\Services\Modules\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Nome, email e senha são obrigatórios.');
        }

        // Check for duplicate email
        $existing = $this->userRepository->findByEmail($data['email']);
        if ($existing) {
            throw new \RuntimeException('Este email já está em uso.');
        }

        $data['active'] = $data['active'] ?? true;

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \RuntimeException('Usuário não encontrado.');
        }

        // Check email uniqueness if updating email
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $existing = $this->userRepository->findByEmail($data['email']);
            if ($existing) {
                throw new \RuntimeException('Este email já está em uso.');
            }
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \RuntimeException('Usuário não encontrado.');
        }

        // Prevent deleting yourself
        if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === $id) {
            throw new \RuntimeException('Não é possível excluir o próprio usuário.');
        }

        return $this->userRepository->delete($id);
    }

    public function paginateUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }
}
