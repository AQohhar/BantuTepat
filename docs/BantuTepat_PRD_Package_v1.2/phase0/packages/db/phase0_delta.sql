-- v1.1 delta template, NOT a standalone initial migration. Apply only after baseline tables users, roles, permissions, user_role_scopes, appeals, audit_events exist with UUID ids; adjust after Prisma schema review.
BEGIN;
CREATE TABLE role_permissions (role_id uuid NOT NULL REFERENCES roles(id) ON DELETE RESTRICT, permission_id uuid NOT NULL REFERENCES permissions(id) ON DELETE RESTRICT, PRIMARY KEY(role_id,permission_id));
-- Scoped elevated grants (program.publish, case.assign etc.) are separate from base role permissions.
CREATE TABLE user_scope_permissions (scope_id uuid NOT NULL REFERENCES user_role_scopes(id) ON DELETE RESTRICT, permission_id uuid NOT NULL REFERENCES permissions(id) ON DELETE RESTRICT, granted_by uuid NOT NULL REFERENCES users(id), valid_until timestamptz NOT NULL, revoked_at timestamptz, PRIMARY KEY(scope_id,permission_id));
CREATE TABLE idempotency_keys (actor_scope text NOT NULL, actor_id uuid REFERENCES users(id) ON DELETE SET NULL, idempotency_key text NOT NULL, operation text NOT NULL, request_hash text NOT NULL CHECK (length(request_hash)=64), response_status integer, response_json jsonb, created_at timestamptz NOT NULL DEFAULT now(), expires_at timestamptz NOT NULL, PRIMARY KEY(actor_scope,idempotency_key), CHECK (expires_at>created_at));
CREATE INDEX idempotency_expiry_idx ON idempotency_keys(expires_at);
-- actor_scope = user:<uuid>, service:<principal>, wa:<provider account>. Hash canonical method/path/body; no raw PII. Insert key and commit response with mutation; lock/replay on conflict, mismatch 409. Retention >= provider retry horizon.
CREATE TABLE IF NOT EXISTS appeal_decisions (id uuid PRIMARY KEY, appeal_id uuid NOT NULL REFERENCES appeals(id) ON DELETE RESTRICT, reviewer_id uuid NOT NULL REFERENCES users(id) ON DELETE RESTRICT, decision text NOT NULL CHECK(decision IN ('UPHELD','RECHECK_REQUIRED','DECISION_CORRECTED')), reason text NOT NULL CHECK(length(trim(reason))>0), evidence_ref text, created_at timestamptz NOT NULL DEFAULT now());
CREATE INDEX IF NOT EXISTS appeal_decisions_appeal_idx ON appeal_decisions(appeal_id);
CREATE TABLE notifications (id uuid PRIMARY KEY, recipient_id uuid NOT NULL REFERENCES users(id) ON DELETE RESTRICT, event_id uuid NOT NULL REFERENCES audit_events(id) ON DELETE RESTRICT, title text NOT NULL, safe_body text NOT NULL, created_at timestamptz NOT NULL DEFAULT now(), read_at timestamptz, UNIQUE(event_id,recipient_id));
CREATE TABLE sessions (id uuid PRIMARY KEY, user_id uuid NOT NULL REFERENCES users(id) ON DELETE RESTRICT, token_hash text NOT NULL UNIQUE, encrypted_provider_tokens bytea NOT NULL, csrf_hash text NOT NULL, aal smallint NOT NULL CHECK(aal IN (1,2)), expires_at timestamptz NOT NULL, last_seen_at timestamptz NOT NULL, revoked_at timestamptz);
CREATE OR REPLACE FUNCTION reject_audit_mutation() RETURNS trigger LANGUAGE plpgsql AS $$ BEGIN RAISE EXCEPTION 'audit_events append-only'; END; $$;
CREATE TRIGGER audit_no_row_mutation BEFORE UPDATE OR DELETE ON audit_events FOR EACH ROW EXECUTE FUNCTION reject_audit_mutation();
CREATE TRIGGER audit_no_truncate BEFORE TRUNCATE ON audit_events FOR EACH STATEMENT EXECUTE FUNCTION reject_audit_mutation();
-- Provision bt_runtime as NON-owner, NOINHERIT/no role escalation; separate migration owner. Configure via deployment admin.
REVOKE UPDATE, DELETE, TRUNCATE ON audit_events FROM PUBLIC;
-- After provisioning bt_runtime: REVOKE ALL ON audit_events FROM bt_runtime; GRANT SELECT, INSERT ON audit_events TO bt_runtime;
-- SELECT only via scoped query/service; DB owner/superuser remain trusted, not application credentials.
COMMIT;
