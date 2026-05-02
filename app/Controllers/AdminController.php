<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CertificateName;
use App\Models\City;
use App\Models\Country;
use App\Models\CV;
use App\Models\CVCategory;
use App\Models\DegreeLevel;
use App\Models\District;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\Institution;
use App\Models\IssuingOrganization;
use App\Models\JobTitle;
use App\Models\Major;
use App\Models\Role;
use App\Models\Skill;
use App\Models\SkillProficiencyLevel;
use App\Models\User;
use PDOException;

class AdminController extends Controller
{
    private array $referenceTypes = [
        'skills' => ['label' => 'Skills', 'icon' => 'psychology', 'model' => Skill::class, 'columns' => ['id' => 'ID', 'name' => 'Skill Name'], 'fields' => ['name' => 'Skill Name']],
        'categories' => ['label' => 'CV Categories', 'icon' => 'category', 'model' => CVCategory::class, 'columns' => ['id' => 'ID', 'name' => 'Category Name'], 'fields' => ['name' => 'Category Name']],
        'degrees' => ['label' => 'Degree Levels', 'icon' => 'school', 'model' => DegreeLevel::class, 'columns' => ['id' => 'ID', 'name' => 'Degree Level', 'sort_order' => 'Sort Order'], 'fields' => ['name' => 'Degree Level', 'sort_order' => 'Sort Order'], 'locked' => true],
        'majors' => ['label' => 'Majors', 'icon' => 'auto_stories', 'model' => Major::class, 'columns' => ['id' => 'ID', 'name' => 'Major Name'], 'fields' => ['name' => 'Major Name']],
        'institutions' => ['label' => 'Institutions', 'icon' => 'account_balance', 'model' => Institution::class, 'columns' => ['id' => 'ID', 'name' => 'Institution Name'], 'fields' => ['name' => 'Institution Name']],
        'countries' => ['label' => 'Countries', 'icon' => 'public', 'model' => Country::class, 'columns' => ['id' => 'ID', 'name' => 'Country Name'], 'fields' => ['name' => 'Country Name']],
        'cities' => ['label' => 'Cities', 'icon' => 'location_city', 'model' => City::class, 'columns' => ['id' => 'ID', 'name' => 'City Name', 'country_name' => 'Country'], 'fields' => ['name' => 'City Name', 'country_id' => 'Country']],
        'districts' => ['label' => 'Districts', 'icon' => 'map', 'model' => District::class, 'columns' => ['id' => 'ID', 'name' => 'District Name', 'city_name' => 'City'], 'fields' => ['name' => 'District Name', 'city_id' => 'City']],
        'job_titles' => ['label' => 'Job Titles', 'icon' => 'badge', 'model' => JobTitle::class, 'columns' => ['id' => 'ID', 'name' => 'Job Title'], 'fields' => ['name' => 'Job Title']],
        'employment_types' => ['label' => 'Employment Types', 'icon' => 'work_history', 'model' => EmploymentType::class, 'columns' => ['id' => 'ID', 'name' => 'Employment Type'], 'fields' => ['name' => 'Employment Type']],
        'industries' => ['label' => 'Industries', 'icon' => 'work', 'model' => Industry::class, 'columns' => ['id' => 'ID', 'name' => 'Industry Name'], 'fields' => ['name' => 'Industry Name']],
        'certificates' => ['label' => 'Certificates', 'icon' => 'workspace_premium', 'model' => CertificateName::class, 'columns' => ['id' => 'ID', 'name' => 'Certificate Name'], 'fields' => ['name' => 'Certificate Name']],
        'issuing_organizations' => ['label' => 'Issuing Organizations', 'icon' => 'verified', 'model' => IssuingOrganization::class, 'columns' => ['id' => 'ID', 'name' => 'Organization Name'], 'fields' => ['name' => 'Organization Name']],
        'proficiency_levels' => ['label' => 'Proficiency Levels', 'icon' => 'bar_chart', 'model' => SkillProficiencyLevel::class, 'columns' => ['id' => 'ID', 'name' => 'Level Name', 'level_value' => 'Value'], 'fields' => ['name' => 'Level Name', 'level_value' => 'Value'], 'locked' => true],
    ];

    public function overview(): void
    {
        $this->requireAdmin();

        $stats = [
            'total_users' => $this->countTable('users'),
            'job_seekers' => $this->countUsersByRole('job_seeker'),
            'employers' => $this->countUsersByRole('employer'),
            'admins' => $this->countUsersByRole('admin'),
            'total_cvs' => $this->countTable('cvs'),
            'institutions' => $this->countTable('institutions'),
            'skills' => $this->countTable('skills'),
            'categories' => $this->countTable('cv_categories'),
        ];

        $this->view('admin/overview', [
            'title' => 'Admin Overview',
            'activeTab' => 'overview',
            'stats' => $stats,
        ]);
    }

    public function userManagement(): void
    {
        $this->requireAdmin();

        $role = $_GET['role'] ?? 'job_seeker';
        $allowedRoles = ['job_seeker', 'employer', 'admin'];

        if (! in_array($role, $allowedRoles, true)) {
            $role = 'job_seeker';
        }

        $users = (new User())->get(
            'SELECT users.id, users.full_name, users.email, users.status, users.created_at, roles.name AS role_name
             FROM `users`
             INNER JOIN `roles` ON roles.id = users.role_id
             WHERE roles.name = :role
             ORDER BY users.created_at DESC',
            ['role' => $role]
        );

        $this->view('admin/users', [
            'title' => 'User Management',
            'activeTab' => 'users',
            'selectedRole' => $role,
            'roles' => $allowedRoles,
            'users' => $users,
            'total' => count($users),
        ]);
    }

    public function referenceManagement(): void
    {
        $this->requireAdmin();

        $type = $_GET['type'] ?? 'institutions';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;

        if (! array_key_exists($type, $this->referenceTypes)) {
            $type = 'institutions';
        }

        $config = $this->referenceTypes[$type];
        $total = $this->referenceCount($type, $config['model']);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $rows = $this->referenceRows($type, $config['model'], $perPage, ($page - 1) * $perPage);

        $this->view('admin/reference-data', [
            'title' => 'Reference Management',
            'activeTab' => 'reference',
            'referenceTypes' => $this->referenceTypes,
            'selectedType' => $type,
            'selectedConfig' => $config,
            'rows' => $rows,
            'fieldOptions' => $this->fieldOptions($type),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
                'to' => min($page * $perPage, $total),
            ],
        ]);
    }

    public function storeReference(): void
    {
        $this->requireAdmin();

        $type = $_POST['type'] ?? '';
        $page = max(1, (int) ($_POST['page'] ?? 1));
        $config = $this->referenceConfig($type);

        if ($config === null) {
            $this->flash('errors', ['Invalid reference type.']);
            $this->redirect('/admin/reference?type=skills&page=1');
        }

        if (! empty($config['locked'])) {
            $this->flash('errors', ["{$config['label']} is locked and cannot be changed from the admin UI."]);
            $this->redirect($this->referenceUrl($type, $page));
        }

        [$payload, $errors] = $this->referencePayload($type, $config);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->redirect($this->referenceUrl($type, $page));
        }

        try {
            $model = new $config['model']();
            $model->create($payload);
            $this->flash('success', "{$config['label']} entry created.");
        } catch (PDOException $exception) {
            $this->flash('errors', ['Could not create entry. It may already exist or violates a database rule.']);
        }

        $this->redirect($this->referenceUrl($type, $page));
    }

    public function updateReference(): void
    {
        $this->requireAdmin();

        $type = $_POST['type'] ?? '';
        $page = max(1, (int) ($_POST['page'] ?? 1));
        $id = (int) ($_POST['id'] ?? 0);
        $config = $this->referenceConfig($type);

        if ($config === null || $id <= 0) {
            $this->flash('errors', ['Invalid update request.']);
            $this->redirect('/admin/reference?type=skills&page=1');
        }

        if (! empty($config['locked'])) {
            $this->flash('errors', ["{$config['label']} is locked and cannot be changed from the admin UI."]);
            $this->redirect($this->referenceUrl($type, $page));
        }

        [$payload, $errors] = $this->referencePayload($type, $config);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->redirect($this->referenceUrl($type, $page));
        }

        try {
            $model = new $config['model']();
            $model->update($id, $payload);
            $this->flash('success', "{$config['label']} entry updated.");
        } catch (PDOException $exception) {
            $this->flash('errors', ['Could not update entry. It may duplicate another entry or violates a database rule.']);
        }

        $this->redirect($this->referenceUrl($type, $page));
    }

    public function deleteReference(): void
    {
        $this->requireAdmin();

        $type = $_POST['type'] ?? '';
        $page = max(1, (int) ($_POST['page'] ?? 1));
        $id = (int) ($_POST['id'] ?? 0);
        $config = $this->referenceConfig($type);

        if ($config === null || $id <= 0) {
            $this->flash('errors', ['Invalid delete request.']);
            $this->redirect('/admin/reference?type=skills&page=1');
        }

        if (! empty($config['locked'])) {
            $this->flash('errors', ["{$config['label']} is locked and cannot be changed from the admin UI."]);
            $this->redirect($this->referenceUrl($type, $page));
        }

        try {
            $model = new $config['model']();
            $model->delete($id);
            $this->flash('success', "{$config['label']} entry deleted.");
        } catch (PDOException $exception) {
            $this->flash('errors', ['Could not delete entry because it is currently used by CV data.']);
        }

        $this->redirect($this->referenceUrl($type, $page));
    }

    private function requireAdmin(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'admin') {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Forbidden']);
            exit;
        }
    }

    private function countTable(string $table): int
    {
        $row = (new User())->first("SELECT COUNT(*) AS total FROM `{$table}`");

        return (int) ($row['total'] ?? 0);
    }

    private function countUsersByRole(string $role): int
    {
        $row = (new User())->first(
            'SELECT COUNT(*) AS total
             FROM `users`
             INNER JOIN `roles` ON roles.id = users.role_id
             WHERE roles.name = :role',
            ['role' => $role]
        );

        return (int) ($row['total'] ?? 0);
    }

    private function referenceRows(string $type, string $modelClass, int $limit, int $offset): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        if ($type === 'degrees') {
            return (new DegreeLevel())->get(
                "SELECT * FROM `degree_levels` ORDER BY `sort_order` ASC LIMIT {$limit} OFFSET {$offset}"
            );
        }

        if ($type === 'proficiency_levels') {
            return (new SkillProficiencyLevel())->get(
                "SELECT * FROM `skill_proficiency_levels` ORDER BY `level_value` ASC LIMIT {$limit} OFFSET {$offset}"
            );
        }

        if ($type === 'cities') {
            return (new City())->get(
                "SELECT cities.id, cities.name, cities.country_id, countries.name AS country_name
                 FROM `cities`
                 INNER JOIN `countries` ON countries.id = cities.country_id
                 ORDER BY countries.name ASC, cities.name ASC
                 LIMIT {$limit} OFFSET {$offset}"
            );
        }

        if ($type === 'districts') {
            return (new District())->get(
                "SELECT districts.id, districts.name, districts.city_id, cities.name AS city_name
                 FROM `districts`
                 INNER JOIN `cities` ON cities.id = districts.city_id
                 ORDER BY cities.name ASC, districts.name ASC
                 LIMIT {$limit} OFFSET {$offset}"
            );
        }

        $table = $this->referenceTable($modelClass);

        return (new User())->get(
            "SELECT * FROM `{$table}` ORDER BY `name` ASC LIMIT {$limit} OFFSET {$offset}"
        );
    }

    private function referenceCount(string $type, string $modelClass): int
    {
        $table = $this->referenceTable($modelClass);
        $row = (new User())->first("SELECT COUNT(*) AS total FROM `{$table}`");

        return (int) ($row['total'] ?? 0);
    }

    private function referenceTable(string $modelClass): string
    {
        return match ($modelClass) {
            Skill::class => 'skills',
            CVCategory::class => 'cv_categories',
            DegreeLevel::class => 'degree_levels',
            Major::class => 'majors',
            Institution::class => 'institutions',
            Country::class => 'countries',
            City::class => 'cities',
            District::class => 'districts',
            JobTitle::class => 'job_titles',
            EmploymentType::class => 'employment_types',
            Industry::class => 'industries',
            CertificateName::class => 'certificate_names',
            IssuingOrganization::class => 'issuing_organizations',
            SkillProficiencyLevel::class => 'skill_proficiency_levels',
        };
    }

    private function referenceConfig(string $type): ?array
    {
        return $this->referenceTypes[$type] ?? null;
    }

    private function referencePayload(string $type, array $config): array
    {
        $payload = [];
        $errors = [];

        foreach ($config['fields'] as $field => $label) {
            $value = trim((string) ($_POST[$field] ?? ''));

            if ($value === '') {
                $errors[] = "{$label} is required.";
                continue;
            }

            if (in_array($field, ['country_id', 'city_id', 'sort_order', 'level_value'], true)) {
                $payload[$field] = (int) $value;
            } else {
                $payload[$field] = $value;
            }
        }

        if (isset($payload['sort_order']) && $payload['sort_order'] < 0) {
            $errors[] = 'Sort Order must be 0 or greater.';
        }

        if (isset($payload['level_value']) && ($payload['level_value'] < 1 || $payload['level_value'] > 10)) {
            $errors[] = 'Proficiency level value must be between 1 and 10.';
        }

        if ($type === 'cities' && isset($payload['country_id']) && ! (new Country())->exists($payload['country_id'])) {
            $errors[] = 'Selected country does not exist.';
        }

        if ($type === 'districts' && isset($payload['city_id']) && ! (new City())->exists($payload['city_id'])) {
            $errors[] = 'Selected city does not exist.';
        }

        return [$payload, $errors];
    }

    private function fieldOptions(string $type): array
    {
        if ($type === 'cities') {
            return [
                'country_id' => (new Country())->all('name'),
            ];
        }

        if ($type === 'districts') {
            return [
                'city_id' => (new City())->get(
                    'SELECT cities.id, CONCAT(countries.name, " - ", cities.name) AS name
                     FROM `cities`
                     INNER JOIN `countries` ON countries.id = cities.country_id
                     ORDER BY countries.name ASC, cities.name ASC'
                ),
            ];
        }

        return [];
    }

    private function referenceUrl(string $type, int $page): string
    {
        return '/admin/reference?type=' . urlencode($type) . '&page=' . max(1, $page);
    }
}
