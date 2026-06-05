<?php

namespace Tests\Repositories;

use Jacobk\PhpTier2\Repositories\SupabaseLinkRepository;
use Jacobk\PhpTier2\Repositories\LinkRepositoryInterface;

class SupabaseLinkRepositoryTest extends LinkRepositoryContractTest
{
    protected function repository(): LinkRepositoryInterface
    {
        $url = getenv('SUPABASE_URL');
        $key = getenv('SUPABASE_KEY');

        if (!$url || !$key) {
            $this->markTestSkipped('Supabase credentials not set in environment variables.');
        }

        return new SupabaseLinkRepository($url, $key);
    }
}