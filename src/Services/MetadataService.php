<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Services;

class MetadataService
{
    /**
     * Fetch all metadata for a URL
     */
    public function fetch(string $url): array
    {
        $html = $this->fetchHtml($url);

        return [
            'title'   => $this->extractTitle($html) ?? $url,
            'favicon' => $this->extractFavicon($html, $url),
            'domain'  => $this->extractDomain($url)
        ];
    }

    /**
     * Fetch raw HTML from a URL
     */
    private function fetchHtml(string $url): string
    {
        // Basic timeout + user agent to avoid blocks
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'header'  => "User-Agent: LinkInboxBot/1.0\r\n"
            ]
        ]);

        $html = @file_get_contents($url, false, $context);

        return $html ?: '';
    }

    /**
     * Extract <title>...</title>
     */
    private function extractTitle(string $html): ?string
    {
        if (!$html) return null;

        if (preg_match('/<title>(.*?)<\/title>/si', $html, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extract favicon from <link rel="icon"> or fallback to /favicon.ico
     */
    private function extractFavicon(string $html, string $url): string
    {
        $domain = $this->extractDomain($url);
        $base   = $this->extractBaseUrl($url);

        // Try to find <link rel="icon" ...>
        if ($html && preg_match('/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+>/i', $html, $match)) {
            if (preg_match('/href=["\']([^"\']+)["\']/', $match[0], $hrefMatch)) {
                $href = $hrefMatch[1];

                // Absolute URL
                if (str_starts_with($href, 'http')) {
                    return $href;
                }

                // Root-relative
                if (str_starts_with($href, '/')) {
                    return $base . $href;
                }

                // Relative path
                return $base . '/' . ltrim($href, '/');
            }
        }

        // Fallback: /favicon.ico
        return $base . '/favicon.ico';
    }

    /**
     * Extract domain from URL
     */
    private function extractDomain(string $url): string
    {
        return parse_url($url, PHP_URL_HOST) ?? '';
    }

    /**
     * Extract base URL (scheme + host)
     */
    private function extractBaseUrl(string $url): string
    {
        $parts = parse_url($url);

        $scheme = $parts['scheme'] ?? 'https';
        $host   = $parts['host'] ?? '';

        return $scheme . '://' . $host;
    }
}
