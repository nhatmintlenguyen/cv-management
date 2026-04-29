<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class City extends Model
{
    protected string $table = 'cities';

    public function findByCountryId(int $countryId): array
    {
        return $this->get(
            'SELECT * FROM `cities` WHERE `country_id` = :country_id ORDER BY `name` ASC',
            ['country_id' => $countryId]
        );
    }
}
