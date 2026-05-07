ALTER TABLE cvs
  ADD COLUMN is_completed BOOLEAN NOT NULL DEFAULT FALSE AFTER summary,
  ADD COLUMN completed_at DATETIME NULL AFTER is_completed,
  ADD KEY idx_cvs_completed (is_completed, completed_at);
