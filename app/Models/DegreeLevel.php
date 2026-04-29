<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class DegreeLevel extends Model
{
    protected string $table = 'degree_levels';

    public function ordered(): array
    {
        return $this->all('sort_order');
    }
}
