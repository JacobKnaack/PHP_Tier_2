<?php
declare(strict_types=1);
namespace Jacobk\PhpTier2\Repositories;

class SupabaseLinkRepository implements LinkRepositoryInterface
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
    }

    private function request(string $method, string $path, array $body = null): array
    {
        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => [
                    "apikey: {$this->apiKey}",
                    "Authorization: Bearer {$this->apiKey}",
                    "Content-Type: application/json",
                ],
                'ignore_errors' => true
            ]
        ];

        if ($body !== null) {
            $opts['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($opts);
        $response = file_get_contents("{$this->baseUrl}/$path", false, $context);

        return json_decode($response, true) ?? [];
    }

    public function all(): array
    {
        return $this->request('GET', 'links?order=created_at.desc');
    }

    public function find(string $id): ?array
    {
        $rows = $this->request('GET', "links?id=eq.$id");
        return $rows[0] ?? null;
    }

    public function add(string $url, array $metadata): array
    {
        $record = [
            'url'        => $url,
            'title'      => $metadata['title'] ?? $url,
            'favicon'    => $metadata['favicon'] ?? null,
            'domain'     => $metadata['domain'] ?? null,
            'read'       => false,
            'tags'       => [],
        ];

        $rows = $this->request('POST', 'links', $record);
        return $rows[0] ?? $record;
    }

    public function markRead(string $id): void
    {
        $this->request('PATCH', "links?id=eq.$id", ['read' => true]);
    }

    public function delete(string $id): void
    {
        $this->request('DELETE', "links?id=eq.$id");
    }

    public function search(string $term): array
    {
        $term = strtolower($term);
        return $this->request('GET', "links?or=(title.ilike.%$term%,url.ilike.%$term%,domain.ilike.%$term%)");
    }
}
