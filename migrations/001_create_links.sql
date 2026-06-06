create table if not exists links (
  id          text primary key,
  url         text not null,
  title       text,
  favicon     text,
  domain      text,
  created_at  timestamptz default now(),
  read        boolean default false,
  tags        jsonb default '[]'::jsonb
);

create index if not exists links_domain_idx on links (domain);
create index if not exists links_title_idx on links using gin (to_tsvector('english', title));
create index if not exists links_url_idx on links using gin (to_tsvector('english', url));
