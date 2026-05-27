<?php
declare(strict_types=1);

namespace JacobK\PhpTier2\Services;

class ShortLinkService
{
    private string $shortlinksFile;
    private string $eventsFile;

    public function __construct(string $shortlinksFile, string $eventsFile)
    {
        $this->shortlinksFile = $shortlinksFile;
        $this->eventsFile = $eventsFile;

        if (!file_exists($this->shortlinksFile)) {
            file_put_contents($this->shortlinksFile, json_encode([]));
        }
        if (!file_exists($this->eventsFile)) {
            file_put_contents($this->eventsFile, json_encode([]));
        }
    }

    // Generate a short base62 code
    public function generateCode(int $length = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }

    // Create a short link record for a given URL and link ID
    public function create(string $url, string $linkId): array
    {
        $shortlinks = $this->loadShortLinks();

        do {
            $code = $this->generateCode();
        } while ($this->codeExists($code, $shortlinks));

        $record = [
            'id' => uniqid('', true),
            'code' => $code,
            'link_id' => $linkId,
            'target_url' => $url,
            'created_at' => date('c'),
        ];

        $shortlinks[] = $record;
        $this->saveShortLinks($shortlinks);

        return $record;
    }

    // Resolves a short code to its corresponding record, or returns null if not found
    public function resolve(string $code): ?array
    {
        foreach ($this->loadShortLinks() as $link) {
            if ($link['code'] === $code) {
                return $link;
            }
        }

        return null;
    }

    // Log analytics event for a short link
    public function logEvent(string $shorlinkId, array $data): void
    {
        $events = $this->loadEvents();
        $events[] = [
            'id' => uniqid('', true),
            'shortlink_id' => $shorlinkId,
            'timestamp' => date('c'),
            'ip_hash' => $data['ip_hash'] ?? null,
            'referrer' => $data['referrer'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ];
        $this->saveEvents($events);
    }

    // Get analytics for a shortlink
    public function getEvents(string $shortlinkId): array
    {
        return array_values(array_filter(
            $this->loadEvents(),
            fn($e) => $e['shortlink_id'] === $shortlinkId
        ));
    }

    private function codeExists(string $code, array $shortlinks): bool
    {
        foreach ($shortlinks as $link) {
            if ($link['code'] === $code) {
                return true;
            }
        }
        return false;
    }

    private function loadShortLinks(): array
    {
        $data = file_get_contents($this->shortlinksFile);
        return json_decode($data, true) ?? [];
    }

    private function saveShortLinks(array $data): void
    {
        file_put_contents(
            $this->shortlinksFile,
            json_encode($data, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    private function loadEvents(): array
    {
        return json_decode(file_get_contents($this->eventsFile), true) ?? [];
    }

    private function saveEvents(array $data): void
    {
        file_put_contents(
            $this->eventsFile,
            json_encode($data, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
