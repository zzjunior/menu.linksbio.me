-- Migration: Add store_id relationship to users table
-- Created: 2025-10-07 05:51:25

-- Add store_id foreign key to users table
ALTER TABLE users ADD COLUMN store_id INT AFTER id;

-- Add foreign key constraint
ALTER TABLE users ADD FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE;

-- Update existing users to reference the first store (assuming store id = 1)
UPDATE users SET store_id = 1 WHERE store_id IS NULL;