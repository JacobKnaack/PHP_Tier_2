create or replace function public.run_sql(sql text)
returns void
language plpgsql
security definer
as $$
begin
    execute sql;
end;
$$;

-- Allow service role to call it
grant execute on function public.run_sql(text) to service_role;
