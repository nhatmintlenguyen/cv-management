<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    public function createToken(int $userId, string $tokenHash): int
    {
        $this->deleteActiveForUser($userId);

        $this->query(
            'INSERT INTO `password_resets` (`user_id`, `token_hash`, `expires_at`)
             VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))',
            [
                'user_id' => $userId,
                'token_hash' => $tokenHash,
            ]
        );

        return (int) $this->db()->lastInsertId();
    }

    public function findValidByHash(string $tokenHash): ?array
    {
        return $this->first(
            'SELECT password_resets.*, users.email, users.full_name
             FROM `password_resets`
             INNER JOIN `users` ON users.id = password_resets.user_id
             WHERE password_resets.token_hash = :token_hash
               AND password_resets.used_at IS NULL
               AND password_resets.expires_at > NOW()
               AND users.status = "active"
             LIMIT 1',
            ['token_hash' => $tokenHash]
        );
    }

    public function markUsed(int $id): bool
    {
        return $this->query(
            'UPDATE `password_resets`
             SET `used_at` = NOW()
             WHERE `id` = :id AND `used_at` IS NULL',
            ['id' => $id]
        )->rowCount() > 0;
    }

    private function deleteActiveForUser(int $userId): void
    {
        $this->query(
            'DELETE FROM `password_resets`
             WHERE `user_id` = :user_id AND `used_at` IS NULL',
            ['user_id' => $userId]
        );
    }
}
