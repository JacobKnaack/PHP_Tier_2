<?php

namespace Tests\Repositories;

use Jacobk\PhpTier2\Repositories\SupabaseLinkRepository;
use Jacobk\PhpTier2\Repositories\LinkRepositoryInterface;

class SupabaseLinkRepositoryTest extends LinkRepositoryContractTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $url = $_ENV['SUPABASE_URL'];
        $key = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];
        $schema = $_ENV['SUPABASE_SCHEMA'];
    
        if ($url && $key && $schema) {
            $repo = new SupabaseLinkRepository($url, $key, $schema);
            $repo->rawSql("truncate table {$schema}.links restart identity cascade");
        }
    }

    protected function repository(): LinkRepositoryInterface
    {
        $url = $_ENV['SUPABASE_URL'];
        $key = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];
        $schema = $_ENV['SUPABASE_SCHEMA'];

        if (!$url || !$key || !$schema) {
            $this->markTestSkipped('Supabase environment variables not set in test environment.');
        }

        return new SupabaseLinkRepository($url, $key, $schema);
    }
}