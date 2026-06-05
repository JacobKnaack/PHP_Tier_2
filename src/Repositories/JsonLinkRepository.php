<?php
declare(strict_types=1);
namespace Jacobk\PhpTier2\Repositories;

class JsonLinkRepository implements LinkRepositoryInterface
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;

        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }
    }

    public function all(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $link) {
            if ($link['id'] === $id) {
                return $link;
            }
        }
        return null;
    }

    public function add(string $url, array $metadata): array
    {
        $links = $this->all();

        $record = [
            'id'         => uniqid('', true),
            'url'        => $url,
            'title'      => $metadata['title'] ?? $url,
            'favicon'    => $metadata['favicon'] ?? null,
            'domain'     => $metadata['domain'] ?? null,
            'created_at' => date('c'),
            'read'       => false,
            'tags'       => []
        ];

        $links[] = $record;

        file_put_contents($this->file, json_encode($links, JSON_PRETTY_PRINT));

        return $record;
    }

    public function markRead(string $id): void
    {
        $links = $this->all();

        foreach ($links as &$link) {
            if ($link['id'] === $id) {
                $link['read'] = true;
            }
        }

        file_put_contents($this->file, json_encode($links, JSON_PRETTY_PRINT));
    }

    public function delete(string $id): void
    {
        $links = array_filter($this->all(), fn($l) => $l['id'] !== $id);
        file_put_contents($this->file, json_encode(array_values($links), JSON_PRETTY_PRINT));
    }

    public function search(string $term): array
    {
        $term = strtolower($term);

        return array_values(array_filter($this->all(), function ($link) use ($term) {
            return str_contains(strtolower($link['title'] ?? ''), $term)
                || str_contains(strtolower($link['url'] ?? ''), $term)
                || str_contains(strtolower($link['domain'] ?? ''), $term);
        }));
    }
}
