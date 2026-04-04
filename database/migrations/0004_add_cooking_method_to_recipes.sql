-- Add cooking_method column to recipes table (idempotent — uses IF NOT EXISTS)
ALTER TABLE recipes
    ADD COLUMN IF NOT EXISTS cooking_method VARCHAR(100) NULL AFTER servings;
