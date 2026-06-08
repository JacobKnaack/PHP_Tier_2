<?php
declare(strict_types=1);
namespace Jacobk\PhpTier2\Repositories;

class SupabaseLinkRepository implements LinkRepositoryInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $schema;

    public function __construct(string $baseUrl, string $apiKey, string $schema = 'public')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->schema  = $schema;
    }

    private function request(string $method, string $path, array | null $body = null): array
    {
        $headers = [
            "apikey: {$this->apiKey}",
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
            "Accept-Profile: {$this->schema}",
            "Content-Profile: {$this->schema}"
        ];

        // Required for INSERT/UPDATE to return rows
        if ($method === 'POST' || $method === 'PATCH') {
            $headers[] = "Prefer: return=representation";
        }

        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => $headers,
                'ignore_errors' => true
            ]
        ];

        if ($body !== null) {
            $opts['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($opts);
        $url = "{$this->baseUrl}/{$path}";
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true) ?? [];
    }

    public function all(): array
    {
        return $this->request('GET', "links?order=created_at.desc");
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

        $rows = $this->request('POST', "links", $record);

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
        $filter = rawurlencode(
            "title.ilike.*{$term}*,url.ilike.*{$term}*"
        );

        $rows = $this->request('GET', "links?or=($filter)");

        return is_array($rows) ? array_values($rows) : [];
    }

    public function rawSql(string $sql): void
    {
        $headers = [
            "apikey: {$this->apiKey}",
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
        ];
        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => $headers,
                'content' => json_encode(['sql' => $sql]),
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($opts);
        file_get_contents("{$this->baseUrl}/rpc/run_sql", false, $context);
    }
}
