<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CertificateName;
use App\Models\City;
use App\Models\Country;
use App\Models\CVCategory;
use App\Models\DegreeLevel;
use App\Models\District;
use App\Models\EmploymentType;
use App\Models\Gender;
use App\Models\Industry;
use App\Models\Institution;
use App\Models\IssuingOrganization;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobTitle;
use App\Models\Major;
use App\Models\SalaryRange;
use App\Models\SalaryType;
use App\Models\Skill;
use App\Models\SkillProficiencyLevel;
use App\Models\WorkArrangement;
use Throwable;

class ReferenceApiController
{
    private const LIMIT = 20;

    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $type = (string) ($_GET['type'] ?? '');
        $query = trim((string) ($_GET['q'] ?? ''));
        $parentId = (int) ($_GET['parent_id'] ?? 0);
        $config = $this->referenceMap()[$type] ?? null;

        if ($config === null) {
            http_response_code(400);
            echo json_encode(['items' => []]);
            return;
        }

        try {
            $model = new $config['model']();
            $rows = $model->all($config['order'] ?? 'name');
            $items = [];

            foreach ($rows as $row) {
                if (($config['parent'] ?? null) !== null && $parentId > 0 && (int) ($row[$config['parent']] ?? 0) !== $parentId) {
                    continue;
                }

                $label = $this->labelFor($row, $config);

                if ($query !== '' && stripos($label, $query) === false) {
                    continue;
                }

                $items[] = [
                    'id' => (string) $row['id'],
                    'label' => $label,
                ];

                if (count($items) >= self::LIMIT) {
                    break;
                }
            }

            echo json_encode(['items' => $items]);
        } catch (Throwable) {
            http_response_code(500);
            echo json_encode(['items' => []]);
        }
    }

    private function labelFor(array $row, array $config): string
    {
        if (($config['type'] ?? '') === 'proficiency') {
            return 'Level ' . (int) $row['level_value'] . ' - ' . (string) $row['name'];
        }

        return (string) ($row[$config['label']] ?? '');
    }

    private function referenceMap(): array
    {
        return [
            'certificate_names' => ['model' => CertificateName::class, 'label' => 'name'],
            'cities' => ['model' => City::class, 'label' => 'name', 'parent' => 'country_id'],
            'countries' => ['model' => Country::class, 'label' => 'name'],
            'cv_categories' => ['model' => CVCategory::class, 'label' => 'name'],
            'degree_levels' => ['model' => DegreeLevel::class, 'label' => 'name', 'order' => 'sort_order'],
            'districts' => ['model' => District::class, 'label' => 'name', 'parent' => 'city_id'],
            'employment_types' => ['model' => EmploymentType::class, 'label' => 'name'],
            'genders' => ['model' => Gender::class, 'label' => 'name'],
            'industries' => ['model' => Industry::class, 'label' => 'name'],
            'institutions' => ['model' => Institution::class, 'label' => 'name'],
            'issuing_organizations' => ['model' => IssuingOrganization::class, 'label' => 'name'],
            'job_categories' => ['model' => JobCategory::class, 'label' => 'name'],
            'job_levels' => ['model' => JobLevel::class, 'label' => 'name', 'order' => 'sort_order'],
            'job_titles' => ['model' => JobTitle::class, 'label' => 'name'],
            'majors' => ['model' => Major::class, 'label' => 'name'],
            'salary_ranges' => ['model' => SalaryRange::class, 'label' => 'label', 'order' => 'sort_order'],
            'salary_types' => ['model' => SalaryType::class, 'label' => 'name'],
            'skill_proficiency_levels' => ['model' => SkillProficiencyLevel::class, 'label' => 'name', 'order' => 'level_value', 'type' => 'proficiency'],
            'skills' => ['model' => Skill::class, 'label' => 'name'],
            'work_arrangements' => ['model' => WorkArrangement::class, 'label' => 'name'],
        ];
    }
}
