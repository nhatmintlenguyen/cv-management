-- Online CV Management & Search System
-- MySQL 8+ schema

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS job_vacancy_skills;
DROP TABLE IF EXISTS job_vacancies;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS cv_skills;
DROP TABLE IF EXISTS cv_certificates;
DROP TABLE IF EXISTS cv_work_histories;
DROP TABLE IF EXISTS cv_educations;
DROP TABLE IF EXISTS cvs;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS users;

DROP TABLE IF EXISTS work_arrangements;
DROP TABLE IF EXISTS salary_types;
DROP TABLE IF EXISTS salary_ranges;
DROP TABLE IF EXISTS job_levels;
DROP TABLE IF EXISTS job_categories;
DROP TABLE IF EXISTS cv_templates;
DROP TABLE IF EXISTS certificate_names;
DROP TABLE IF EXISTS issuing_organizations;
DROP TABLE IF EXISTS skill_proficiency_levels;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS industries;
DROP TABLE IF EXISTS employment_types;
DROP TABLE IF EXISTS job_titles;
DROP TABLE IF EXISTS institutions;
DROP TABLE IF EXISTS majors;
DROP TABLE IF EXISTS degree_levels;
DROP TABLE IF EXISTS cv_categories;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS cities;
DROP TABLE IF EXISTS countries;
DROP TABLE IF EXISTS genders;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar_url VARCHAR(2048) NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  KEY idx_password_resets_user (user_id),
  KEY idx_password_resets_expires_at (expires_at),

  CONSTRAINT fk_password_resets_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE genders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE countries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  country_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,

  UNIQUE KEY uq_cities_country_name (country_id, name),
  CONSTRAINT fk_cities_country
    FOREIGN KEY (country_id) REFERENCES countries(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE districts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,

  UNIQUE KEY uq_districts_city_name (city_id, name),
  CONSTRAINT fk_districts_city
    FOREIGN KEY (city_id) REFERENCES cities(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE degree_levels (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE majors (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE institutions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_titles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_levels (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE employment_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE industries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE salary_ranges (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL UNIQUE,
  min_salary DECIMAL(12,2) NULL,
  max_salary DECIMAL(12,2) NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'USD',
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE salary_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE work_arrangements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skill_proficiency_levels (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  level_value TINYINT UNSIGNED NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,

  CONSTRAINT chk_skill_proficiency_level_value
    CHECK (level_value BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE certificate_names (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE issuing_organizations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_templates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cvs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  cv_template_id BIGINT UNSIGNED NULL,
  cv_category_id BIGINT UNSIGNED NOT NULL,
  gender_id BIGINT UNSIGNED NOT NULL,
  country_id BIGINT UNSIGNED NOT NULL,
  city_id BIGINT UNSIGNED NOT NULL,
  district_id BIGINT UNSIGNED NULL,

  full_name VARCHAR(150) NOT NULL,
  date_of_birth DATE NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone_number VARCHAR(30) NOT NULL,
  street_address VARCHAR(255) NOT NULL,
  postal_code VARCHAR(30) NULL,
  summary TEXT NULL,
  is_completed BOOLEAN NOT NULL DEFAULT FALSE,
  completed_at DATETIME NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_cvs_category (cv_category_id),
  KEY idx_cvs_location (country_id, city_id),
  KEY idx_cvs_completed (is_completed, completed_at),
  KEY idx_cvs_updated_at (updated_at),
  FULLTEXT KEY ft_cvs_keyword (full_name, summary),

  CONSTRAINT fk_cvs_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cvs_template
    FOREIGN KEY (cv_template_id) REFERENCES cv_templates(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT fk_cvs_category
    FOREIGN KEY (cv_category_id) REFERENCES cv_categories(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cvs_gender
    FOREIGN KEY (gender_id) REFERENCES genders(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cvs_country
    FOREIGN KEY (country_id) REFERENCES countries(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cvs_city
    FOREIGN KEY (city_id) REFERENCES cities(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cvs_district
    FOREIGN KEY (district_id) REFERENCES districts(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_educations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id BIGINT UNSIGNED NOT NULL,
  institution_id BIGINT UNSIGNED NOT NULL,
  degree_level_id BIGINT UNSIGNED NOT NULL,
  major_id BIGINT UNSIGNED NOT NULL,
  start_year YEAR NOT NULL,
  end_year YEAR NOT NULL,
  description TEXT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_cv_educations_cv (cv_id),
  KEY idx_cv_educations_degree (degree_level_id),

  CONSTRAINT chk_cv_educations_years
    CHECK (end_year >= start_year),
  CONSTRAINT fk_cv_educations_cv
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cv_educations_institution
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_educations_degree
    FOREIGN KEY (degree_level_id) REFERENCES degree_levels(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_educations_major
    FOREIGN KEY (major_id) REFERENCES majors(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_work_histories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id BIGINT UNSIGNED NOT NULL,
  job_title_id BIGINT UNSIGNED NOT NULL,
  employment_type_id BIGINT UNSIGNED NOT NULL,
  industry_id BIGINT UNSIGNED NOT NULL,

  company_name VARCHAR(180) NOT NULL,
  start_year YEAR NOT NULL,
  end_year YEAR NULL,
  is_current BOOLEAN NOT NULL DEFAULT FALSE,
  job_description TEXT NOT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_cv_work_histories_cv (cv_id),
  KEY idx_cv_work_histories_industry (industry_id),
  FULLTEXT KEY ft_cv_work_histories_keyword (company_name, job_description),

  CONSTRAINT chk_cv_work_histories_current_end_year
    CHECK (
      (is_current = TRUE AND end_year IS NULL)
      OR
      (is_current = FALSE AND end_year IS NOT NULL AND end_year >= start_year)
    ),
  CONSTRAINT fk_cv_work_histories_cv
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cv_work_histories_job_title
    FOREIGN KEY (job_title_id) REFERENCES job_titles(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_work_histories_employment_type
    FOREIGN KEY (employment_type_id) REFERENCES employment_types(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_work_histories_industry
    FOREIGN KEY (industry_id) REFERENCES industries(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_certificates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id BIGINT UNSIGNED NOT NULL,
  certificate_name_id BIGINT UNSIGNED NOT NULL,
  issuing_organization_id BIGINT UNSIGNED NOT NULL,
  year_issued YEAR NOT NULL,
  description TEXT NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_cv_certificates_cv (cv_id),
  FULLTEXT KEY ft_cv_certificates_keyword (description),

  CONSTRAINT fk_cv_certificates_cv
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cv_certificates_certificate_name
    FOREIGN KEY (certificate_name_id) REFERENCES certificate_names(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_certificates_issuing_organization
    FOREIGN KEY (issuing_organization_id) REFERENCES issuing_organizations(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cv_skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cv_id BIGINT UNSIGNED NOT NULL,
  skill_id BIGINT UNSIGNED NOT NULL,
  proficiency_level_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_cv_skills_cv_skill (cv_id, skill_id),
  KEY idx_cv_skills_skill (skill_id),
  KEY idx_cv_skills_proficiency (proficiency_level_id),

  CONSTRAINT fk_cv_skills_cv
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cv_skills_skill
    FOREIGN KEY (skill_id) REFERENCES skills(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_cv_skills_proficiency
    FOREIGN KEY (proficiency_level_id) REFERENCES skill_proficiency_levels(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE companies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employer_user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  avatar_url VARCHAR(2048) NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_companies_employer_name (employer_user_id, name),
  KEY idx_companies_employer (employer_user_id),

  CONSTRAINT fk_companies_employer
    FOREIGN KEY (employer_user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_vacancies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employer_user_id BIGINT UNSIGNED NOT NULL,
  company_id BIGINT UNSIGNED NOT NULL,

  job_title_id BIGINT UNSIGNED NOT NULL,
  job_category_id BIGINT UNSIGNED NOT NULL,
  employment_type_id BIGINT UNSIGNED NOT NULL,
  industry_id BIGINT UNSIGNED NOT NULL,
  job_level_id BIGINT UNSIGNED NOT NULL,
  number_of_openings INT UNSIGNED NOT NULL DEFAULT 1,

  country_id BIGINT UNSIGNED NOT NULL,
  city_id BIGINT UNSIGNED NOT NULL,
  district_id BIGINT UNSIGNED NULL,
  work_arrangement_id BIGINT UNSIGNED NOT NULL,

  salary_range_id BIGINT UNSIGNED NOT NULL,
  salary_type_id BIGINT UNSIGNED NOT NULL,
  benefits TEXT NULL,

  responsibilities TEXT NOT NULL,
  required_qualifications TEXT NOT NULL,
  preferred_skills TEXT NULL,
  additional_notes TEXT NULL,

  minimum_degree_level_id BIGINT UNSIGNED NOT NULL,
  minimum_years_experience TINYINT UNSIGNED NOT NULL DEFAULT 0,

  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_job_vacancies_employer (employer_user_id),
  KEY idx_job_vacancies_company (company_id),
  KEY idx_job_vacancies_status (status),
  KEY idx_job_vacancies_category (job_category_id),
  KEY idx_job_vacancies_location (country_id, city_id),
  KEY idx_job_vacancies_salary (salary_range_id),
  KEY idx_job_vacancies_updated_at (updated_at),
  FULLTEXT KEY ft_job_vacancies_keyword (
    responsibilities,
    required_qualifications,
    preferred_skills,
    additional_notes
  ),

  CONSTRAINT fk_job_vacancies_employer
    FOREIGN KEY (employer_user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_job_vacancies_company
    FOREIGN KEY (company_id) REFERENCES companies(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_title
    FOREIGN KEY (job_title_id) REFERENCES job_titles(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_category
    FOREIGN KEY (job_category_id) REFERENCES job_categories(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_employment_type
    FOREIGN KEY (employment_type_id) REFERENCES employment_types(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_industry
    FOREIGN KEY (industry_id) REFERENCES industries(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_level
    FOREIGN KEY (job_level_id) REFERENCES job_levels(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_country
    FOREIGN KEY (country_id) REFERENCES countries(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_city
    FOREIGN KEY (city_id) REFERENCES cities(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_district
    FOREIGN KEY (district_id) REFERENCES districts(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT fk_job_vacancies_work_arrangement
    FOREIGN KEY (work_arrangement_id) REFERENCES work_arrangements(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_salary_range
    FOREIGN KEY (salary_range_id) REFERENCES salary_ranges(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_salary_type
    FOREIGN KEY (salary_type_id) REFERENCES salary_types(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancies_min_degree
    FOREIGN KEY (minimum_degree_level_id) REFERENCES degree_levels(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_vacancy_skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_vacancy_id BIGINT UNSIGNED NOT NULL,
  skill_id BIGINT UNSIGNED NOT NULL,
  minimum_proficiency_level_id BIGINT UNSIGNED NOT NULL,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_job_vacancy_skills_job_skill (job_vacancy_id, skill_id),
  KEY idx_job_vacancy_skills_skill (skill_id),
  KEY idx_job_vacancy_skills_proficiency (minimum_proficiency_level_id),

  CONSTRAINT fk_job_vacancy_skills_job
    FOREIGN KEY (job_vacancy_id) REFERENCES job_vacancies(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_job_vacancy_skills_skill
    FOREIGN KEY (skill_id) REFERENCES skills(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_job_vacancy_skills_proficiency
    FOREIGN KEY (minimum_proficiency_level_id) REFERENCES skill_proficiency_levels(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

CREATE TRIGGER trg_cv_skills_limit_before_insert
BEFORE INSERT ON cv_skills
FOR EACH ROW
BEGIN
  IF (
    SELECT COUNT(*)
    FROM cv_skills
    WHERE cv_id = NEW.cv_id
  ) >= 5 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'A CV can have at most 5 strongest skills.';
  END IF;
END//

CREATE TRIGGER trg_cv_skills_limit_before_update
BEFORE UPDATE ON cv_skills
FOR EACH ROW
BEGIN
  IF NEW.cv_id <> OLD.cv_id AND (
    SELECT COUNT(*)
    FROM cv_skills
    WHERE cv_id = NEW.cv_id
  ) >= 5 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'A CV can have at most 5 strongest skills.';
  END IF;
END//

CREATE TRIGGER trg_job_vacancy_skills_limit_before_insert
BEFORE INSERT ON job_vacancy_skills
FOR EACH ROW
BEGIN
  IF (
    SELECT COUNT(*)
    FROM job_vacancy_skills
    WHERE job_vacancy_id = NEW.job_vacancy_id
  ) >= 5 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'A job vacancy can have at most 5 required skills.';
  END IF;
END//

CREATE TRIGGER trg_job_vacancy_skills_limit_before_update
BEFORE UPDATE ON job_vacancy_skills
FOR EACH ROW
BEGIN
  IF NEW.job_vacancy_id <> OLD.job_vacancy_id AND (
    SELECT COUNT(*)
    FROM job_vacancy_skills
    WHERE job_vacancy_id = NEW.job_vacancy_id
  ) >= 5 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'A job vacancy can have at most 5 required skills.';
  END IF;
END//

DELIMITER ;

INSERT INTO roles (name) VALUES
  ('job_seeker'),
  ('employer'),
  ('admin');

INSERT INTO users (role_id, full_name, email, password_hash, status) VALUES
  ((SELECT id FROM roles WHERE name = 'admin'), 'Admin User', 'admin@gmail.com', '$2y$10$q58vc1yIj2e48FvsoYPn2eZvc0thAbfGevkgD3EgKH/y7wUbCigVi', 'active'),
  ((SELECT id FROM roles WHERE name = 'job_seeker'), 'Minh Nguyen', 'minh@gmail.com', '$2y$10$q58vc1yIj2e48FvsoYPn2eZvc0thAbfGevkgD3EgKH/y7wUbCigVi', 'active'),
  ((SELECT id FROM roles WHERE name = 'job_seeker'), 'Linh Tran', 'linh@gmail.com', '$2y$10$q58vc1yIj2e48FvsoYPn2eZvc0thAbfGevkgD3EgKH/y7wUbCigVi', 'active'),
  ((SELECT id FROM roles WHERE name = 'employer'), 'An Pham', 'an@gmail.com', '$2y$10$q58vc1yIj2e48FvsoYPn2eZvc0thAbfGevkgD3EgKH/y7wUbCigVi', 'active'),
  ((SELECT id FROM roles WHERE name = 'employer'), 'Hoa Le', 'hoa@gmail.com', '$2y$10$q58vc1yIj2e48FvsoYPn2eZvc0thAbfGevkgD3EgKH/y7wUbCigVi', 'active');

INSERT INTO genders (name) VALUES
  ('Male'),
  ('Female'),
  ('Other'),
  ('Prefer not to say');

INSERT INTO countries (name) VALUES
  ('Vietnam'),
  ('Laos'),
  ('Cambodia'),
  ('Thailand'),
  ('Singapore'),
  ('Malaysia'),
  ('Japan'),
  ('South Korea'),
  ('Australia'),
  ('United States');

INSERT INTO cities (country_id, name)
SELECT id, 'Hanoi' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Ho Chi Minh City' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Da Nang' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Can Tho' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Hai Phong' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Hue' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Nha Trang' FROM countries WHERE name = 'Vietnam'
UNION ALL SELECT id, 'Vientiane' FROM countries WHERE name = 'Laos'
UNION ALL SELECT id, 'Phnom Penh' FROM countries WHERE name = 'Cambodia'
UNION ALL SELECT id, 'Bangkok' FROM countries WHERE name = 'Thailand'
UNION ALL SELECT id, 'Singapore' FROM countries WHERE name = 'Singapore'
UNION ALL SELECT id, 'Kuala Lumpur' FROM countries WHERE name = 'Malaysia'
UNION ALL SELECT id, 'Tokyo' FROM countries WHERE name = 'Japan'
UNION ALL SELECT id, 'Seoul' FROM countries WHERE name = 'South Korea'
UNION ALL SELECT id, 'Sydney' FROM countries WHERE name = 'Australia'
UNION ALL SELECT id, 'New York' FROM countries WHERE name = 'United States';

INSERT INTO districts (city_id, name)
SELECT cities.id, 'Ba Dinh' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi'
UNION ALL SELECT cities.id, 'Hoan Kiem' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi'
UNION ALL SELECT cities.id, 'Cau Giay' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi'
UNION ALL SELECT cities.id, 'Dong Da' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi'
UNION ALL SELECT cities.id, 'District 1' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'
UNION ALL SELECT cities.id, 'District 3' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'
UNION ALL SELECT cities.id, 'Binh Thanh' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'
UNION ALL SELECT cities.id, 'Thu Duc' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'
UNION ALL SELECT cities.id, 'Hai Chau' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Da Nang'
UNION ALL SELECT cities.id, 'Thanh Khe' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Da Nang'
UNION ALL SELECT cities.id, 'Ninh Kieu' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Can Tho'
UNION ALL SELECT cities.id, 'Cai Rang' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Can Tho'
UNION ALL SELECT cities.id, 'Chanthabouly' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Laos' AND cities.name = 'Vientiane'
UNION ALL SELECT cities.id, 'Sikhottabong' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Laos' AND cities.name = 'Vientiane'
UNION ALL SELECT cities.id, 'Chamkarmon' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Cambodia' AND cities.name = 'Phnom Penh'
UNION ALL SELECT cities.id, 'Daun Penh' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Cambodia' AND cities.name = 'Phnom Penh'
UNION ALL SELECT cities.id, 'Pathum Wan' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Thailand' AND cities.name = 'Bangkok'
UNION ALL SELECT cities.id, 'Watthana' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Thailand' AND cities.name = 'Bangkok'
UNION ALL SELECT cities.id, 'Central Area' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Singapore' AND cities.name = 'Singapore'
UNION ALL SELECT cities.id, 'Jurong East' FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Singapore' AND cities.name = 'Singapore';

INSERT INTO cv_categories (name) VALUES
  ('Software Development'),
  ('Data Science'),
  ('Finance & Accounting'),
  ('Marketing'),
  ('Education'),
  ('Design & Creative'),
  ('Business & Management'),
  ('Law & Legal Services'),
  ('General Labor');

INSERT INTO job_categories (name) VALUES
  ('Software Development'),
  ('Data Science'),
  ('Finance & Accounting'),
  ('Marketing'),
  ('Education'),
  ('Design & Creative'),
  ('Business & Management'),
  ('Law & Legal Services'),
  ('General Labor');

INSERT INTO degree_levels (name, sort_order) VALUES
  ('High School', 1),
  ('Associate Degree', 2),
  ('Bachelor Degree', 3),
  ('Master Degree', 4),
  ('Doctorate', 5);

INSERT INTO majors (name) VALUES
  ('Computer Science'),
  ('Software Engineering'),
  ('Information Systems'),
  ('Information Technology'),
  ('Cybersecurity'),
  ('Data Science'),
  ('Artificial Intelligence'),
  ('Business Administration'),
  ('International Business'),
  ('Marketing'),
  ('Digital Marketing'),
  ('Finance'),
  ('Banking'),
  ('Accounting'),
  ('Auditing'),
  ('Economics'),
  ('Human Resource Management'),
  ('Logistics and Supply Chain Management'),
  ('Law'),
  ('Business Law'),
  ('English Language'),
  ('Education'),
  ('Graphic Design'),
  ('Architecture'),
  ('Civil Engineering'),
  ('Electrical Engineering'),
  ('Mechanical Engineering'),
  ('Medicine'),
  ('Pharmacy'),
  ('Nursing'),
  ('Tourism and Hospitality Management'),
  ('Agriculture'),
  ('Food Technology'),
  ('Public Relations'),
  ('Media and Communication');

INSERT INTO institutions (name) VALUES
  ('Academy of Finance'),
  ('An Giang University'),
  ('Bac Lieu University'),
  ('Banking Academy of Vietnam'),
  ('Banking University of Ho Chi Minh City'),
  ('Binh Duong Economics and Technology University'),
  ('Binh Duong University'),
  ('British University of Vietnam'),
  ('Can-Tho University'),
  ('Danang College of Technology'),
  ('Dong Nai Technology University'),
  ('Eastern International University'),
  ('Electric Power University'),
  ('Foreign Trade University'),
  ('FPT University'),
  ('Gia Dinh University'),
  ('Hai Duong University'),
  ('Hanoi Financial and Banking University'),
  ('Hanoi Medical University'),
  ('Hanoi National Economics University'),
  ('Hanoi Open University'),
  ('Hanoi University'),
  ('Hanoi University of Architecture'),
  ('Hanoi University of Business And Technology'),
  ('Hanoi University of Civil Engineering'),
  ('Hanoi University of Industry'),
  ('Hanoi University of Mining and Geology'),
  ('Hanoi University of Science'),
  ('Hanoi University of Science & Technology'),
  ('Hcmc University of Technology & Education'),
  ('Hoa Sen University'),
  ('Ho Chi Minh City Open University'),
  ('Ho Chi Minh City University of Agriculture and Forestry'),
  ('Ho Chi Minh City University of Architecture'),
  ('Ho Chi Minh City University of Economics'),
  ('Hochiminh City University of Food Industry'),
  ('Ho Chi Minh City University of Foreign Languages and Information Technology'),
  ('Ho Chi Minh City University of Law'),
  ('Ho Chi Minh City University of Medicine and Pharmacy'),
  ('Ho Chi Minh City University of Natural Sciences'),
  ('Ho Chi Minh City University of Pedagogics'),
  ('Ho Chi Minh City University of Social Sciences and Humanities'),
  ('Ho Chi Minh City University of Technology'),
  ('Ho Chi Minh City University Of Technology (HUTECH)'),
  ('Ho Chi Minh City University of Transport'),
  ('Hong Bang University International'),
  ('Hue College of Economics'),
  ('Hue University'),
  ('Hue University of Agriculture and Forestry'),
  ('Hung Vuong University Ho Chi Minh City'),
  ('Industrial University of Ho Chi Minh City'),
  ('Institute of Finance'),
  ('Lac Hong University'),
  ('Military Academy of Logistics'),
  ('Nha Trang University'),
  ('Phenikaa University'),
  ('Phuong Dong University'),
  ('Posts & Telecommunications Institute of Technology'),
  ('RMIT International University Vietnam'),
  ('Saigon Technology University'),
  ('Saigon University'),
  ('Tay Nguyen University'),
  ('Thainguyen University of Agriculture and Forestry'),
  ('Thuongmai University'),
  ('Trade Union University'),
  ('Tra Vinh University'),
  ('UNETI University'),
  ('University of Da Lat'),
  ('University of Da Nang'),
  ('University of Finance and Marketing'),
  ('University of Labour and Social Affairs'),
  ('University of Technical Education Ho Chi Minh City'),
  ('University of Transport and Communications'),
  ('Van Lang University'),
  ('Vietnam Maritime University'),
  ('Vietnam National University Hanoi'),
  ('Vietnam National University Ho Chi Minh City'),
  ('Vietnam National University of Agriculture'),
  ('Vinh University'),
  ('Water Resources University');

INSERT INTO job_titles (name) VALUES
  ('Software Engineer'),
  ('Frontend Developer'),
  ('Backend Developer'),
  ('Full Stack Developer'),
  ('Mobile App Developer'),
  ('DevOps Engineer'),
  ('QA Engineer'),
  ('UI/UX Designer'),
  ('Graphic Designer'),
  ('Product Designer'),
  ('Data Analyst'),
  ('Data Scientist'),
  ('Machine Learning Engineer'),
  ('Business Analyst'),
  ('Project Manager'),
  ('Product Manager'),
  ('Sales Executive'),
  ('Account Executive'),
  ('Customer Service Representative'),
  ('Human Resources Officer'),
  ('Recruiter'),
  ('Administrative Assistant'),
  ('Office Administrator'),
  ('Accountant'),
  ('Auditor'),
  ('Financial Analyst'),
  ('Bank Teller'),
  ('Marketing Executive'),
  ('Digital Marketing Specialist'),
  ('Content Writer'),
  ('Social Media Specialist'),
  ('SEO Specialist'),
  ('Teacher'),
  ('Teaching Assistant'),
  ('Academic Advisor'),
  ('Legal Assistant'),
  ('Paralegal'),
  ('Legal Officer'),
  ('Lawyer'),
  ('Warehouse Worker'),
  ('Factory Worker'),
  ('Delivery Driver'),
  ('Security Guard'),
  ('Cleaner'),
  ('Cashier'),
  ('Waiter/Waitress'),
  ('Cook'),
  ('Receptionist');

INSERT INTO employment_types (name) VALUES
  ('Full-time'),
  ('Part-time'),
  ('Contract'),
  ('Internship'),
  ('Freelance');

INSERT INTO job_levels (name, sort_order) VALUES
  ('Junior', 1),
  ('Mid', 2),
  ('Senior', 3);

INSERT INTO industries (name) VALUES
  ('Information Technology'),
  ('Software Development'),
  ('Data & Analytics'),
  ('Cybersecurity'),
  ('Telecommunications'),
  ('Banking'),
  ('Finance'),
  ('Accounting'),
  ('Insurance'),
  ('Marketing & Advertising'),
  ('Media & Communications'),
  ('Education'),
  ('Higher Education'),
  ('Healthcare'),
  ('Pharmaceuticals'),
  ('Legal Services'),
  ('Business Consulting'),
  ('Human Resources'),
  ('Real Estate'),
  ('Architecture'),
  ('Construction'),
  ('Manufacturing'),
  ('Logistics & Supply Chain'),
  ('Transportation'),
  ('Retail'),
  ('Wholesale'),
  ('Hospitality'),
  ('Food & Beverage'),
  ('Tourism'),
  ('Agriculture'),
  ('Energy'),
  ('Utilities'),
  ('Government'),
  ('Non-Profit'),
  ('Design & Creative Services'),
  ('E-commerce'),
  ('Customer Service'),
  ('Security Services'),
  ('Cleaning Services'),
  ('General Labor');

INSERT INTO salary_ranges (label, min_salary, max_salary, currency, sort_order) VALUES
  ('Below 500 USD', 0, 499, 'USD', 1),
  ('500 - 1000 USD', 500, 1000, 'USD', 2),
  ('1000 - 1500 USD', 1000, 1500, 'USD', 3),
  ('1500 - 2000 USD', 1500, 2000, 'USD', 4),
  ('2000 - 3000 USD', 2000, 3000, 'USD', 5),
  ('Above 3000 USD', 3000, NULL, 'USD', 6),
  ('Negotiable', NULL, NULL, 'USD', 7);

INSERT INTO salary_types (name) VALUES
  ('Gross'),
  ('Net');

INSERT INTO work_arrangements (name) VALUES
  ('Onsite'),
  ('Remote'),
  ('Hybrid');

INSERT INTO skills (name) VALUES
  ('PHP'),
  ('Laravel'),
  ('JavaScript'),
  ('TypeScript'),
  ('HTML'),
  ('CSS'),
  ('React'),
  ('Vue.js'),
  ('Node.js'),
  ('MySQL'),
  ('PostgreSQL'),
  ('Git'),
  ('REST API Development'),
  ('Database Design'),
  ('UI/UX Design'),
  ('Figma'),
  ('Adobe Photoshop'),
  ('Adobe Illustrator'),
  ('Data Analysis'),
  ('Microsoft Excel'),
  ('Power BI'),
  ('Python'),
  ('Machine Learning'),
  ('Financial Analysis'),
  ('Accounting'),
  ('Auditing'),
  ('Bookkeeping'),
  ('Digital Marketing'),
  ('SEO'),
  ('Content Writing'),
  ('Social Media Marketing'),
  ('Market Research'),
  ('Teaching'),
  ('Curriculum Design'),
  ('Public Speaking'),
  ('Academic Writing'),
  ('Business Analysis'),
  ('Project Management'),
  ('Product Management'),
  ('Sales'),
  ('Customer Service'),
  ('Recruitment'),
  ('Human Resource Management'),
  ('Legal Research'),
  ('Contract Drafting'),
  ('Legal Writing'),
  ('Communication'),
  ('Teamwork'),
  ('Problem Solving'),
  ('Time Management'),
  ('Leadership'),
  ('Critical Thinking'),
  ('Inventory Management'),
  ('Warehouse Operations'),
  ('Cash Handling'),
  ('Food Preparation'),
  ('Cleaning'),
  ('Security Monitoring'),
  ('Driving'),
  ('Basic Computer Skills');

INSERT INTO skill_proficiency_levels (name, level_value) VALUES
  ('Level 1 - Beginner', 1),
  ('Level 2 - Basic', 2),
  ('Level 3 - Elementary', 3),
  ('Level 4 - Developing', 4),
  ('Level 5 - Intermediate', 5),
  ('Level 6 - Competent', 6),
  ('Level 7 - Proficient', 7),
  ('Level 8 - Advanced', 8),
  ('Level 9 - Expert', 9),
  ('Level 10 - Master', 10);

INSERT INTO certificate_names (name) VALUES
  ('TOEIC'),
  ('IELTS'),
  ('TOEFL'),
  ('Google Data Analytics Professional Certificate'),
  ('Google Project Management Professional Certificate'),
  ('Google UX Design Professional Certificate'),
  ('Microsoft Office Specialist'),
  ('Microsoft Certified: Azure Fundamentals'),
  ('AWS Certified Cloud Practitioner'),
  ('AWS Certified Solutions Architect - Associate'),
  ('Cisco Certified Network Associate'),
  ('CompTIA A+'),
  ('CompTIA Security+'),
  ('Oracle Certified Professional Java Programmer'),
  ('Meta Front-End Developer Professional Certificate'),
  ('Meta Back-End Developer Professional Certificate'),
  ('IBM Data Science Professional Certificate'),
  ('Certified Public Accountant'),
  ('ACCA'),
  ('CFA Level I'),
  ('Project Management Professional'),
  ('Certified ScrumMaster'),
  ('HubSpot Content Marketing Certification'),
  ('Google Ads Certification'),
  ('Google Analytics Certification'),
  ('Facebook Blueprint Certification'),
  ('TESOL Certificate'),
  ('TEFL Certificate'),
  ('Teaching Certificate'),
  ('Legal Practice Certificate'),
  ('Occupational Safety and Health Certificate'),
  ('Food Safety Certificate'),
  ('Forklift Operator Certificate'),
  ('Driving License');

INSERT INTO issuing_organizations (name) VALUES
  ('ETS'),
  ('British Council'),
  ('IDP Education'),
  ('Educational Testing Service'),
  ('Google'),
  ('Microsoft'),
  ('Amazon Web Services'),
  ('Cisco'),
  ('CompTIA'),
  ('Oracle'),
  ('Meta'),
  ('IBM'),
  ('ACCA Global'),
  ('CFA Institute'),
  ('Project Management Institute'),
  ('Scrum Alliance'),
  ('HubSpot Academy'),
  ('Google Skillshop'),
  ('Google Analytics Academy'),
  ('Facebook Blueprint'),
  ('Cambridge Assessment English'),
  ('TESOL International Association'),
  ('International TEFL Academy'),
  ('Ministry of Education and Training'),
  ('Ministry of Labour, Invalids and Social Affairs'),
  ('Ministry of Health'),
  ('Vietnam Bar Federation'),
  ('Vietnam Chamber of Commerce and Industry'),
  ('Directorate for Roads of Vietnam'),
  ('Vietnam Register'),
  ('Occupational Safety and Health Administration'),
  ('Local Vocational Training Center'),
  ('University Training Center'),
  ('Professional Training Institute');

INSERT INTO cv_templates (name) VALUES
  ('Modern'),
  ('Classic'),
  ('Minimal');

INSERT INTO companies (employer_user_id, name, avatar_url, description) VALUES
  (
    (SELECT id FROM users WHERE email = 'an@gmail.com'),
    'OneTech Labs',
    'https://placehold.co/160x160/001a38/ffffff?text=OT',
    'A product engineering studio building web platforms, internal tools, and data-driven digital products for growing teams.'
  ),
  (
    (SELECT id FROM users WHERE email = 'an@gmail.com'),
    'Insight Works',
    'https://placehold.co/160x160/d5e3ff/001a38?text=IW',
    'A business analytics team helping companies turn operational data into dashboards, reports, and practical decisions.'
  ),
  (
    (SELECT id FROM users WHERE email = 'hoa@gmail.com'),
    'FinCore Services',
    'https://placehold.co/160x160/111827/ffffff?text=FC',
    'A professional services company focused on accounting, auditing support, and financial operations for SMEs.'
  ),
  (
    (SELECT id FROM users WHERE email = 'hoa@gmail.com'),
    'Pixel Strategy Studio',
    'https://placehold.co/160x160/e0e3e5/001a38?text=PS',
    'A remote-first design studio creating product interfaces, design systems, and customer experience improvements.'
  );

INSERT INTO job_vacancies (
  employer_user_id,
  company_id,
  job_title_id,
  job_category_id,
  employment_type_id,
  industry_id,
  job_level_id,
  number_of_openings,
  country_id,
  city_id,
  district_id,
  work_arrangement_id,
  salary_range_id,
  salary_type_id,
  benefits,
  responsibilities,
  required_qualifications,
  preferred_skills,
  additional_notes,
  minimum_degree_level_id,
  minimum_years_experience,
  status
) VALUES
  (
    (SELECT id FROM users WHERE email = 'an@gmail.com'),
    (SELECT companies.id FROM companies JOIN users ON users.id = companies.employer_user_id WHERE users.email = 'an@gmail.com' AND companies.name = 'OneTech Labs'),
    (SELECT id FROM job_titles WHERE name = 'Frontend Developer'),
    (SELECT id FROM job_categories WHERE name = 'Software Development'),
    (SELECT id FROM employment_types WHERE name = 'Full-time'),
    (SELECT id FROM industries WHERE name = 'Software Development'),
    (SELECT id FROM job_levels WHERE name = 'Mid'),
    2,
    (SELECT id FROM countries WHERE name = 'Vietnam'),
    (SELECT cities.id FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'),
    (SELECT districts.id FROM districts JOIN cities ON cities.id = districts.city_id JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City' AND districts.name = 'District 1'),
    (SELECT id FROM work_arrangements WHERE name = 'Hybrid'),
    (SELECT id FROM salary_ranges WHERE label = '1000 - 1500 USD'),
    (SELECT id FROM salary_types WHERE name = 'Gross'),
    'Annual performance bonus, health insurance, laptop allowance, and flexible working days.',
    'Build responsive web interfaces, collaborate with backend developers, convert UI designs into production-ready pages, and improve frontend performance.',
    'At least 2 years of frontend experience, strong HTML/CSS/JavaScript fundamentals, and practical experience with component-based UI development.',
    'Experience with React, REST APIs, Git workflow, and basic UI/UX design sense is preferred.',
    'Portfolio or GitHub profile is a strong plus.',
    (SELECT id FROM degree_levels WHERE name = 'Bachelor Degree'),
    2,
    'active'
  ),
  (
    (SELECT id FROM users WHERE email = 'an@gmail.com'),
    (SELECT companies.id FROM companies JOIN users ON users.id = companies.employer_user_id WHERE users.email = 'an@gmail.com' AND companies.name = 'Insight Works'),
    (SELECT id FROM job_titles WHERE name = 'Data Analyst'),
    (SELECT id FROM job_categories WHERE name = 'Data Science'),
    (SELECT id FROM employment_types WHERE name = 'Full-time'),
    (SELECT id FROM industries WHERE name = 'Data & Analytics'),
    (SELECT id FROM job_levels WHERE name = 'Junior'),
    1,
    (SELECT id FROM countries WHERE name = 'Vietnam'),
    (SELECT cities.id FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi'),
    (SELECT districts.id FROM districts JOIN cities ON cities.id = districts.city_id JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Hanoi' AND districts.name = 'Cau Giay'),
    (SELECT id FROM work_arrangements WHERE name = 'Onsite'),
    (SELECT id FROM salary_ranges WHERE label = '500 - 1000 USD'),
    (SELECT id FROM salary_types WHERE name = 'Net'),
    'Training budget, mentorship program, paid leave, and quarterly team activities.',
    'Prepare dashboards, clean raw datasets, analyze business metrics, and communicate insights to internal teams.',
    'Good Excel skills, basic SQL knowledge, analytical thinking, and ability to explain data clearly.',
    'Power BI, Python, statistics, and business reporting experience are preferred.',
    'Fresh graduates with strong analytical projects can apply.',
    (SELECT id FROM degree_levels WHERE name = 'Bachelor Degree'),
    0,
    'active'
  ),
  (
    (SELECT id FROM users WHERE email = 'hoa@gmail.com'),
    (SELECT companies.id FROM companies JOIN users ON users.id = companies.employer_user_id WHERE users.email = 'hoa@gmail.com' AND companies.name = 'FinCore Services'),
    (SELECT id FROM job_titles WHERE name = 'Accountant'),
    (SELECT id FROM job_categories WHERE name = 'Finance & Accounting'),
    (SELECT id FROM employment_types WHERE name = 'Full-time'),
    (SELECT id FROM industries WHERE name = 'Accounting'),
    (SELECT id FROM job_levels WHERE name = 'Mid'),
    1,
    (SELECT id FROM countries WHERE name = 'Vietnam'),
    (SELECT cities.id FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Da Nang'),
    (SELECT districts.id FROM districts JOIN cities ON cities.id = districts.city_id JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Da Nang' AND districts.name = 'Hai Chau'),
    (SELECT id FROM work_arrangements WHERE name = 'Onsite'),
    (SELECT id FROM salary_ranges WHERE label = '1000 - 1500 USD'),
    (SELECT id FROM salary_types WHERE name = 'Gross'),
    '13th-month salary, professional certification support, and annual company trip.',
    'Manage bookkeeping records, prepare monthly reports, reconcile transactions, and support tax documentation.',
    'At least 2 years of accounting experience, solid Excel skills, and knowledge of Vietnamese accounting standards.',
    'CPA, ACCA, auditing experience, and ERP exposure are preferred.',
    'Candidates should be detail-oriented and comfortable with deadlines.',
    (SELECT id FROM degree_levels WHERE name = 'Bachelor Degree'),
    2,
    'active'
  ),
  (
    (SELECT id FROM users WHERE email = 'hoa@gmail.com'),
    (SELECT companies.id FROM companies JOIN users ON users.id = companies.employer_user_id WHERE users.email = 'hoa@gmail.com' AND companies.name = 'Pixel Strategy Studio'),
    (SELECT id FROM job_titles WHERE name = 'UI/UX Designer'),
    (SELECT id FROM job_categories WHERE name = 'Design & Creative'),
    (SELECT id FROM employment_types WHERE name = 'Contract'),
    (SELECT id FROM industries WHERE name = 'Design & Creative Services'),
    (SELECT id FROM job_levels WHERE name = 'Senior'),
    1,
    (SELECT id FROM countries WHERE name = 'Vietnam'),
    (SELECT cities.id FROM cities JOIN countries ON countries.id = cities.country_id WHERE countries.name = 'Vietnam' AND cities.name = 'Ho Chi Minh City'),
    NULL,
    (SELECT id FROM work_arrangements WHERE name = 'Remote'),
    (SELECT id FROM salary_ranges WHERE label = '1500 - 2000 USD'),
    (SELECT id FROM salary_types WHERE name = 'Net'),
    'Remote-first contract, flexible schedule, design tooling budget, and milestone bonuses.',
    'Lead product design work, create wireframes and prototypes, run usability reviews, and maintain design system consistency.',
    'At least 4 years of UI/UX experience, strong portfolio, user-centered thinking, and ability to communicate design decisions.',
    'Figma, design systems, accessibility, product strategy, and frontend awareness are preferred.',
    'This contract can be extended based on product roadmap needs.',
    (SELECT id FROM degree_levels WHERE name = 'Bachelor Degree'),
    4,
    'inactive'
  );

INSERT INTO job_vacancy_skills (job_vacancy_id, skill_id, minimum_proficiency_level_id)
SELECT
  job_vacancies.id,
  skills.id,
  skill_proficiency_levels.id
FROM job_vacancies
JOIN job_titles ON job_titles.id = job_vacancies.job_title_id
JOIN users ON users.id = job_vacancies.employer_user_id
JOIN skills ON skills.name IN ('HTML', 'CSS', 'JavaScript', 'React', 'Git')
JOIN skill_proficiency_levels ON skill_proficiency_levels.level_value = CASE skills.name
  WHEN 'HTML' THEN 7
  WHEN 'CSS' THEN 7
  WHEN 'JavaScript' THEN 7
  WHEN 'React' THEN 6
  ELSE 5
END
WHERE users.email = 'an@gmail.com'
  AND job_titles.name = 'Frontend Developer';

INSERT INTO job_vacancy_skills (job_vacancy_id, skill_id, minimum_proficiency_level_id)
SELECT
  job_vacancies.id,
  skills.id,
  skill_proficiency_levels.id
FROM job_vacancies
JOIN job_titles ON job_titles.id = job_vacancies.job_title_id
JOIN users ON users.id = job_vacancies.employer_user_id
JOIN skills ON skills.name IN ('Microsoft Excel', 'Data Analysis', 'Power BI', 'Python', 'Business Analysis')
JOIN skill_proficiency_levels ON skill_proficiency_levels.level_value = CASE skills.name
  WHEN 'Microsoft Excel' THEN 7
  WHEN 'Data Analysis' THEN 6
  WHEN 'Power BI' THEN 5
  WHEN 'Python' THEN 4
  ELSE 5
END
WHERE users.email = 'an@gmail.com'
  AND job_titles.name = 'Data Analyst';

INSERT INTO job_vacancy_skills (job_vacancy_id, skill_id, minimum_proficiency_level_id)
SELECT
  job_vacancies.id,
  skills.id,
  skill_proficiency_levels.id
FROM job_vacancies
JOIN job_titles ON job_titles.id = job_vacancies.job_title_id
JOIN users ON users.id = job_vacancies.employer_user_id
JOIN skills ON skills.name IN ('Accounting', 'Bookkeeping', 'Auditing', 'Microsoft Excel', 'Financial Analysis')
JOIN skill_proficiency_levels ON skill_proficiency_levels.level_value = CASE skills.name
  WHEN 'Accounting' THEN 7
  WHEN 'Bookkeeping' THEN 7
  WHEN 'Auditing' THEN 5
  WHEN 'Microsoft Excel' THEN 7
  ELSE 6
END
WHERE users.email = 'hoa@gmail.com'
  AND job_titles.name = 'Accountant';

INSERT INTO job_vacancy_skills (job_vacancy_id, skill_id, minimum_proficiency_level_id)
SELECT
  job_vacancies.id,
  skills.id,
  skill_proficiency_levels.id
FROM job_vacancies
JOIN job_titles ON job_titles.id = job_vacancies.job_title_id
JOIN users ON users.id = job_vacancies.employer_user_id
JOIN skills ON skills.name IN ('UI/UX Design', 'Figma', 'Adobe Photoshop', 'Communication', 'Product Management')
JOIN skill_proficiency_levels ON skill_proficiency_levels.level_value = CASE skills.name
  WHEN 'UI/UX Design' THEN 8
  WHEN 'Figma' THEN 8
  WHEN 'Adobe Photoshop' THEN 6
  WHEN 'Communication' THEN 7
  ELSE 5
END
WHERE users.email = 'hoa@gmail.com'
  AND job_titles.name = 'UI/UX Designer';
