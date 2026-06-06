<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Repositories;

class JsonShortLinkEventRepository implements ShortLinkEventRepositoryInterface
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function log(string $shortlinkId, array $data): array
    {
        $events = $this->load();

        $record = [
            'id'            => uniqid('', true),
            'shortlink_id'  => $shortlinkId,
            'timestamp'     => date('c'),
            'ip_hash'       => $data['ip_hash'] ?? null,
            'referrer'      => $data['referrer'] ?? null,
            'user_agent'    => $data['user_agent'] ?? null,
        ];

        $events[] = $record;
        $this->save($events);

        return $record;
    }

    public function getByShortlink(string $shortlinkId): array
    {
        return array_values(array_filter(
            $this->load(),
            fn($e) => $e['shortlink_id'] === $shortlinkId
        ));
    }

    public function deleteByShortlink(string $shortlinkId): void
    {
        $events = array_filter(
            $this->load(),
            fn($e) => $e['shortlink_id'] !== $shortlinkId
        );

        $this->save(array_values($events));
    }

    private function load(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    private function save(array $events): void
    {
        file_put_contents(
            $this->file,
            json_encode($events, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
