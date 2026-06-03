<?php
/** @var array $events */
/** @var array $shortlink */
/** @var array $link */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$shortUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/s/' . $shortlink['code'];
?>

<h1>Short Link Analytics</h1>

<p>
    <strong>Short URL:</strong>
    <?= htmlspecialchars($shortUrl) ?>
</p>

<p>
    <strong>Original URL:</strong>
    <?= htmlspecialchars($link['url']) ?>
</p>

<h2>Events</h2>

<?php if (empty($events)): ?>
    <p>No clicks yet.</p>
<?php else: ?>
    <table class="analytics-table">
        <thead>
            <tr>
                <th>Time</th>
                <th>Referrer</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['timestamp']) ?></td>
                    <td><?= htmlspecialchars($event['referrer'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($event['user_agent'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="/inbox-list">Back to Inbox</a>
</p>

<style>
.analytics-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

.analytics-table th,
.analytics-table td {
    border: 1px solid #ddd;
    padding: 8px;
}

.analytics-table th {
    background: #f5f5f5;
}
</style>
