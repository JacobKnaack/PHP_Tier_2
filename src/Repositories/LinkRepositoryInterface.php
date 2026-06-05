<?php
declare(strict_types=1);
namespace JacobK\PhpTier2\Repositories;

interface LinkRepositoryInterface
{
    public function all(): array;
    public function find(string $id): ?array;
    public function add(string $url, array $metadata): array;
    public function markRead(string $id): void;
    public function search(string $term): array;
    public function delete(string $id): void;
}
