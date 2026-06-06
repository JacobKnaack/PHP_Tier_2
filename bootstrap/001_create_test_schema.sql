-- Create the schema if it doesn't exist
create schema if not exists test;

-- Allow Supabase roles to see and use the schema
grant usage on schema test to anon, authenticated, service_role;

-- Allow Supabase roles to create objects in the schema
grant create on schema test to anon, authenticated, service_role;

-- Allow Supabase roles to read/write all existing tables in the schema
grant all on all tables in schema test to anon, authenticated, service_role;

-- Allow Supabase roles to use any sequences (UUID defaults, serials, etc.)
grant all on all sequences in schema test to anon, authenticated, service_role;

-- Allow Supabase roles to call any routines (RPC functions) in the schema
grant all on all routines in schema test to anon, authenticated, service_role;

-- Ensure future tables inherit the correct privileges
alter default privileges for role postgres in schema test
  grant all on tables to anon, authenticated, service_role;

alter default privileges for role postgres in schema test
  grant all on routines to anon, authenticated, service_role;

alter default privileges for role postgres in schema test
  grant all on sequences to anon, authenticated, service_role;

