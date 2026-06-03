<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Jacobk\PhpTier2\Calculator;
use Jacobk\PhpTier2\Router;
use Jacobk\PhpTier2\Controllers\LinkController;
use Jacobk\PhpTier2\Controllers\ShortLinkController;

$router = new Router();
$parser = new Parsedown();
$linkController = new LinkController();
$shortLinkController = new ShortLinkController();

$router->get('/', function() use ($parser) {
    $readme = file_get_contents(__DIR__ . '/../README.md');
    $html = $parser->text(htmlspecialchars($readme));
    include __DIR__ . '/../views/readme.php';
});

$router->get('/calculator', function() {
    echo file_get_contents(__DIR__ . '/assets/html/calculator.html');
});

$router->get('/inbox', function() {
    echo file_get_contents(__DIR__ . '/assets/html/inbox.html');
});
$router->get('/inbox-list', [$linkController, 'index']);
$router->post('/links', [$linkController, 'store']);
$router->post('/links/{id}/read', [$linkController, 'markRead']);
$router->delete('/links/{id}', [$linkController, 'destroy']);
$router->get('/search', [$linkController, 'search']);

$router->get('/links/{id}/share', [$shortLinkController, 'create']);
$router->get('/s/{code}', [$shortLinkController, 'redirect']);
$router->get('/links/{id}/stats', [$shortLinkController, 'stats']);

$router->post('/api/expression', function() {
    $calculator = new Calculator();
    $expression = $_POST['expression'] ?? '';
    if ($expression == '') {
        http_response_code(400);
        echo json_encode(['error' => 'No expression provided']);
        return;
    }
    $result = $calculator->evaluate($expression);
    echo json_encode(['result' => $result]);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
