ALTER TABLE job_vacancies
  MODIFY COLUMN status ENUM('active', 'inactive', 'suspicious') NOT NULL DEFAULT 'active';
