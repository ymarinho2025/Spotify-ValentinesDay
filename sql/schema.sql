CREATE TABLE IF NOT EXISTS couple_users(
 id BIGSERIAL PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(180) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK(status IN('active','blocked')),created_at TIMESTAMPTZ DEFAULT NOW(),updated_at TIMESTAMPTZ DEFAULT NOW());
CREATE TABLE IF NOT EXISTS couple_settings(
 id SMALLINT PRIMARY KEY DEFAULT 1 CHECK(id=1),names VARCHAR(180) NOT NULL DEFAULT 'Sarah & Yuri',start_date DATE NOT NULL DEFAULT '2026-07-16',first_met DATE NOT NULL DEFAULT '2026-06-13',about_text TEXT NOT NULL DEFAULT '',final_message TEXT NOT NULL DEFAULT 'Nossa história só está começando ✨',updated_by BIGINT REFERENCES couple_users(id),updated_at TIMESTAMPTZ DEFAULT NOW());
CREATE TABLE IF NOT EXISTS couple_images(
 id BIGSERIAL PRIMARY KEY,display_name VARCHAR(220) NOT NULL,original_name VARCHAR(255),mime_type VARCHAR(100),base64_data TEXT,size_bytes BIGINT NOT NULL DEFAULT 0,source_type VARCHAR(20) NOT NULL DEFAULT 'database' CHECK(source_type IN('database','local')),local_path TEXT,created_by BIGINT REFERENCES couple_users(id),updated_by BIGINT REFERENCES couple_users(id),created_at TIMESTAMPTZ DEFAULT NOW(),updated_at TIMESTAMPTZ DEFAULT NOW());
CREATE TABLE IF NOT EXISTS couple_chapters(
 id BIGSERIAL PRIMARY KEY,sort_order INTEGER NOT NULL,chapter_label VARCHAR(100) NOT NULL,title VARCHAR(180) NOT NULL,description TEXT NOT NULL DEFAULT '',image_id BIGINT REFERENCES couple_images(id) ON DELETE SET NULL,active BOOLEAN NOT NULL DEFAULT TRUE,created_by BIGINT REFERENCES couple_users(id),updated_by BIGINT REFERENCES couple_users(id),created_at TIMESTAMPTZ DEFAULT NOW(),updated_at TIMESTAMPTZ DEFAULT NOW());
CREATE TABLE IF NOT EXISTS couple_audit(
 id BIGSERIAL PRIMARY KEY,user_id BIGINT REFERENCES couple_users(id) ON DELETE SET NULL,action VARCHAR(50) NOT NULL,entity_type VARCHAR(60) NOT NULL,entity_id BIGINT,changes_json JSONB DEFAULT '{}'::jsonb,ip VARCHAR(45),user_agent TEXT,created_at TIMESTAMPTZ DEFAULT NOW());
CREATE INDEX IF NOT EXISTS idx_couple_chapters_order ON couple_chapters(sort_order,id);
CREATE INDEX IF NOT EXISTS idx_couple_images_updated ON couple_images(updated_at DESC);
