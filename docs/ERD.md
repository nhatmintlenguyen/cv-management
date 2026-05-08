# OneCV Main Relationship ERD

This diagram intentionally shows only the most important relationships from
`schema.sql`. The goal is to prove that the database design implements:

- One-to-one (1:1)
- One-to-many (1:N)
- Many-to-many (M:N)

```mermaid
erDiagram
    ROLES ||--o{ USERS : "1:N"
    USERS ||--o| CVS : "1:1"
    USERS ||--o{ COMPANIES : "1:N"
    COMPANIES ||--o{ JOB_VACANCIES : "1:N"

    CVS ||--o{ CV_EDUCATIONS : "1:N"
    CVS ||--o{ CV_WORK_HISTORIES : "1:N"
    CVS ||--o{ CV_CERTIFICATES : "1:N"

    CVS ||--o{ CV_SKILLS : "1:N"
    SKILLS ||--o{ CV_SKILLS : "1:N"
    SKILL_PROFICIENCY_LEVELS ||--o{ CV_SKILLS : "1:N"

    JOB_VACANCIES ||--o{ JOB_VACANCY_SKILLS : "1:N"
    SKILLS ||--o{ JOB_VACANCY_SKILLS : "1:N"
    SKILL_PROFICIENCY_LEVELS ||--o{ JOB_VACANCY_SKILLS : "1:N"

    COUNTRIES ||--o{ CITIES : "1:N"
    CITIES ||--o{ DISTRICTS : "1:N"

    CV_CATEGORIES ||--o{ CVS : "1:N"
    JOB_CATEGORIES ||--o{ JOB_VACANCIES : "1:N"

    ROLES {
        BIGINT id PK
        VARCHAR name
    }

    USERS {
        BIGINT id PK
        BIGINT role_id FK
        VARCHAR full_name
        VARCHAR email
        VARCHAR password_hash
        VARCHAR avatar_url
        ENUM status
    }

    CVS {
        BIGINT id PK
        BIGINT user_id FK_UK
        BIGINT cv_category_id FK
        VARCHAR full_name
        DATE date_of_birth
        VARCHAR email
        VARCHAR phone_number
        TEXT summary
        BOOLEAN is_completed
    }

    CV_EDUCATIONS {
        BIGINT id PK
        BIGINT cv_id FK
        BIGINT institution_id FK
        BIGINT degree_level_id FK
        BIGINT major_id FK
        YEAR start_year
        YEAR end_year
    }

    CV_WORK_HISTORIES {
        BIGINT id PK
        BIGINT cv_id FK
        BIGINT job_title_id FK
        BIGINT employment_type_id FK
        BIGINT industry_id FK
        VARCHAR company_name
        YEAR start_year
        YEAR end_year
    }

    CV_CERTIFICATES {
        BIGINT id PK
        BIGINT cv_id FK
        BIGINT certificate_name_id FK
        BIGINT issuing_organization_id FK
        YEAR year_issued
    }

    CV_SKILLS {
        BIGINT id PK
        BIGINT cv_id FK
        BIGINT skill_id FK
        BIGINT proficiency_level_id FK
    }

    COMPANIES {
        BIGINT id PK
        BIGINT employer_user_id FK
        VARCHAR name
        VARCHAR avatar_url
        TEXT description
    }

    JOB_VACANCIES {
        BIGINT id PK
        BIGINT employer_user_id FK
        BIGINT company_id FK
        BIGINT job_category_id FK
        BIGINT job_title_id FK
        BIGINT employment_type_id FK
        BIGINT job_level_id FK
        BIGINT salary_range_id FK
        ENUM status
    }

    JOB_VACANCY_SKILLS {
        BIGINT id PK
        BIGINT job_vacancy_id FK
        BIGINT skill_id FK
        BIGINT minimum_proficiency_level_id FK
    }

    SKILLS {
        BIGINT id PK
        VARCHAR name
    }

    SKILL_PROFICIENCY_LEVELS {
        BIGINT id PK
        VARCHAR name
        TINYINT level_value
    }

    COUNTRIES {
        BIGINT id PK
        VARCHAR name
    }

    CITIES {
        BIGINT id PK
        BIGINT country_id FK
        VARCHAR name
    }

    DISTRICTS {
        BIGINT id PK
        BIGINT city_id FK
        VARCHAR name
    }

    CV_CATEGORIES {
        BIGINT id PK
        VARCHAR name
    }

    JOB_CATEGORIES {
        BIGINT id PK
        VARCHAR name
    }
```

## 3.2.1 One-to-One (1:1) Relationships

The schema implements a one-to-one relationship between `users` and `cvs`.
Each user account can own at most one CV because the `cvs.user_id` column is
both a foreign key and a unique key:

```sql
user_id BIGINT UNSIGNED NOT NULL UNIQUE
```

This means one row in `users` can be linked to zero or one row in `cvs`, and
one row in `cvs` must belong to exactly one user. This design matches the
current system rule that each job seeker manages one active online CV.

The relationship also uses `ON DELETE CASCADE`, so when a user is deleted, the
associated CV is deleted automatically.

## 3.2.2 One-to-Many (1:N) Relationships

One-to-many relationships are used for data that can have multiple child
records.

Important examples:

- One `role` has many `users`.
- One `user` can own many `companies`.
- One `company` can publish many `job_vacancies`.
- One `cv` can have many `cv_educations`.
- One `cv` can have many `cv_work_histories`.
- One `cv` can have many `cv_certificates`.
- One `country` has many `cities`.
- One `city` has many `districts`.

For example, `companies.employer_user_id` references `users.id`, which allows a
single employer account to manage multiple company profiles. Similarly,
`job_vacancies.company_id` references `companies.id`, so a single company can
publish multiple job vacancies.

## 3.2.3 Many-to-Many (M:N) Relationships

Many-to-many relationships are implemented through bridge tables.

The relationship between CVs and skills is many-to-many:

- One CV can contain many skills.
- One skill can appear in many CVs.

This is implemented through `cv_skills`:

```sql
cv_skills (
  cv_id,
  skill_id,
  proficiency_level_id
)
```

The table also stores contextual data through `proficiency_level_id`, which
describes how strong the candidate is in that skill. A unique key on
`(cv_id, skill_id)` prevents the same skill from being added twice to the same
CV.

The same bridge-table pattern is used for job vacancies:

```sql
job_vacancy_skills (
  job_vacancy_id,
  skill_id,
  minimum_proficiency_level_id
)
```

This allows one job vacancy to require multiple skills, while one skill can be
required by many different job vacancies. The
`minimum_proficiency_level_id` column stores the required skill level for that
specific job.
