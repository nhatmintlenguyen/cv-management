# Online CV Management & Search System Boilerplate

This repository is currently empty. The commands below assume Ubuntu, a clean directory, PHP `8.3`, and MySQL.

## 1. Ubuntu setup and Laravel scaffold

```bash
sudo apt update
sudo apt install -y git unzip curl mysql-server nodejs npm php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl composer

mysql --version
php --version
composer --version
node --version
npm --version

sudo systemctl enable --now mysql
sudo mysql
```

```sql
CREATE DATABASE cv_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cv_user'@'localhost' IDENTIFIED BY 'ChangeThisStrongPassword!';
GRANT ALL PRIVILEGES ON cv_management.* TO 'cv_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
git init
git branch -M main

composer create-project laravel/laravel .
cp .env.example .env
php artisan key:generate
```

Update `.env`:

```dotenv
APP_NAME="CV Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cv_management
DB_USERNAME=cv_user
DB_PASSWORD=ChangeThisStrongPassword!
```
Role: Act as a Senior Full-Stack PHP Developer and Database Architect.

Task: I need to set up a production-ready boilerplate for an "Online CV Management & Search System" using Laravel (PHP) and MySQL. The system requires strict database normalization , an MVC architecture , and dynamic JavaScript forms.

Please provide the exact terminal commands (Ubuntu environment) to scaffold the Laravel project, along with the code for the foundational database migrations, Eloquent models, and the core routing structure.

System Requirements & Constraints to Enforce:

    Authentication & Roles: Scaffold an auth system with three roles: Job Seeker, Employer, and Administrator .

    Strict Database Normalization: - Ensure the job_seeker_profiles table connects to users.

        The address must be broken down into separate queryable columns (country, city, district, street, postal code) using foreign keys where applicable . Absolutely no JSON, plain text blobs, or comma-separated values for structured data .

        Create migrations for reference tables (Lookup tables): roles, genders, cv_categories, countries, cities, districts, institutions, degrees, majors, job_titles, employment_types, industries, certificates, issuing_organizations, skills, and proficiencies .

    Dynamic One-to-Many Relationships: Create the migration schema and Eloquent model relationships (hasMany, belongsTo) for:

        Education Records 

        Work History 

        Certificates 

        Skills (Maximum 5 per user) 

    MVC Structure: Generate the command list to create the necessary Controllers (e.g., CVController, SearchController, AdminController).

Output Delivery:

    Step-by-step Ubuntu terminal commands (using Composer and Artisan) to initialize the project, configure Git, and generate models/migrations.

    The full PHP code for the most complex database migration (the job_seeker_profiles and addresses tables, enforcing foreign keys).

    The Eloquent Model for JobSeekerProfile.php showing the correct relationship methods.

    A stubbed-out Javascript example (Vanilla JS) demonstrating how to dynamically append a new "Education Record" input block to the DOM, as dynamic frontend forms are required.
Install auth scaffolding with Blade:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
```

Production-oriented app bootstrap:

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Initial Git commit:

```bash
git add .
git commit -m "chore: bootstrap Laravel CV management system"
```

## 2. Artisan generators

Create controllers:

```bash
php artisan make:controller DashboardController
php artisan make:controller CVController
php artisan make:controller SearchController
php artisan make:controller Admin/AdminController
php artisan make:controller Admin/ReferenceDataController
php artisan make:controller ProfileController
```

Create middleware for role-based access:

```bash
php artisan make:middleware EnsureUserHasRole
```

Create core models and migrations:

```bash
php artisan make:model Role -m
php artisan make:model Gender -m
php artisan make:model CvCategory -m
php artisan make:model Country -m
php artisan make:model City -m
php artisan make:model District -m
php artisan make:model Institution -m
php artisan make:model Degree -m
php artisan make:model Major -m
php artisan make:model JobTitle -m
php artisan make:model EmploymentType -m
php artisan make:model Industry -m
php artisan make:model Certificate -m
php artisan make:model IssuingOrganization -m
php artisan make:model Skill -m
php artisan make:model Proficiency -m

php artisan make:model Address -m
php artisan make:model JobSeekerProfile -m
php artisan make:model EducationRecord -m
php artisan make:model WorkHistory -m
php artisan make:model UserCertificate -m
php artisan make:model JobSeekerSkill -m
```

Optional seeders for lookup/reference data:

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder GenderSeeder
php artisan make:seeder EmploymentTypeSeeder
php artisan make:seeder ProficiencySeeder
php artisan make:seeder CvCategorySeeder
php artisan make:seeder CountrySeeder
```

## 3. Normalized schema plan

Core identity:

- `users`
- `roles`
- `users.role_id -> roles.id`
- `genders`

Job seeker profile:

- `job_seeker_profiles.user_id -> users.id`
- `job_seeker_profiles.gender_id -> genders.id`
- `job_seeker_profiles.cv_category_id -> cv_categories.id`
- `job_seeker_profiles.address_id -> addresses.id`

Normalized address chain:

- `countries`
- `cities.country_id -> countries.id`
- `districts.city_id -> cities.id`
- `addresses.country_id -> countries.id`
- `addresses.city_id -> cities.id`
- `addresses.district_id -> districts.id`
- `addresses.street_line`
- `addresses.postal_code`

One-to-many / lookup-backed profile data:

- `education_records.job_seeker_profile_id -> job_seeker_profiles.id`
- `education_records.institution_id -> institutions.id`
- `education_records.degree_id -> degrees.id`
- `education_records.major_id -> majors.id`

- `work_histories.job_seeker_profile_id -> job_seeker_profiles.id`
- `work_histories.job_title_id -> job_titles.id`
- `work_histories.employment_type_id -> employment_types.id`
- `work_histories.industry_id -> industries.id`

- `user_certificates.job_seeker_profile_id -> job_seeker_profiles.id`
- `user_certificates.certificate_id -> certificates.id`
- `user_certificates.issuing_organization_id -> issuing_organizations.id`

- `job_seeker_skills.job_seeker_profile_id -> job_seeker_profiles.id`
- `job_seeker_skills.skill_id -> skills.id`
- `job_seeker_skills.proficiency_id -> proficiencies.id`

`job_seeker_skills` should have a unique composite index on `job_seeker_profile_id + skill_id`. The maximum of 5 skills per user is best enforced in Laravel validation or a database trigger; standard MySQL foreign keys and simple constraints cannot reliably enforce cross-row row-count limits.

## 4. Reference migration patterns

`roles`:

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50)->unique();
    $table->string('slug', 50)->unique();
    $table->timestamps();
});
```

Update `users` to link role:

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')
        ->after('id')
        ->constrained('roles')
        ->restrictOnDelete();
});
```

`countries`, `cities`, `districts`:

```php
Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150)->unique();
    $table->string('iso2', 2)->unique();
    $table->string('iso3', 3)->unique();
    $table->timestamps();
});

Schema::create('cities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained()->restrictOnDelete();
    $table->string('name', 150);
    $table->timestamps();
    $table->unique(['country_id', 'name']);
});

Schema::create('districts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('city_id')->constrained()->restrictOnDelete();
    $table->string('name', 150);
    $table->timestamps();
    $table->unique(['city_id', 'name']);
});
```

## 5. Complex migration: `addresses` and `job_seeker_profiles`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete();
            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();
            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete();
            $table->string('street_line', 255);
            $table->string('postal_code', 20)->nullable();
            $table->timestamps();

            $table->index(['country_id', 'city_id']);
            $table->index(['city_id', 'district_id']);
            $table->unique(
                ['country_id', 'city_id', 'district_id', 'street_line', 'postal_code'],
                'addresses_unique_location'
            );
        });

        Schema::create('job_seeker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('gender_id')
                ->nullable()
                ->constrained('genders')
                ->nullOnDelete();
            $table->foreignId('cv_category_id')
                ->nullable()
                ->constrained('cv_categories')
                ->nullOnDelete();
            $table->foreignId('address_id')
                ->nullable()
                ->constrained('addresses')
                ->nullOnDelete();

            $table->string('headline', 150)->nullable();
            $table->text('professional_summary')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->boolean('is_open_to_work')->default(true);
            $table->boolean('is_searchable')->default(true);
            $table->timestamps();

            $table->index(['cv_category_id', 'is_searchable']);
            $table->index(['gender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_seeker_profiles');
        Schema::dropIfExists('addresses');
    }
};
```

## 6. Relationship migrations for one-to-many sections

`education_records`:

```php
Schema::create('education_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();
    $table->foreignId('institution_id')->constrained()->restrictOnDelete();
    $table->foreignId('degree_id')->constrained()->restrictOnDelete();
    $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->decimal('gpa', 3, 2)->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

`work_histories`:

```php
Schema::create('work_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();
    $table->foreignId('job_title_id')->constrained()->restrictOnDelete();
    $table->foreignId('employment_type_id')->constrained()->restrictOnDelete();
    $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
    $table->string('company_name', 150);
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->boolean('is_current')->default(false);
    $table->text('responsibilities')->nullable();
    $table->timestamps();
});
```

`user_certificates`:

```php
Schema::create('user_certificates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();
    $table->foreignId('certificate_id')->constrained()->restrictOnDelete();
    $table->foreignId('issuing_organization_id')->constrained()->restrictOnDelete();
    $table->string('credential_id', 100)->nullable();
    $table->date('issued_on')->nullable();
    $table->date('expires_on')->nullable();
    $table->string('credential_url', 255)->nullable();
    $table->timestamps();
});
```

`job_seeker_skills`:

```php
Schema::create('job_seeker_skills', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_seeker_profile_id')->constrained()->cascadeOnDelete();
    $table->foreignId('skill_id')->constrained()->restrictOnDelete();
    $table->foreignId('proficiency_id')->constrained()->restrictOnDelete();
    $table->timestamps();

    $table->unique(['job_seeker_profile_id', 'skill_id']);
});
```

## 7. `JobSeekerProfile` Eloquent model

`app/Models/JobSeekerProfile.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSeekerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender_id',
        'cv_category_id',
        'address_id',
        'headline',
        'professional_summary',
        'date_of_birth',
        'phone',
        'linkedin_url',
        'portfolio_url',
        'is_open_to_work',
        'is_searchable',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_open_to_work' => 'boolean',
        'is_searchable' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function cvCategory(): BelongsTo
    {
        return $this->belongsTo(CvCategory::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function educationRecords(): HasMany
    {
        return $this->hasMany(EducationRecord::class);
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function userCertificates(): HasMany
    {
        return $this->hasMany(UserCertificate::class);
    }

    public function jobSeekerSkills(): HasMany
    {
        return $this->hasMany(JobSeekerSkill::class);
    }
}
```

## 8. Companion relationship stubs

`app/Models/User.php`

```php
public function role(): BelongsTo
{
    return $this->belongsTo(Role::class);
}

public function jobSeekerProfile(): HasOne
{
    return $this->hasOne(JobSeekerProfile::class);
}
```

`app/Models/Address.php`

```php
public function country(): BelongsTo
{
    return $this->belongsTo(Country::class);
}

public function city(): BelongsTo
{
    return $this->belongsTo(City::class);
}

public function district(): BelongsTo
{
    return $this->belongsTo(District::class);
}

public function jobSeekerProfiles(): HasMany
{
    return $this->hasMany(JobSeekerProfile::class);
}
```

## 9. Core route structure

`routes/web.php`

```php
<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ReferenceDataController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:job_seeker')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/cv', [CVController::class, 'index'])->name('cv.index');
        Route::get('/cv/create', [CVController::class, 'create'])->name('cv.create');
        Route::post('/cv', [CVController::class, 'store'])->name('cv.store');
        Route::get('/cv/{profile}/edit', [CVController::class, 'edit'])->name('cv.edit');
        Route::put('/cv/{profile}', [CVController::class, 'update'])->name('cv.update');
        Route::delete('/cv/{profile}', [CVController::class, 'destroy'])->name('cv.destroy');
    });

    Route::middleware('role:employer')->group(function () {
        Route::get('/search/candidates', [SearchController::class, 'index'])->name('search.index');
        Route::get('/search/candidates/results', [SearchController::class, 'results'])->name('search.results');
        Route::get('/search/candidates/{profile}', [SearchController::class, 'show'])->name('search.show');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:administrator')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::resource('reference-data', ReferenceDataController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
```

## 10. Minimal role middleware idea

The route file above assumes a middleware alias such as:

```php
public function handle($request, Closure $next, string $role)
{
    if (! $request->user() || $request->user()->role->slug !== $role) {
        abort(403);
    }

    return $next($request);
}
```

Use role slugs:

- `job_seeker`
- `employer`
- `administrator`

## 11. Vanilla JS dynamic education form block

```html
<div id="education-records"></div>
<button type="button" id="add-education-btn">Add Education</button>
```

```html
<template id="education-record-template">
    <div class="education-record">
        <hr>
        <label>
            Institution
            <input type="text" name="education_records[__INDEX__][institution_name]" required>
        </label>

        <label>
            Degree
            <input type="text" name="education_records[__INDEX__][degree_name]" required>
        </label>

        <label>
            Major
            <input type="text" name="education_records[__INDEX__][major_name]">
        </label>

        <label>
            Start Date
            <input type="date" name="education_records[__INDEX__][start_date]" required>
        </label>

        <label>
            End Date
            <input type="date" name="education_records[__INDEX__][end_date]">
        </label>

        <button type="button" class="remove-education-btn">Remove</button>
    </div>
</template>
```

```js
document.addEventListener('DOMContentLoaded', () => {
    const recordsContainer = document.getElementById('education-records');
    const addButton = document.getElementById('add-education-btn');
    const template = document.getElementById('education-record-template');

    let educationIndex = 0;

    const addEducationRecord = () => {
        const html = template.innerHTML.replaceAll('__INDEX__', educationIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();

        const record = wrapper.firstElementChild;

        record.querySelector('.remove-education-btn').addEventListener('click', () => {
            record.remove();
        });

        recordsContainer.appendChild(record);
        educationIndex += 1;
    };

    addButton.addEventListener('click', addEducationRecord);

    addEducationRecord();
});
```

## 12. Recommended next implementation steps

```bash
php artisan migrate
php artisan db:seed
php artisan make:request StoreJobSeekerProfileRequest
php artisan make:request UpdateJobSeekerProfileRequest
php artisan make:policy JobSeekerProfilePolicy --model=JobSeekerProfile
php artisan serve
```

## Notes

- This scaffold favors strict normalization over denormalized blobs.
- Structured address data is fully queryable through foreign keys and scalar columns.
- The 5-skill maximum should be enforced in request validation plus service-layer checks, or via a MySQL trigger if you want database-only enforcement.
- Laravel 12 starter-kit and migration patterns were cross-checked against the official Laravel docs:
  - https://laravel.com/docs/12.x/installation
  - https://laravel.com/docs/12.x/starter-kits
  - https://laravel.com/docs/12.x/migrations
