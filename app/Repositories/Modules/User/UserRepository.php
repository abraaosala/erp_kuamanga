<?php

declare(strict_types=1);

namespace App\Repositories\Modules\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        /** @var Collection<int, User> $result */
        $result = User::with('roles')->get();

        return $result;
    }

    public function findById(int $id): ?User
    {
        /** @var \App\Models\User|null $user */
        $user = User::with('roles')->find($id);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        /** @var \App\Models\User|null $user */
        $user = User::where('email', $email)->first();

        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        if (isset($data['password']) && empty($data['password'])) {
            unset($data['password']);
        }

        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        /** @var bool $deleted */
        $deleted = $user->delete();
        return $deleted;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')->paginate($perPage);
    }
}
