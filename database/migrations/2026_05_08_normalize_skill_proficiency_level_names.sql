-- Normalize skill proficiency level names.
--
-- Older seed data stored names such as "Level 1 - Beginner".
-- The UI already displays the numeric level separately, so the database should
-- store only the human-readable proficiency name.

UPDATE skill_proficiency_levels
SET name = CASE level_value
  WHEN 1 THEN 'Beginner'
  WHEN 2 THEN 'Basic'
  WHEN 3 THEN 'Elementary'
  WHEN 4 THEN 'Developing'
  WHEN 5 THEN 'Intermediate'
  WHEN 6 THEN 'Competent'
  WHEN 7 THEN 'Proficient'
  WHEN 8 THEN 'Advanced'
  WHEN 9 THEN 'Expert'
  WHEN 10 THEN 'Master'
  ELSE name
END
WHERE level_value BETWEEN 1 AND 10;
