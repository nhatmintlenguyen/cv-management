## CV Management Website

MVC skeleton for the Online CV Management & Search System assignment.

The project is intentionally scaffolded with mostly empty code files so each
feature can be implemented step by step.

### Project Goal

This project is prepared for the Assignment 1 requirement: an Online CV
Management & Search System.

The system should support three roles:

- `job_seeker`: creates and manages one online CV.
- `employer`: searches and views CVs in read-only mode.
- `admin`: manages users and reference/lookup data.

This skeleton does not implement the full application yet. It only provides a
clear MVC folder structure so the system can be built gradually.

### Folder Structure

```text
app/
  Controllers/
  Core/
  Models/
config/
database/
docs/
public/
  css/
  js/
  images/
resources/
  views/
routes/
storage/
tests/
```

### Folder Meaning

#### `app/`

Main application source code. This is where the MVC parts live.

#### `app/Controllers/`

Controllers receive requests, call the necessary models, and return views.

Important files:

- `AuthController.php`: handles registration, login, logout, and role-based
  access preparation.
- `DashboardController.php`: can route users to different dashboards depending
  on their role.
- `CVController.php`: handles job seeker CV creation, editing, updating, and
  viewing.
- `SearchController.php`: handles employer CV search, combined filters, sorting,
  and read-only CV viewing.
- `AdminController.php`: handles admin pages such as user management.
- `ReferenceDataController.php`: handles lookup/reference data such as skills,
  CV categories, degrees, majors, industries, employment types, certificates,
  and locations.

#### `app/Core/`

Core framework-like classes for a simple custom MVC application.

Important files:

- `App.php`: can be used as the application bootstrap class.
- `Router.php`: should map URLs to controller methods.
- `Controller.php`: base controller class for shared controller behavior.
- `Model.php`: base model class for shared database model behavior.
- `Database.php`: should contain the database connection logic.
- `View.php`: should load view files and pass data from controllers to views.

#### `app/Models/`

Models represent database tables and contain database-related logic.

Important files:

- `User.php`: represents system users: job seekers, employers, and admins.
- `Role.php`: represents user roles.
- `CV.php`: represents the main CV record. Each job seeker should have only one
  CV.
- `CVEducation.php`: represents multiple education records for a CV.
- `CVWorkHistory.php`: represents multiple work history records for a CV.
- `CVCertificate.php`: represents multiple certificate records for a CV.
- `CVSkill.php`: represents strongest skills for a CV. The assignment requires
  a maximum of 5 skills.
- `CVCategory.php`: represents predefined professional CV categories.
- `Country.php`, `City.php`, `District.php`: represent structured address
  location data.
- `DegreeLevel.php`, `Major.php`, `Institution.php`: represent education lookup
  data.
- `JobTitle.php`, `EmploymentType.php`, `Industry.php`: represent work history
  lookup data.
- `Skill.php`, `SkillProficiencyLevel.php`: represent selectable skills and
  proficiency levels.
- `CertificateName.php`, `IssuingOrganization.php`: represent certificate
  lookup data.
- `CVTemplate.php`: represents available CV presentation templates such as
  Modern, Classic, and Minimal.

#### `resources/views/`

Views contain the HTML/PHP templates shown to users. Views should focus on
displaying data, not database logic.

Important folders and files:

- `layouts/main.php`: shared layout for pages, such as header, navigation, main
  content area, and footer.
- `auth/login.php`: login page.
- `auth/register.php`: registration page.
- `cv/create.php`: form for creating a CV.
- `cv/edit-personal-info.php`: Step 1 form for editing personal information.
- `cv/edit-academic.php`: Step 2 form for education and work history.
- `cv/edit-qualifications.php`: Step 3 form for certificates and skills.
- `cv/edit-review.php`: Step 4 final review and template selection.
- `cv/show.php`: structured read-only CV display.
- `cv/templates-modern.php`: Modern CV presentation template.
- `cv/templates-classic.php`: Classic CV presentation template.
- `cv/templates-minimal.php`: Minimal CV presentation template.
- `search/index.php`: employer search/filter form.
- `search/show.php`: employer read-only CV preview page.
- `admin/users.php`: admin user management page.
- `admin/reference-data.php`: admin reference data management page.
- `errors/403.php`: forbidden access page.
- `errors/404.php`: page not found view.

#### `config/`

Configuration files for the application.

Important files:

- `app.php`: general application settings such as app name, environment, or base
  URL.
- `database.php`: database connection settings such as host, database name,
  username, password, and charset.

#### `database/`

Database-related files.

Important files and folders:

- `schema.sql`: full MySQL database schema for the assignment.
- `migrations/`: reserved for future migration files if you decide to build a
  migration system.
- `seeders/reference_data.sql`: reserved for inserting default lookup data such
  as roles, skills, categories, countries, degrees, and employment types.

#### `docs/`

Documentation for design and implementation decisions.

Important files:

- `ERD.md`: can describe the database ER diagram and relationships.
- `IMPLEMENTATION_PLAN.md`: can track the order in which features will be
  implemented.

#### `public/`

Public web root. Files in this folder are directly accessible by the browser.

Important files and folders:

- `index.php`: application entry point. In an MVC app, all requests usually pass
  through this file first.
- `css/app.css`: main stylesheet.
- `js/app.js`: general JavaScript for the site.
- `js/cv-form.js`: JavaScript for dynamic CV forms such as Add Degree,
  Add Work History, Add Certificate, and Add Skill.
- `images/`: stores public images used by the UI.

#### `routes/`

Route definition files. Routes connect URLs to controller methods.

Important files:

- `web.php`: main browser routes, such as home, login, register, CV pages, and
  search pages.
- `admin.php`: admin-only routes.
- `api.php`: reserved for future AJAX/API endpoints, for example loading cities
  by country or districts by city.

#### `storage/`

Writable application storage.

Important folders:

- `logs/`: application log files can be stored here.

The assignment does not require CV file uploads, so this project should not
store uploaded CV files.

#### `tests/`

Reserved for automated tests.

Important folders:

- `Feature/`: tests for user-facing features such as login, CV creation, search,
  and admin pages.
- `Unit/`: tests for smaller isolated logic such as validation helpers or model
  methods.

### Root Files

Important root-level files:

- `schema.sql`: MySQL schema for the normalized CV management database.
- `mockdata.json`: sample/mock data that can be used later for development or
  testing.
- `Assignment 1. CV Online Management.pdf`: original assignment description.
- `BOILERPLATE.md`: earlier setup notes for a possible Laravel/MySQL setup.
- `.gitignore`: tells Git which generated/local files should not be committed.

### Main Assignment Areas

- Authentication and role-based access: `app/Controllers/AuthController.php`
- Job seeker CV management: `app/Controllers/CVController.php`
- Employer CV search and filtering: `app/Controllers/SearchController.php`
- Admin user/reference data management: `app/Controllers/AdminController.php`
- Lookup/reference tables: `app/Controllers/ReferenceDataController.php`
- CV dynamic forms: `public/js/cv-form.js`
- Database schema: `schema.sql`

### Local Database Setup With XAMPP

This project is intended to run with XAMPP using local MySQL/MariaDB and
phpMyAdmin.

Default local database config is stored in `config/database.php`:

```php
'host' => '127.0.0.1',
'port' => 3306,
'database' => 'cv_management',
'username' => 'root',
'password' => '',
```

These values match the usual XAMPP default setup:

- MySQL host: `127.0.0.1`
- MySQL port: `3306`
- MySQL user: `root`
- MySQL password: empty
- Database name: `cv_management`

If your XAMPP MySQL uses a password, update `config/database.php`.

#### Option 1: Create Tables With phpMyAdmin

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Open `http://localhost/phpmyadmin/`.
3. Create a new database named `cv_management`.
4. Select the `cv_management` database.
5. Open the Import tab.
6. Choose `schema.sql`.
7. Click Import.

After import, phpMyAdmin should show tables such as:

- `users`
- `roles`
- `cvs`
- `cv_educations`
- `cv_work_histories`
- `cv_certificates`
- `cv_skills`
- `skills`
- `cv_categories`
- `countries`
- `cities`
- `districts`

#### Option 2: Create Database and Tables With PHP Script

You can also run the setup script from the project root:

```bash
php database/setup.php
```

The script will:

1. Read database settings from `config/database.php`.
2. Create the `cv_management` database if it does not exist.
3. Import all tables, indexes, foreign keys, seed data, and triggers from
   `schema.sql`.

If the command succeeds, you should see:

```text
Database 'cv_management' was created and schema.sql was imported successfully.
```

Then open `http://localhost/phpmyadmin/` and check the `cv_management`
database.

Running `database/setup.php` again will recreate the schema because
`schema.sql` drops existing project tables before creating them. Do not run it
after adding real data unless you want to reset the database.

### Mailpit Setup for Forgot Password

The forgot password feature sends reset emails through a local SMTP server.
For local development, this project uses Mailpit in Docker. Mailpit catches
emails locally so you can test password reset without sending real email.

#### 1. Pull the Mailpit Docker Image

```bash
docker pull axllent/mailpit:edge
```

#### 2. Start the Mailpit Container

```bash
docker run -d --name onecv-mailpit \
  -p 8025:8025 \
  -p 1025:1025 \
  axllent/mailpit:edge
```

Ports used by Mailpit:

- `8025`: web inbox UI.
- `1025`: SMTP server used by the PHP application.

Open the Mailpit inbox at:

```text
http://localhost:8025
```

#### 3. Project Mail Configuration

The project SMTP settings are stored in `config/mail.php`:

```php
'host' => '127.0.0.1',
'port' => 1025,
'username' => null,
'password' => null,
'encryption' => null,
'from_email' => 'no-reply@onecv.local',
'from_name' => 'OneCV',
```

Mailpit does not require SMTP authentication or TLS in local development.

#### 4. Required Database Table

Forgot password needs the `password_resets` table.

If you created the database from the latest `schema.sql`, this table already
exists. If your database was created before this feature was added, run the SQL
file below in phpMyAdmin:

```text
database/migrations/2026_05_06_create_password_resets.sql
```

#### 5. Test the Forgot Password Flow

1. Start Apache and MySQL from XAMPP.
2. Make sure the Mailpit container is running.
3. Open the login page:

   ```text
   http://localhost/cv-management/public/login
   ```

4. Click `Forgot password?`.
5. Enter an email address that exists in the `users` table.
6. Open Mailpit:

   ```text
   http://localhost:8025
   ```

7. Open the reset email and click the reset link.
8. Enter a new password and log in with the updated password.

Useful Docker commands:

```bash
docker ps
docker start onecv-mailpit
docker stop onecv-mailpit
docker rm -f onecv-mailpit
```

#### Serving the Project With XAMPP

For a simple XAMPP setup, place or symlink this project inside XAMPP's web root.
Common web root locations are:

- Windows: `C:\xampp\htdocs`
- Linux: `/opt/lampp/htdocs`
- macOS: `/Applications/XAMPP/htdocs`

Because this MVC project uses `public/index.php` as the entry point, the cleaner
URL should point to the `public` folder:

```text
http://localhost/cv-management/public/
```

Later, Apache can be configured so the document root points directly to
`public/`, but for coursework development the URL above is enough.

### Suggested Implementation Order

1. Set up database connection in `config/database.php` and `app/Core/Database.php`.
2. Implement routing in `public/index.php`, `app/Core/Router.php`, and
   `routes/web.php`.
3. Implement base `Controller`, `Model`, and `View` classes.
4. Implement authentication and roles.
5. Implement job seeker CV create/edit pages.
6. Implement dynamic forms for education, work history, certificates, and
   skills.
7. Implement employer search and filtering.
8. Implement CV templates.
9. Implement admin user and reference data management.
10. Add validation and tests.
