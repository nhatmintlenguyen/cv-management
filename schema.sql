-- Online CV Management & Search System
-- MySQL 8+ schema

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS cv_skills;
DROP TABLE IF EXISTS cv_certificates;
DROP TABLE IF EXISTS cv_work_histories;
DROP TABLE IF EXISTS cv_educations;
DROP TABLE IF EXISTS cvs;
DROP TABLE IF EXISTS users;

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
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
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
    CHECK (level_value BETWEEN 1 AND 5)
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

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  KEY idx_cvs_category (cv_category_id),
  KEY idx_cvs_location (country_id, city_id),
  KEY idx_cvs_updated_at (updated_at),
  FULLTEXT KEY ft_cvs_keyword (full_name, summary),

  CONSTRAINT fk_cvs_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
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

DELIMITER ;

INSERT INTO roles (name) VALUES
  ('job_seeker'),
  ('employer'),
  ('admin');

INSERT INTO genders (name) VALUES
  ('Male'),
  ('Female'),
  ('Other'),
  ('Prefer not to say');

INSERT INTO cv_categories (name) VALUES
  ('Software Development'),
  ('Data Science'),
  ('Finance & Accounting'),
  ('Marketing'),
  ('Education'),
  ('Design & Creative');

INSERT INTO degree_levels (name, sort_order) VALUES
  ('High School', 1),
  ('Associate Degree', 2),
  ('Bachelor Degree', 3),
  ('Master Degree', 4),
  ('Doctorate', 5);

INSERT INTO employment_types (name) VALUES
  ('Full-time'),
  ('Part-time'),
  ('Contract'),
  ('Internship'),
  ('Freelance');

INSERT INTO skill_proficiency_levels (name, level_value) VALUES
  ('Beginner', 1),
  ('Elementary', 2),
  ('Intermediate', 3),
  ('Advanced', 4),
  ('Expert', 5);

INSERT INTO cv_templates (name) VALUES
  ('Modern'),
  ('Classic'),
  ('Minimal');
