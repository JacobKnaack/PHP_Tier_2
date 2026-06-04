<?php
/** @var array $events */
/** @var array $shortlink */
/** @var array $link */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$shortUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/s/' . $shortlink['code'];
?>

<?php include __DIR__ . '/layout/inbox.php'; ?>

<div class="stats-container">
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
</div>


<style>
/* ============================
   STATS PAGE LAYOUT
   ============================ */

.stats-container {
    background: #fff;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    margin-top: 20px;
}

.stats-container h1 {
    margin-bottom: 12px;
    font-size: 1.6rem;
    color: #222;
}

.stats-container p {
    margin-bottom: 10px;
    line-height: 1.5;
}

/* ============================
   ANALYTICS TABLE
   ============================ */

.analytics-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 0.95rem;
}

.analytics-table th,
.analytics-table td {
    border: 1px solid #e0e0e0;
    padding: 10px;
    text-align: left;
}

.analytics-table th {
    background: #f5f5f5;
    font-weight: 600;
}

.analytics-table tr:nth-child(even) {
    background: #fafafa;
}

.analytics-table tr:hover {
    background: #f0f8ff;
}

/* ============================
   LINKS
   ============================ */

.stats-container a {
    color: #4a90e2;
    text-decoration: none;
}

.stats-container a:hover {
    text-decoration: underline;
}
</style>
