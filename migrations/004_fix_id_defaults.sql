alter table public.links
    alter column id set default gen_random_uuid();

alter table public.events
    alter column id set default gen_random_uuid();

alter table public.shortlinks
    alter column id set default gen_random_uuid();
