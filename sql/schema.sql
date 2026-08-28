CREATE TABLE IF NOT EXISTS couple_users(
 id BIGSERIAL PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(180) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK(status IN('active','blocked')),
 created_at TIMESTAMPTZ DEFAULT NOW(),
 updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS couple_settings(
 id SMALLINT PRIMARY KEY DEFAULT 1 CHECK(id=1),
 names VARCHAR(180) NOT NULL DEFAULT 'Sarah & Yuri',
 start_date DATE NOT NULL DEFAULT '2026-07-16',
 first_met DATE NOT NULL DEFAULT '2026-06-13',
 about_text TEXT NOT NULL DEFAULT '',
 final_message TEXT NOT NULL DEFAULT 'Nossa história só está começando ✨',
 updated_by BIGINT REFERENCES couple_users(id),
 updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS couple_images(
 id BIGSERIAL PRIMARY KEY,
 display_name VARCHAR(220) NOT NULL,
 original_name VARCHAR(255),
 mime_type VARCHAR(100),
 base64_data TEXT,
 size_bytes BIGINT NOT NULL DEFAULT 0,
 source_type VARCHAR(20) NOT NULL DEFAULT 'database' CHECK(source_type IN('database','local')),
 local_path TEXT,
 seed_key VARCHAR(120),
 created_by BIGINT REFERENCES couple_users(id),
 updated_by BIGINT REFERENCES couple_users(id),
 created_at TIMESTAMPTZ DEFAULT NOW(),
 updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS couple_chapters(
 id BIGSERIAL PRIMARY KEY,
 sort_order INTEGER NOT NULL,
 chapter_label VARCHAR(100) NOT NULL,
 title VARCHAR(180) NOT NULL,
 description TEXT NOT NULL DEFAULT '',
 image_id BIGINT REFERENCES couple_images(id) ON DELETE SET NULL,
 seed_key VARCHAR(120),
 active BOOLEAN NOT NULL DEFAULT TRUE,
 created_by BIGINT REFERENCES couple_users(id),
 updated_by BIGINT REFERENCES couple_users(id),
 created_at TIMESTAMPTZ DEFAULT NOW(),
 updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS couple_audit(
 id BIGSERIAL PRIMARY KEY,
 user_id BIGINT REFERENCES couple_users(id) ON DELETE SET NULL,
 action VARCHAR(50) NOT NULL,
 entity_type VARCHAR(60) NOT NULL,
 entity_id BIGINT,
 changes_json JSONB DEFAULT '{}'::jsonb,
 ip VARCHAR(45),
 user_agent TEXT,
 created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS couple_invites(
 id BIGSERIAL PRIMARY KEY,
 token_hash VARCHAR(64) NOT NULL UNIQUE,
 invited_by BIGINT NOT NULL REFERENCES couple_users(id) ON DELETE CASCADE,
 status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK(status IN('pending','accepted','revoked')),
 expires_at TIMESTAMPTZ NOT NULL,
 accepted_by BIGINT REFERENCES couple_users(id) ON DELETE SET NULL,
 created_at TIMESTAMPTZ DEFAULT NOW(),
 accepted_at TIMESTAMPTZ
);

-- Migrações seguras para bancos criados por versões anteriores.
ALTER TABLE couple_images ADD COLUMN IF NOT EXISTS seed_key VARCHAR(120);
ALTER TABLE couple_chapters ADD COLUMN IF NOT EXISTS seed_key VARCHAR(120);
ALTER TABLE couple_settings ADD COLUMN IF NOT EXISTS avatar_her_id BIGINT REFERENCES couple_images(id) ON DELETE SET NULL;
ALTER TABLE couple_settings ADD COLUMN IF NOT EXISTS avatar_me_id BIGINT REFERENCES couple_images(id) ON DELETE SET NULL;
ALTER TABLE couple_settings ADD COLUMN IF NOT EXISTS seed_version INTEGER NOT NULL DEFAULT 0;

CREATE UNIQUE INDEX IF NOT EXISTS uq_couple_images_seed_key ON couple_images(seed_key) WHERE seed_key IS NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS uq_couple_chapters_seed_key ON couple_chapters(seed_key) WHERE seed_key IS NOT NULL;
CREATE INDEX IF NOT EXISTS idx_couple_chapters_order ON couple_chapters(sort_order,id);
CREATE INDEX IF NOT EXISTS idx_couple_images_updated ON couple_images(updated_at DESC);
CREATE INDEX IF NOT EXISTS idx_couple_invites_status ON couple_invites(status,expires_at);

-- Máximo de duas contas ativas/criadas para este site de casal.
CREATE OR REPLACE FUNCTION enforce_two_couple_users() RETURNS trigger AS $$
BEGIN
  PERFORM pg_advisory_xact_lock(90202616);
  IF (SELECT COUNT(*) FROM couple_users) >= 2 THEN
    RAISE EXCEPTION 'Este casal já possui duas contas.';
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
DROP TRIGGER IF EXISTS trg_two_couple_users ON couple_users;
CREATE TRIGGER trg_two_couple_users BEFORE INSERT ON couple_users
FOR EACH ROW EXECUTE FUNCTION enforce_two_couple_users();
