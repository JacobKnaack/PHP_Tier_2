create table if not exists shortlinks (
  id          text primary key,
  code        text unique not null,
  url         text not null,
  created_at  timestamptz default now(),
  metadata    jsonb default '{}'::jsonb
);

create index if not exists shortlinks_code_idx on shortlinks (code);
