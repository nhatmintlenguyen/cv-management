<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class District extends Model
{
    protected string $table = 'districts';

    public function findByCityId(int $cityId): array
    {
        return $this->get(
            'SELECT * FROM `districts` WHERE `city_id` = :city_id ORDER BY `name` ASC',
            ['city_id' => $cityId]
        );
    }
}
