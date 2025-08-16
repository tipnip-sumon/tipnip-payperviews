-- URGENT: Manual SQL fix for missing columns in video_links table
-- Run this if the migration script fails

-- Check current table structure
DESCRIBE video_links;

-- Add the missing columns
ALTER TABLE video_links 
ADD COLUMN embed_url VARCHAR(255) NULL AFTER description,
ADD COLUMN earning_per_view DECIMAL(10,2) DEFAULT 0.00 AFTER cost_per_click;

-- Verify the columns were added
DESCRIBE video_links;

-- Show a few rows to confirm structure
SELECT id, title, embed_url, earning_per_view FROM video_links LIMIT 3;
