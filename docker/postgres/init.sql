-- Initialize schemas
CREATE SCHEMA IF NOT EXISTS medify;
CREATE SCHEMA IF NOT EXISTS medify_chat;

-- Initialize chatbot read-only roles
DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'chat_admin_ro') THEN
        CREATE ROLE chat_admin_ro WITH LOGIN PASSWORD 'postgres';
    END IF;
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'chat_vendor_ro') THEN
        CREATE ROLE chat_vendor_ro WITH LOGIN PASSWORD 'postgres';
    END IF;
END
$$;

GRANT USAGE ON SCHEMA medify TO chat_admin_ro, chat_vendor_ro;
GRANT USAGE ON SCHEMA medify_chat TO chat_admin_ro, chat_vendor_ro;
