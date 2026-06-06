create table if not exists events (
  id          text primary key,
  type        text not null,
  link_id     text references links(id) on delete cascade,
  created_at  timestamptz default now(),
  metadata    jsonb default '{}'::jsonb
);

create index if not exists events_type_idx on events (type);
create index if not exists events_link_idx on events (link_id);
