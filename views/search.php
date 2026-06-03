
<?php
/** @var array $links */
include __DIR__ . './layout/inbox.php';
?>

<style>
    <?php include __DIR__ . '/../public/assets/styles/inbox-list.css'; ?>
</style>

<div class="inbox-search-container">
    <h1>Search Results</h1>
    <p>Showing results for: <strong><?= htmlspecialchars($_GET['q'] ?? '') ?></strong></p>

    <ul class="link-list">
        <?php foreach ($links as $link): ?>
            <?php include __DIR__ . '/partials/link-item.php'; ?>
        <?php endforeach; ?>
    </ul>

    <?php if (empty($links)): ?>
        <p class="empty">No results found</p>
    <?php endif; ?>

    <p><a href="/inbox-list">Back to Inbox</a></p>
</div>