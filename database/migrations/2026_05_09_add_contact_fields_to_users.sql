ALTER TABLE users
  ADD COLUMN phone_number VARCHAR(30) NULL AFTER avatar_url,
  ADD COLUMN country_id BIGINT UNSIGNED NULL AFTER phone_number,
  ADD COLUMN city_id BIGINT UNSIGNED NULL AFTER country_id,
  ADD COLUMN street_address VARCHAR(255) NULL AFTER city_id,
  ADD KEY idx_users_location (country_id, city_id),
  ADD CONSTRAINT fk_users_country
    FOREIGN KEY (country_id) REFERENCES countries(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  ADD CONSTRAINT fk_users_city
    FOREIGN KEY (city_id) REFERENCES cities(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL;
