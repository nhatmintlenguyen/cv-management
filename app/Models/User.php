<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere('email', $email);
    }

    public function createUser(array $data): int
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->create($data);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return $this->update($userId, $data);
    }

    public function updatePassword(int $userId, string $password): bool
    {
        return $this->update($userId, [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    public function emailExistsForAnotherUser(string $email, int $userId): bool
    {
        return $this->first(
            'SELECT 1
             FROM `users`
             WHERE `email` = :email AND `id` <> :user_id
             LIMIT 1',
            ['email' => $email, 'user_id' => $userId]
        ) !== null;
    }

    public function getRole(int $userId): ?array
    {
        return $this->first(
            'SELECT roles.*
             FROM `users`
             INNER JOIN `roles` ON roles.id = users.role_id
             WHERE users.id = :user_id
             LIMIT 1',
            ['user_id' => $userId]
        );
    }

    public function hasRole(int $userId, string $roleName): bool
    {
        return $this->first(
            'SELECT 1
             FROM `users`
             INNER JOIN `roles` ON roles.id = users.role_id
             WHERE users.id = :user_id AND roles.name = :role_name
             LIMIT 1',
            ['user_id' => $userId, 'role_name' => $roleName]
        ) !== null;
    }

    public function activeUsersWithRoles(): array
    {
        return $this->get(
            'SELECT users.id, users.full_name, users.email, users.status, roles.name AS role_name,
                    users.created_at, users.updated_at
             FROM `users`
             INNER JOIN `roles` ON roles.id = users.role_id
             ORDER BY users.created_at DESC'
        );
    }
}
