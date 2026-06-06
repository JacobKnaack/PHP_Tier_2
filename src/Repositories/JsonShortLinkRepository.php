<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Repositories;

class JsonShortLinkRepository implements ShortLinkRepositoryInterface
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function all(): array
    {
        return $this->load();
    }

    public function create(string $url, string $linkId, string $code): array
    {
        $records = $this->load();

        $record = [
            'id'         => uniqid('', true),
            'code'       => $code,
            'link_id'    => $linkId,
            'target_url' => $url,
            'created_at' => date('c'),
        ];

        $records[] = $record;
        $this->save($records);

        return $record;
    }

    public function findByCode(string $code): ?array
    {
        foreach ($this->load() as $record) {
            if ($record['code'] === $code) {
                return $record;
            }
        }
        return null;
    }

    public function findByLinkId(string $linkId): ?array
    {
        foreach ($this->load() as $record) {
            if ($record['link_id'] === $linkId) {
                return $record;
            }
        }
        return null;
    }

    public function delete(string $shortlinkId): void
    {
        $records = array_filter(
            $this->load(),
            fn($r) => $r['id'] !== $shortlinkId
        );

        $this->save(array_values($records));
    }

    public function deleteByLinkId(string $linkId): void
    {
        $records = array_filter(
            $this->load(),
            fn($r) => $r['link_id'] !== $linkId
        );

        $this->save(array_values($records));
    }

    private function load(): array
    {
        $data = file_get_contents($this->file);
        return json_decode($data, true) ?? [];
    }

    private function save(array $records): void
    {
        file_put_contents(
            $this->file,
            json_encode($records, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
