USE emma_elearning;

ALTER TABLE lessons ADD COLUMN IF NOT EXISTS video_url VARCHAR(500) NULL AFTER content;
