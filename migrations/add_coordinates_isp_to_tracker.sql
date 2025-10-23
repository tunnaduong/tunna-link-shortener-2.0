-- Migration: Add coordinates and ISP columns to tracker table
-- Date: 2024

-- Add coordinates column (latitude, longitude as JSON)
ALTER TABLE tracker ADD COLUMN coordinates JSON NULL;

-- Add ISP column
ALTER TABLE tracker ADD COLUMN isp VARCHAR(255) NULL;
