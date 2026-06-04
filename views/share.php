<?php
/** @var array $shortlink */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$shortUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/s/' . $shortlink['code'];
?>

<?php include __DIR__ . '/layout/inbox.php'; ?>

<div class="share-container">
    <h1>Short Link Created</h1>
    
    <div class="shortlink-success">
        <p>Your short link is ready:</p>
    </div>
    
    <div class="shortlink-box">
        <input type="text" value="<?= htmlspecialchars($shortUrl) ?>" readonly>
        <button onclick="copyShortlink()">Copy</button>
    </div>
    
    <div class="share-page-links">
        <a href="/links/<?= $shortlink['link_id'] ?>/stats">View Analytics</a> |
        <a href="/inbox-list">Back to Inbox</a>
    </div>
</div>

<script>
async function copyShortlink() {
    const input = document.querySelector('.shortlink-box input');
    await navigator.clipboard.writeText(input.value);
    alert('Copied to clipboard');
}
</script>

<style>
.shortlink-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px 0;
}

.shortlink-box input {
    flex: 1;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
}

.shortlink-box button {
    padding: 10px 14px;
    font-size: 0.9rem;
    background: #4a90e2;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.shortlink-box button:hover {
    background: #357ac8;
}

.share-page-links {
    margin-top: 20px;
    font-size: 0.95rem;
}

.share-page-links a {
    color: #4a90e2;
    text-decoration: none;
}

.share-page-links a:hover {
    text-decoration: underline;
}
</style>
