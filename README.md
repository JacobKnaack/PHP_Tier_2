# PHP Tier 2

Focuses on server side concepts.  Serving forms and handles simple web requests.

## Using Supabase

Before running migrations for the first time, execute:

  bootstrap/000_create_sql_executor.sql

in the Supabase SQL Editor.

This creates the run_sql() RPC function used by the migration runner.

## Usage

Set Environment Variables

```env
APP_ENV=<development|test|production>
SUPABASE_URL=<https://<app-id>.supabase.co/rest/v1/
SUPABASE_SERVICE_ROLE_KEY=<api-key>
```

Start the Server

```bash
php -S localhost:PORT -t public/
```

Or use composer

```bash
composer start
```

## Pages

- [Calculator](/calculator)
- [Link Inbox](/inbox)
