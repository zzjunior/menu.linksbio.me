-- Migration: Add loyalty and delivery fields to users table
-- Created: 2025-10-07 05:47:20

-- Add new fields to users table for store settings
ALTER TABLE users ADD COLUMN store_description TEXT AFTER store_name;
ALTER TABLE users ADD COLUMN delivery_enabled BOOLEAN DEFAULT TRUE AFTER address;
ALTER TABLE users ADD COLUMN delivery_fee DECIMAL(10,2) DEFAULT 0.00 AFTER delivery_enabled;
ALTER TABLE users ADD COLUMN loyalty_enabled BOOLEAN DEFAULT FALSE AFTER delivery_fee;
ALTER TABLE users ADD COLUMN loyalty_orders_required INT DEFAULT 10 AFTER loyalty_enabled;
ALTER TABLE users ADD COLUMN loyalty_discount_percent DECIMAL(5,2) DEFAULT 10.00 AFTER loyalty_orders_required;