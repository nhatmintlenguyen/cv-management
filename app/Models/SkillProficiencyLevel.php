<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class SkillProficiencyLevel extends Model
{
    protected string $table = 'skill_proficiency_levels';

    public function ordered(): array
    {
        return $this->all('level_value');
    }
}
