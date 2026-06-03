<?php
/** @var array $links */

// $links is provided by LinkController@index()
$unread = array_filter($links, fn($l) => !$l['read']);
$read   = array_filter($links, fn($l) =>  $l['read']);
?>

<?php include __DIR__ . '/layout/inbox.php'; ?>
<style>
    <?php include __DIR__ . '/../public/assets/styles/inbox-list.css'; ?>
</style>

<div class="inbox-container">

    <h1>Personal Link Inbox</h1>

    <!-- Add Link Form -->
    <form class="add-link-form" action="/links" method="POST">
        <input 
            type="url" 
            name="url" 
            placeholder="Paste a link…" 
            required
        >
        <button type="submit">Save</button>
    </form>

    <!-- Search -->
    <form class="search-form" action="/search" method="GET">
        <input 
            type="text" 
            name="q" 
            placeholder="Search links…"
        >
        <button type="submit">Search</button>
    </form>

    <!-- Unread Links -->
    <section class="link-section">
        <h2>Inbox</h2>

        <?php if (empty($unread)): ?>
            <p class="empty">No unread links</p>
        <?php else: ?>
            <ul class="link-list">
                <?php foreach ($unread as $link): ?>
                    <?php include __DIR__ . '/partials/link-item.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Read Links -->
    <section class="link-section">
        <h2>Archive</h2>

        <?php if (empty($read)): ?>
            <p class="empty">No archived links</p>
        <?php else: ?>
            <ul class="link-list archived">
                <?php foreach ($read as $link): ?>
                    <?php include __DIR__ . '/partials/link-item.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

</div>
