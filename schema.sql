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
