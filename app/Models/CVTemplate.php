<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CVTemplate extends Model
{
    protected string $table = 'cv_templates';

    public function findByKey(string $key): ?array
    {
        return $this->firstWhere('name', $this->nameFromKey($key));
    }

    public function keyFromName(?string $name): string
    {
        return match (strtolower(trim((string) $name))) {
            'classic' => 'classic',
            'minimal' => 'minimal',
            default => 'modern',
        };
    }

    private function nameFromKey(string $key): string
    {
        return match ($key) {
            'classic' => 'Classic',
            'minimal' => 'Minimal',
            default => 'Modern',
        };
    }
}
