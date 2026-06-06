<?php
declare(strict_types=1);

use Jacobk\PhpTier2\Repositories\JsonLinkRepository;
use Jacobk\PhpTier2\Repositories\JsonShortLinkRepository;
use Jacobk\PhpTier2\Repositories\JsonShortLinkEventRepository;

use Jacobk\PhpTier2\Repositories\SupabaseLinkRepository;
// use Jacobk\PhpTier2\Repositories\SupabaseShortLinkRepository;
// use Jacobk\PhpTier2\Repositories\SupabaseShortLinkEventRepository;

use Jacobk\PhpTier2\Services\LinkService;
use Jacobk\PhpTier2\Services\ShortLinkService;
use Jacobk\PhpTier2\Services\EventService;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$environment = $_ENV['APP_ENV'] ?? 'development';

switch ($environment) {
    case 'production':
        // Supabase Repositories
        $supabaseUrl = $_ENV['SUPABASE_URL'];
        $supabaseKey = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];

        $linkRepo       = new SupabaseLinkRepository($supabaseUrl, $supabaseKey);
        // $shortlinkRepo  = new SupabaseShortLinkRepository($supabaseUrl, $supabaseKey);
        // $eventRepo      = new SupabaseShortLinkEventRepository($supabaseUrl, $supabaseKey);
        break;
    case 'development':
    default:
        $linkRepo       = new JsonLinkRepository(__DIR__ . '/data/links.json');
        $shortlinkRepo  = new JsonShortLinkRepository(__DIR__ . '/data/shortlinks.json');
        $eventRepo      = new JsonShortLinkEventRepository(__DIR__ . '/data/events.json');
        break;
}

$linkService = new LinkService($linkRepo);
$shortLinkService = new ShortLinkService($shortlinkRepo, $eventRepo);
$eventService     = new EventService($eventRepo);

$GLOBALS['linkService'] = $linkService;
$GLOBALS['shortLinkService'] = $shortLinkService;
$GLOBALS['eventService'] = $eventService;